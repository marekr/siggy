<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Facades\Auth;
use Siggy\Chainmap;
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
		$chainmaps = Chainmap::findAllByGroup(Auth::user()->group->id)
						->keyBy('id')
						->all();


		foreach($chainmaps as $c)
		{
			$members = DB::select("SELECT gm.* FROM groupmembers gm
													LEFT JOIN chainmaps_access a ON(gm.id=a.groupmember_id)
													WHERE chainmap_id=?",[$c->id]);

			$table[] = ['members' => $members, 'chainmap' => $c];
		}

		
		return view('manage.group.members', [
												'chainmaps' => $chainmaps,
												'table' => $table
											]);
	}

	public function postAddDetails(Request $request)
	{
		if (!$request->has(['eveID', 'accessName', 'memberType']))
		{
			//bad request
			return redirect('manage/group/add');
		}

		//see if member exists?
		$member = GroupMember::findByGroupAndType(Auth::user()->groupID, $request->input('memberType'), $request->input('eveID'));

		$chainmaps = array();
		if( $member != null )
		{
			$chainmaps = DB::select("SELECT * FROM chainmaps
														WHERE group_id=:group1 AND
														id NOT IN(
															SELECT chainmap_id FROM chainmaps_access
															WHERE group_id=:group2 AND groupmember_id=:member
														)
														",[
															'group1' => Auth::user()->group->id,
															'group2' => Auth::user()->group->id,
															'member' => $member->id
														]);
		}
		else
		{
			$chainmaps = Chainmap::findAllByGroup(Auth::user()->group->id)->all();
		}

		if( $chainmaps == null)
		{
			flash('This member already has access to all chain maps possible')->error();
			return redirect('manage/group/members/add');
		}
		
		return view('manage.group.add_member_selected', [
												'chainmaps' => $chainmaps,
												'eveID' => $request->input('eveID'),
												'accessName' => $request->input('accessName'),
												'memberType' =>  $request->input('memberType'),
											]);
	}
	
	public function postAddFinish(Request $request)
	{
		$member = GroupMember::findByGroupAndType(Auth::user()->groupID, $request->input('memberType'), $request->input('eveID'));

		if( $member == null )
		{
			$data = [
				'eveID' => $request->input('eveID'),
				'accessName' => $request->input('accessName'),
				'groupID' => Auth::user()->groupID,
				'memberType' => $request->input('memberType'),
			];

			$member = GroupMember::create($data);
		}

		if( $request->has('chainmap_id') && intval($request->input('chainmap_id')) > 0)
		{
			$insert['group_id'] = Auth::user()->groupID;
			$insert['chainmap_id'] = $request->input('chainmap_id');
			$insert['groupmember_id'] = $member->id;
			DB::table('chainmaps_access')->insert($insert);
		}

		Auth::user()->group->save();
		Auth::user()->group->recacheChainmaps();
		Auth::user()->group->recacheMembers();

		flash('Group member added')->success();
		return redirect('manage/group/members');
	}

	public function postAdd(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'searchName' => 'required',
		]);

		if( $validator->passes() )
		{
			$searchName = $request->input('searchName');
			if($request->input('memberType') == 'char')
			{
				$results = Character::searchEVEAPI($searchName, false);
			}
			else if($request->input('memberType') == 'corp')
			{
				$results = Corporation::searchEVEAPI($searchName, false);
			}
			
			return view('manage.group.add_member_search', [
													'results' => $results,
													'memberType' => $request->input('memberType')
												]);
		}
		
		return redirect()->back()
					->withErrors($validator)
					->withInput();
	}

	public function getAdd(Request $request)
	{
		return view('manage.group.add_member_search', [
												'results' => [],
												'memberType' => 'char'
											]);
	}

	public function getRemoveMember(int $id)
	{
		$member = GroupMember::find($id);
		if( $member->groupID != Auth::user()->groupID )
		{
			flash('Error: You do not have permission to remove that group member.')->error();
			return redirect('manage/group/members');
		}

		if ($this->request->method() == HTTP_Request::POST)
		{
			//trigger last_update value to change
			Auth::user()->group->save();
			Auth::user()->group->recacheMembers();

			return redirect('manage/group/members');
		}
		
		return view('manage.group.delete_form', [
												'data' => $member->as_array(),
												'id' => $id,
											]);
	}
}
