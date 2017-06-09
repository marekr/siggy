<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use \Auth;
use \Group;
use \Chainmap;
use \GroupMember;
use \System;

class ChainmapsController extends BaseController
{
	public function getList()
	{
		$group = Auth::$user->group;
		$chainmaps = $group->chainMaps();

		return view('manage.chainmaps.list', [
												'chainmaps' => $chainmaps
											]);
	}

	public function getAdd()
	{
		$group = Auth::$user->group;

		return view('manage.chainmaps.add_edit_form', [
												'mode' => 'add'
											]);
	}

	public function postAdd()
	{
		$group = Auth::$user->group;

		$new = [
			'chainmap_name' => $_POST['chainmap_name'],
			'group_id' => Auth::$user->groupID,
			'chainmap_type' => 'fixed',
			'chainmap_skip_purge_home_sigs' => intval($_POST['chainmap_skip_purge_home_sigs'] ?? 0),
		];
		
		$validator = Validator::make($new, [
			'chainmap_name' => 'required',
		]);
		
		if($validator->passes())
		{
			list($new['chainmap_homesystems_ids'], $new['chainmap_homesystems']) = $this->___process_home_system_input($_POST['chainmap_homesystems']);

			$chainmap = Chainmap::create($new);
			$chainmap->rebuild_map_data_cache();

			Auth::$user->group->save();
			Auth::$user->group->recacheChainmaps();

			return redirect('manage/chainmaps/list');
		}

		return redirect('manage/chainmaps/add')
					->withErrors($validator)
					->withInput();
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

	public function getEdit($id)
	{
		$chainmap = Chainmap::find($id, Auth::$user->groupID);
		if( $chainmap == null )
		{
			flash('Error: You do not have permission to edit that chainmap.')->error();
			return redirect('manage/chainmaps');
		}
		
		return view('manage.chainmaps.add_edit_form', [
												'mode' => 'edit',
												'chainmap' => $chainmap
											]);
	}

	public function postEdit($id)
	{
		$chainmap = Chainmap::find($id, Auth::$user->groupID);
		if( $chainmap == null )
		{
			flash('Error: You do not have permission to edit that chainmap.')->error();
			return redirect('manage/chainmaps');
		}
		
		$update = [
			'chainmap_name' => $_POST['chainmap_name'],
			'chainmap_skip_purge_home_sigs' => intval($_POST['chainmap_skip_purge_home_sigs'] ?? 0)
		];

		$validator = Validator::make($update, [
			'chainmap_name' => 'required',
		]);

		if($validator->passes())
		{
			list($update['chainmap_homesystems_ids'], $update['chainmap_homesystems']) = $this->___process_home_system_input($_POST['chainmap_homesystems']);
			$chainmap->fill($update);
			$chainmap->save();

			$chainmap->rebuild_map_data_cache();
			Auth::$user->group->save();
			Auth::$user->group->recacheChainmaps();

			return redirect('manage/chainmaps/list');
		}
		
		return redirect()->back()
					->withErrors($validator)
					->withInput();
	}


	public function getRemove($id)
	{
		$chainmap = Chainmap::find($id, Auth::$user->groupID);
		if( $chainmap == null )
		{
			flash('Error: You do not have permission to remove that chainmap.')->error();
			return redirect('manage/chainmaps');
		}

		if( $chainmap->chainmap_type == 'default' )
		{
			flash('Error: You cannot delete your default chain map')->error();
			return redirect('manage/chainmaps');
		}
		
		return view('manage.chainmaps.delete', [
												'chainmap' => $chainmap
											]);
	}
	

	public function postRemove($id)
	{
		$chainmap = Chainmap::find($id, Auth::$user->groupID);
		if( $chainmap == null )
		{
			flash('Error: You do not have permission to remove that chainmap.')->error();
			return redirect('manage/chainmaps');
		}

		if( $chainmap->chainmap_type == 'default' )
		{
			flash('Error: You cannot delete your default chain map')->error();
			return redirect('manage/chainmaps');
		}

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

		return redirect('manage/chainmaps/list');
	}
	
	public function getRemoveAccess($id)
	{
		list($chainmap_id, $groupMemberID) = explode('-', $id);

		$cm = Chainmap::find($chainmap_id, Auth::$user->groupID);
		if( $cm == null )
		{
			flash('Error: You do not have permission for that chainmap.')->error();
			return redirect('manage/chainmaps');
		}

		$member = GroupMember::find($groupMemberID);
		if( $member == null || $member->groupID != Auth::$user->group->id )
		{
			flash('Error: The group member does not exist.')->error();
			return redirect('manage/group/members');
		}
		
		return view('manage.chainmaps.remove_access', [
												'id' => $id,
												'member' => $member,
											]);
	}
	
	public function postRemoveAccess($id)
	{
		list($chainmap_id, $groupMemberID) = explode('-', $id);

		$cm = Chainmap::find($chainmap_id, Auth::$user->groupID);
		if( $cm == null )
		{
			flash('Error: You do not have permission for that chainmap.')->error();
			return redirect('manage/chainmaps');
		}

		$member = GroupMember::find($groupMemberID);
		if( $member == null || $member->groupID != Auth::$user->group->id )
		{
			flash('Error: The group member does not exist.')->error();
			return redirect('manage/group/members');
		}
		
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

		return redirect('manage/group/members');
	}
}
