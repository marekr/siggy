<?php

use Illuminate\Database\Capsule\Manager as DB;
use Siggy\View;

class Controller_Manage_Chainmaps extends Controller_Manage
{
	/**
	* @var string Filename of the template file.
	*/
	public $template = 'template/manage';

	/*
	 * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
	 */
	public $auth_required = 'gadmin';

   /*
	* Controls access for separate actions
    */
	public $secure_actions = array(
	);

	/**
	* View: Redirect admins to admin index, users to user profile.
	*/
	public function action_index()
	{
		if( Auth::$user->isGroupAdmin() )
		{
			HTTP::redirect('manage/billing/overview');
		}
		else
		{
			HTTP::redirect('manage/access/denied');
		}
	}

	public function action_list()
	{
		$group = Auth::$user->group;
		$chainmaps = $group->chainMaps();
		$resp = view('manage.chainmaps.list', [
												'chainmaps' => $chainmaps
											]);
		
		$this->response->body($resp);
	}

	public function action_add()
	{
		$group = Auth::$user->group;

		$data = new stdClass;
		$data->chainmap_name = '';
		$data->chainmap_homesystems = '';
		$data->chainmap_skip_purge_home_sigs = 1;

		if ($this->request->method() == "POST")
		{
			$new = [
				'chainmap_name' => $_POST['chainmap_name'],
				'group_id' => Auth::$user->groupID,
				'chainmap_type' => 'fixed',
				'chainmap_skip_purge_home_sigs' => intval($_POST['chainmap_skip_purge_home_sigs'] ?? 0),
			];
			
			$validator = Validator::make($new, [
				'chainmap_name' => 'required',
			]);
			
			if(!$validator->fails())
			{
				list($new['chainmap_homesystems_ids'], $new['chainmap_homesystems']) = $this->___process_home_system_input($_POST['chainmap_homesystems']);

				$chainmap = Chainmap::create($new);
				$chainmap->rebuild_map_data_cache();

				Auth::$user->group->save();
				Auth::$user->group->recacheChainmaps();

				HTTP::redirect('manage/chainmaps/list');
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}

		$resp = view('manage.chainmaps.add_edit_form', [
												'mode' => 'add',
												'chainmap' => $data
											]);
		
		$this->response->body($resp);
	}

	private function ___process_home_system_input($txt)
	{
		$homeSystemIDs = array();
		$homeSystems = array();

		$txt = trim($txt);
		if( !empty($txt) )
		{
			$homeSystems = explode(',', $txt);
			$homeSystemIDs = array();
			if( is_array( $homeSystems ) )
			{
				foreach($homeSystems as $k => $v)
				{
					$v = trim($v);
					if( !empty($v) != '' )
					{
						$system = System::findByName($v);
						if($system != null)
						{
							$homeSystemIDs[] = $system->id;
						}
						else
						{
							unset($homeSystems[ $k ] );
						}
					}
					else
					{
						unset($homeSystems[ $k ] );
					}
				}
			}
		}

		return array( implode(',', $homeSystemIDs), implode(',', $homeSystems) );
	}

	public function action_remove_access()
	{
		$id = $this->request->param('id');
		list($chainmap_id, $groupMemberID) = explode('-', $id);

		$cm = Chainmap::find($chainmap_id, Auth::$user->groupID);
		if( $cm == null )
		{
			Message::add('error', ___('Error: You do not have permission for that chainmap.'));
			HTTP::redirect('manage/chainmaps');
		}

		$member = GroupMember::find($groupMemberID);
		if( $member == null || $member->groupID != Auth::$user->group->id )
		{
			Message::add('error', ___('Error: The group member does not exist.'));
			HTTP::redirect('manage/group/members');
		}
		
		if ($this->request->method() == HTTP_Request::POST)
		{
			DB::delete('DELETE FROM chainmaps_access
											WHERE group_id=:group_id AND chainmap_id=:chainmap
											AND groupmember_id=:member_id',[
												'group_id' => Auth::$user->group->id,
												'chainmap' => $chainmap_id,
												'member_id' => $groupMemberID
											]);


			$count = DB::selectOne('SELECT COUNT(*) as total
											FROM chainmaps_access
											WHERE groupmember_id=?',[ $groupMemberID]);

			$eveID = $member->eveID;
			$memberType = $member->memberType;
			if( $count->total == 0)
			{
				$member->delete();
			}

			//trigger last_update value to change
			Auth::$user->group->save();
			Auth::$user->group->recacheChainmaps();

			HTTP::redirect('manage/group/members');
		}
		
		$resp = view('manage.chainmaps.remove_access', [
												'id' => $id,
												'member' => $member,
											]);
		
		$this->response->body($resp);
	}

	public function action_edit()
	{
		$id = intval($this->request->param('id'));

		$chainmap = Chainmap::find($id, Auth::$user->groupID);
		if( $chainmap == null )
		{
			Message::add('error', ___('Error: You do not have permission to edit that chainmap.'));
			HTTP::redirect('manage/chainmaps');
		}

		if ($this->request->method() == "POST") 
		{
			$update = [
				'chainmap_name' => $_POST['chainmap_name'],
				'chainmap_skip_purge_home_sigs' => intval($_POST['chainmap_skip_purge_home_sigs'] ?? 0)
			];

			$validator = Validator::make($update, [
				'chainmap_name' => 'required',
			]);


			if(!$validator->fails())
			{
				list($update['chainmap_homesystems_ids'], $update['chainmap_homesystems']) = $this->___process_home_system_input($_POST['chainmap_homesystems']);
				$chainmap->fill($update);
				$chainmap->save();

				$chainmap->rebuild_map_data_cache();
				Auth::$user->group->save();
				Auth::$user->group->recacheChainmaps();

				HTTP::redirect('manage/chainmaps/list');
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}
		
		$resp = view('manage.chainmaps.add_edit_form', [
												'mode' => 'edit',
												'chainmap' => $chainmap
											]);
		
		$this->response->body($resp);
	}


	public function action_remove()
	{
		$id = intval($this->request->param('id'));

		$chainmap = Chainmap::find($id, Auth::$user->groupID);
		if( $chainmap == null )
		{
			Message::add('error', ___('Error: You do not have permission to remove that chainmap.'));
			HTTP::redirect('manage/chainmaps');
		}

		if( $chainmap->chainmap_type == 'default' )
		{
			Message::add('error', ___('Error: You cannot delete your default chain map'));
			HTTP::redirect('manage/chainmaps');
		}

		if ($this->request->method() == "POST") 
		{
			try
			{
				DB::table('chainmaps_access')->where('chainmap_id', '=', $chainmap->chainmap_id)->delete();
				DB::table('activesystems')->where('chainmap_id', '=', $chainmap->chainmap_id)->delete();
				
				$groupmembers = GroupMember::findByGroup(Auth::$user->group->id);
				if( count($groupmembers) > 0 )
				{
					foreach($groupmembers as $member)
					{
						$count = DB::selectOne('SELECT COUNT(*) as total
										FROM chainmaps_access
										WHERE groupmember_id=?',[$member->id]);
						
						
						if( $count->total == 0)
						{
							DB::delete('DELETE FROM groupmembers WHERE id=?',[$member->id]);
						}
					}
				}
				
				Auth::$user->group->save();
				
				$chainmap->delete();
				
				Auth::$user->group->recacheChainmaps();

				HTTP::redirect('manage/chainmaps/list');
			}
			catch (Exception $e)
			{
				if($e instanceof HTTP_Exception)
				{
					throw $e;
				}
				else
				{
					Message::add('error', ___('Error: Removal of subgroup failed for unknown reasons.'));
				}
			}
		}
		
		$resp = view('manage.chainmaps.delete', [
												'chainmap' => $chainmap
											]);
		
		$this->response->body($resp);
	}
}
