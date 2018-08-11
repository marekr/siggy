<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

use App\Facades\Auth;
use Siggy\User;
use \Group;
use \GroupMember;
use \CharacterGroup;
use Siggy\Redis\RedisTtlCounter;

class UserSession {

	public $id = "";
	public $user_id = 0;
	public $group_id = 0;
	public $corporation_id = 0;

	public $character_id = 0;
	public $character_name = "";

	public $group = null;
	public $accessData = array();

	private $accessibleGroups = null;
	private $accessibleChainMaps = null;

	public function __construct()
	{
		//try to load the session data
		//session data fetch failed? then recreate it, or create one if no id
		if( !$this->reloadSessionData() )
		{
			$this->populateNewSession();
			$this->reloadUserSession();
		}

		$this->group = Group::find($this->group_id);
		$this->getAccessData();
		$this->updateSession();
	}


	private function reloadSessionData($two = false): bool
	{
		if(session('init', false) == true && session('type', 'guest') == 'user')
		{
			$this->group_id = session('group_id', -2);

			$this->character_id = session('character_id',0);
			if($this->character_id)
			{
				$data = \Character::find($this->character_id);
				if($data != null)
				{
					$this->character_name = $data->name;
					$this->corporation_id = $data->corporation_id;
				}
			}

			$this->user_id = session('user_id');

			return TRUE;
		}
		
		return FALSE;
	}

	public function destroy()
	{
		session()->flush();
	}

	private function __generateSessionID(): string
	{
		$sessionID = md5(uniqid(microtime()) . Request::ip() . Request::header('User-Agent'));
		return $sessionID;
	}

	private function __generateCSRF(): string
	{
		$sessionID = sha1(uniqid(microtime()) . Request::ip() . Request::header('User-Agent'));
		return $sessionID;
	}

	public function reloadUserSession()
	{
		if( !Auth::check() )
		{
			return;
		}

		$update = ['user_id' => Auth::user()->id,
					'group_id' => Auth::user()->groupID,
					'character_id' => Auth::user()->char_id,
					'type' => 'user'
				];
		session($update);

		if(!empty(Auth::user()->char_id) &&
			!empty(Auth::user()->groupID) )
		{
			$chargroup = CharacterGroup::find(Auth::user()->char_id,Auth::user()->groupID);
			if($chargroup == null)
			{
				$chargroup = CharacterGroup::create(['character_id' => Auth::user()->char_id,'group_id' => Auth::user()->groupID]);
			}
			$chargroup->updateGroupAccess();
		}

		$this->reloadSessionData(true);

		$this->getAccessData();

		
		$ttlcUsers = new RedisTtlCounter('ttlc:users:daily', 86400);
		$ttlcUsers->add(Auth::user()->id);
	}

	private function populateNewSession(): bool
	{
		// so we must update it too
		$insert = [ 
					'character_id' => (isset(Auth::user()->character_id) ? Auth::user()->character_id : 0 ),
					'created_at' => Carbon::now()->toDateTimeString(),
					'ip_address' => Request::ip(),
					'user_agent' => Request::header('User-Agent'),
					'type' => $this->determineSessionType(),
					'user_id' => ( isset(Auth::user()->id) ? Auth::user()->id : 0 ),
					'group_id' => ( isset(Auth::user()->groupID) ? Auth::user()->groupID : 0 ),
					'init' => true
				];

		session($insert);

		return TRUE;
	}

	private function determineSessionType(): string
	{
		$type = 'guest';

		if( Auth::check() )
		{
			$type = 'user';
		}

		return $type;
	}

	private function updateSession()
	{
		$type = $this->determineSessionType();

		/* Shitty fix, always update groupID because we don't on creaton have a valid one */
		$update = 	['updated_at' => Carbon::now()->toDateTimeString(),
						 'group_id' => $this->group_id,
						 'type' => $type,
						 'chainmap_id' => ( isset($this->accessData['active_chain_map']) ? $this->accessData['active_chain_map'] : 0 )
		];


		session($update);
	}

	public function validateGroup(): bool
	{
		if( $this->group->findGroupMember(GroupMember::TypeChar, $this->character_id) != null )
		{
			return TRUE;
		}
		
		if( $this->group->findGroupMember(GroupMember::TypeCorp, $this->corporation_id) != null )
		{
			return TRUE;
		}

		if( $this->group != null )
		{
			$this->group = null;
			$this->group_id = 0;

			$this->updateSession();
		}

		return FALSE;
	}

	public function getAccessData(): array
	{
		/* Result array */
		$accessData = [];

		//store the possible groups
		if($this->group != null)
		{
			$accessData['active_chain_map'] = $this->_getChainMapID($this->group->chainMaps());
		}

		$this->accessData = $accessData;

		return $accessData;
	}


	public function accessibleGroups()
	{
		if($this->accessibleGroups == null)
		{
			//start forming a list of possible groups
			$all_groups = [];
			$corp_data = Group::findAllByGroupMembership('corp', $this->corporation_id);
			$char_data = Group::findAllByGroupMembership('char', $this->character_id);

			$access_type = 'char';
			if( count($corp_data) && count($char_data) !== FALSE )
			{
				$all_groups = array_replace($corp_data,$char_data);
			}
			else if( count($corp_data) )
			{
				$all_groups = $corp_data;
			}
			else if (count($char_data) )
			{
				$all_groups = $char_data;
			}
			$this->accessibleGroups = $all_groups;
		}

		return $this->accessibleGroups;
	}

	public function accessibleChainMaps()
	{
		if($this->accessibleChainMaps == null)
		{
			$this->accessibleChainMaps  = $this->_buildAccessChainmaps($this->group->chainMaps());
		}

		return $this->accessibleChainMaps;
	}

	private function _buildAccessChainmaps(array $chainmaps)
	{
		$accessibleChainmaps = [];
		foreach($chainmaps as $id => $c)
		{
			foreach($c->access as $p)
			{
				if( ($p->memberType == 'corp' && $p->eveID == $this->corporation_id)
						|| ($p->memberType == 'char' && $p->eveID == $this->character_id) )
				{
					$accessibleChainmaps[$c->id] = $c;
				}
			}
		}
		return $accessibleChainmaps;
	}

	private function _getDefaultChainMapID(array $chainmaps): int
	{
		//to make usage "neat" for now, we first see if we have access to a default chain map
		foreach($chainmaps as $id => $c)
		{
			foreach($c->access as $p)
			{
				if( $c->default && ( ($p->memberType == 'corp' && $p->eveID == $this->corporation_id)
														|| ($p->memberType == 'char' && $p->eveID == $this->character_id) ) )
				{
					return $c->id;
				}
			}
		}

		//otherwise grab the first one we do have access to
		foreach($chainmaps as $id => $c)
		{
			foreach($c->access as $p)
			{
				if( ($p->memberType == 'corp' && $p->eveID == $this->corporation_id)
						|| ($p->memberType == 'char' && $p->eveID == $this->character_id) )
				{
					return $c->id;
				}
			}
		}

		return 0;
	}

	public function getCorporationId(): int
	{
		return $this->corporation_id;
	}

	public function getCharacterId(): int
	{
		return $this->character_id;
	}

	public function getGroup(): ?Group
	{
		return $this->group;
	}

	public function getCharacterName(): string
	{
		return $this->character_name;
	}

	private function _getChainMapID(array $chainmaps): int
	{
		$desired_id = intval(Cookie::get('chainmap', 0));
		$default_id = 0;
		if( !$desired_id )
		{
			return $this->_getDefaultChainMapID($chainmaps);
		}

		if( isset($chainmaps[ $desired_id ]) )
		{
			foreach($chainmaps[ $desired_id ]->access as $p)
			{
				if( ($p->memberType == 'corp' && $p->eveID == $this->corporation_id)
						|| ($p->memberType == 'char' && $p->eveID == $this->character_id) )
				{
					return $desired_id;
				}
			}
		}
		else
		{
			$desired_id = $this->_getDefaultChainMapID($chainmaps);
			if( $desired_id )
			{
				Cookie::queue('chainmaps',$desired_id);
			}

			return $desired_id;
		}

		return $this->_getDefaultChainMapID($chainmaps);
	}
}
