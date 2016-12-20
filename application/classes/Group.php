<?php

use Carbon\Carbon;
use Pheal\Pheal;

class Group {
	public $id;
	public $name;
	public $isk_balance;
	public $created_at;
	public $updated_at;

	public $groupMembers = null;
	public $chainMaps = null;

	public $blacklistCharacters = null;

	//todo, implement
	public $cache_time = 1;

	public function __construct(array $props)
	{
		foreach ($props as $key => $value) 
		{
    		$this->$key = $value;
		}
	}
	
	public function save(array $props)
	{
		//todo, remove this compatibility hack
		$props['last_update'] = time();

		foreach ($props as $key => $value) 
		{
    		$this->$key = $value;
		}

		DB::update('groups')
			->set( $props )
			->where('id', '=',  $this->id)
			->execute();
	}
	
	private static function hashGroupPassword(string $password, string $salt): string
	{
		return sha1($password . $salt);
	}

	public static function createFancy(array $data): int
	{
		$salt = miscUtils::generateSalt(10);
		$password = "";
		if( $data['group_password'] != "" && $data['group_password_required'] == 1 )
		{
			$password = self::hashGroupPassword( $data['group_password'], $salt );
		}

		$insert = array(
							'name' => $data['groupName'],
							'ticker' => $data['groupTicker'],
							'password_required' => $data['group_password_required'],
							'password_salt' => $salt,
							'password' => $password,
							'dateCreated' => time(),
							'paymentCode' => miscUtils::generateString(14),
							'billable' => 1
						);
		$result = DB::insert('groups', array_keys($insert) )->values( array_values($insert) )->execute();
		$result = $result[0];

		$insert = array( 'group_id' => $result,
						 'chainmap_type' => 'default',
						 'chainmap_name' => 'Default',
						 'chainmap_homesystems' => '',
						 'chainmap_homesystems_ids' => ''
						);
		DB::insert('chainmaps', array_keys($insert) )->values( array_values($insert) )->execute();

		return $result;
	}

	public static function create(array $props): Group
	{
		DB::insert('groups', array_keys($props) )
				->values(array_values($props))
				->execute();

		return new Group($props);
	}

	public static function find(int $id)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM groups WHERE id=:id')
												->param(':id', $id)
												->execute()
												->current();

		if($data != null)
		{
			return new Group($data);
		}

		return null;
	}
	
	public function groupMembers(): array
	{
		if($this->groupMembers == null)
		{
			$this->groupMembers = GroupMember::findByGroup($this->id);
		}

		return $this->groupMembers;
	}

	public function findGroupMember(string $type, int $id)
	{
		return GroupMember::findByGroupAndType($this->id, $type, $id);
	}

	public function blacklistCharacters(): array
	{
		if($this->blacklistCharacters == null)
		{
			$this->blacklistCharacters = GroupBlacklistCharacter::findByGroup($this->id);
		}

		return $this->blacklistCharacters;
	}

	public function chainMaps(): array
	{
		if($this->chainMaps == null)
		{
			$chainmaps = DB::query(Database::SELECT, "SELECT * FROM chainmaps WHERE group_id = :group")
								->param(':group', $this->id)
								->execute()
								->as_array('chainmap_id');

			foreach($chainmaps as &$c)
			{
				$members = DB::query(Database::SELECT, "SELECT gm.memberType, gm.eveID FROM groupmembers gm
													LEFT JOIN chainmaps_access a ON(gm.id=a.groupmember_id) WHERE chainmap_id=:chainmap")
							->param(':chainmap', $c['chainmap_id'])
							->execute()
							->as_array();
				$c['access'] = $members;
			}

			$this->chainMaps = $chainmaps;
		}

		return $this->chainMaps;
	}
	
	public function logAction( string $type, string $message )
	{
		$insert = array( 'groupID' => $this->id,
						 'type' => $type,
						 'message' => $message,
						 'entryTime' => time()
						);

		DB::insert('logs', array_keys($insert) )->values(array_values($insert))->execute();
	}

	public function getCharacterUsageCount(): int
	{
		$num_corps = DB::query(Database::SELECT, "SELECT SUM(DISTINCT c.member_count) as total FROM groupmembers gm
										LEFT JOIN corporations c ON(gm.eveID = c.id)
										WHERE gm.groupID=:group AND gm.memberType='corp'")
										->param(':group', $this->id)
										->execute()
										->current();

		$num_corps = $num_corps['total'];

		$num_chars = DB::query(Database::SELECT, "SELECT COUNT(DISTINCT eveID) as total FROM groupmembers
										WHERE groupID=:group AND memberType ='char' ")
										->param(':group', $this->id)
										->execute()
										->current();
		$num_chars = $num_chars['total'];

		return ($num_corps + $num_chars);
	}
	
	public function incrementStat(string $stat, array $acccessData)
	{
		if( !$this->stats_enabled )
		{
			return;
		}

		if( !in_array( $stat, ['adds','updates','wormholes','pos_adds','pos_updates'] ) )
		{
			throw new Exception("invalid stat key");
		}

		$duplicate_update_string = $stat .'='. $stat .'+1';

		DB::query(Database::INSERT, 'INSERT INTO stats (`charID`,`charName`,`groupID`,`chainmap_id`,`dayStamp`,`'.$stat.'`)
												VALUES(:charID, :charName, :groupID, :chainmap, :dayStamp, 1)
												ON DUPLICATE KEY UPDATE '.$duplicate_update_string)
							->param(':charID',  Auth::$session->charID )
							->param(':charName', Auth::$session->charName )
							->param(':groupID', $this->id )
							->param(':chainmap', $acccessData['active_chain_map'] )
							->param(':dayStamp', miscUtils::getDayStamp() )
							->execute();
	}


	public function applyISKCharge(float $amount)
	{
		DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance - :amount') ) )->param(':amount', $amount)->where('id', '=',  $group_id)->execute();
	}

	public function applyISKPayment(float $amount)
	{
		DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance + :amount'), 'billable' => 1 ) )->param(':amount', $amount)->where('id', '=',  $group_id)->execute();
	}


	public function recacheMembers()
	{
		//placeholder in case we want to implement
	}
}