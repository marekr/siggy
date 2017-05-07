<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use \Auth;
use \Chainmap;
use \Group;
use \GroupMember;
use \Character;
use \Corporation;

class GroupMembersController extends BaseController
{
	public $actionAcl = [
		'members' => ['can_manage_group_members'],
		'addMember' => ['can_manage_group_members'],
		'editMember' => ['can_manage_group_members'],
		'removeMember' => ['can_manage_group_members'],
	];

	public function getList()
	{
		$chainmaps = Chainmap::where('group_id', Auth::$user->group->id)
						->get()
						->keyBy('chainmap_id')
						->all();


		foreach($chainmaps as $c)
		{
			$members = DB::select("SELECT gm.* FROM groupmembers gm
													LEFT JOIN chainmaps_access a ON(gm.id=a.groupmember_id)
													WHERE chainmap_id=?",[$c->chainmap_id]);

			$table[] = ['members' => $members, 'chainmap' => $c];
		}
		
		return view('manage.group.members', [
												'chainmaps' => $chainmaps,
												'table' => $table
											]);
	}

	public function postAddDetails()
	{
		if( empty($_POST['eveID']) || empty($_POST['accessName']) || empty($_POST['memberType']) )
		{
			//bad request
			return redirect('manage/group/add');
		}

		//see if member exists?
		$member = GroupMember::findByGroupAndType(Auth::$user->groupID, $_POST['memberType'], (int)$_POST['eveID']);

		$chainmaps = array();
		if( $member != null )
		{
			$chainmaps = DB::select("SELECT * FROM chainmaps
														WHERE group_id=:group1 AND
														chainmap_id NOT IN(
															SELECT chainmap_id FROM chainmaps_access
															WHERE group_id=:group2 AND groupmember_id=:member
														)
														",[
															'group1' => Auth::$user->group->id,
															'group2' => Auth::$user->group->id,
															'member' => $member->id
														]);
		}
		else
		{
			$chainmaps = Chainmap::where('group_id', Auth::$user->group->id)->get()->all();
		}

		if( $chainmaps == null)
		{
			flash('This member already has access to all chain maps possible')->error();
			return redirect('manage/group/add');
		}
		
		return view('manage.group.add_member_selected', [
												'chainmaps' => $chainmaps,
												'eveID' => intval($_POST['eveID']),
												'accessName' => $_POST['accessName'],
												'memberType' =>  $_POST['memberType'],
											]);
	}
	
	public function postAddFinish()
	{
		$member = GroupMember::findByGroupAndType(Auth::$user->groupID, $_POST['memberType'], (int)$_POST['eveID']);

		if( $member == null )
		{
			$data = [
				'eveID' => $_POST['eveID'],
				'accessName' => $_POST['accessName'],
				'groupID' => Auth::$user->groupID,
				'memberType' => $_POST['memberType'],
			];						

			$member = GroupMember::create($data);
		}

		if( isset( $_POST['chainmap_id'] ) && intval($_POST['chainmap_id']) > 0)
		{
			$insert['group_id'] = Auth::$user->groupID;
			$insert['chainmap_id'] = intval($_POST['chainmap_id']);
			$insert['groupmember_id'] = $member->id;
			DB::table('chainmaps_access')->insert($insert);
		}

		Auth::$user->group->save();
		Auth::$user->group->recacheChainmaps();
		Auth::$user->group->recacheMembers();

		flash('Group member added')->success();
		return redirect('manage/group/members');
	}

	public function postAdd()
	{
		$validator = Validator::make($_POST, [
			'searchName' => 'required',
		]);

		if( $validator->passes() )
		{
			if($_POST['memberType'] == 'char')
			{
				$results = Character::searchEVEAPI($_POST['searchName'], false);
			}

			if($_POST['memberType'] == 'corp')
			{
				$results = Corporation::searchEVEAPI($_POST['searchName'], false);
			}
			
			return view('manage.group.add_member_search', [
													'results' => $results,
													'memberType' => $_POST['memberType']
												]);
		}
		
		return redirect()->back()
					->withErrors($validator)
					->withInput();
	}

	public function getAdd(Request $request)
	{
		$id = intval(0);

	
		return view('manage.group.add_member_search', [
												'results' => [],
												'memberType' => 'char'
											]);
	}

	public function getRemoveMember()
	{
		$id = intval($this->request->param('id'));

		$member = GroupMember::find($id);
		if( $member->groupID != Auth::$user->groupID )
		{
			flash('Error: You do not have permission to remove that group member.')->error();
			return redirect('manage/group/members');
		}

		if ($this->request->method() == HTTP_Request::POST)
		{
			//trigger last_update value to change
			Auth::$user->group->save();
			Auth::$user->group->recacheMembers();

			return redirect('manage/group/members');
		}
		
		$resp = view('manage.group.delete_form', [
												'data' => $member->as_array(),
												'id' => $id,
											]);
		
		$this->response->body($resp);
	}
}
