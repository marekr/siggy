<?php

use Illuminate\Database\Capsule\Manager as DB;

class Controller_Manage_Group extends Controller_Manage
{
   /*
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
		'members' => array('can_manage_group_members'),
		'addMember' => array('can_manage_group_members'),
		'editMember' => array('can_manage_group_members'),
		'removeMember' => array('can_manage_group_members'),
	);

	/**
	* View: Redirect admins to admin index, users to user profile.
	*/
	public function action_index()
	{
		if( Auth::$user->isGroupAdmin() || Auth::$user->admin )
		{
			HTTP::redirect('manage/group/members');
		}
		else
		{
			HTTP::redirect('manage/access/denied');
		}
	}

	public function action_members()
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
			
			$html = view('manage.group.members_table', [
													'members' => $members,
													'chainmap_id' => $c->chainmap_id
												]);

			$membersHTML[ $c->chainmap_id ] = $html;
		}
		
		$resp = view('manage.group.members', [
												'chainmaps' => $chainmaps,
												'membersHTML' => $membersHTML
											]);
		
		$this->response->body($resp);
	}

	public function action_addMember()
	{
		$id = intval($this->request->param('id'));

		if( $id == 0 || $id == 1 )
		{
			$results = array();
			$errors = array();
			$data = array('memberType' => 'corp');

			if ($this->request->method() == "POST")
			{
				$errors = array();
				if( empty($_POST['searchName']) )
				{
					$errors['searchName'] = 'Name cannot be empty';
				}

				if( !count($errors) )
				{
					if($_POST['memberType'] == 'char')
					{
						$results = Character::searchEVEAPI($_POST['searchName'], false);
					}

					if($_POST['memberType'] == 'corp')
					{
						$results = Corporation::searchEVEAPI($_POST['searchName'], false);
					}
				}
			}
		
			$resp = view('manage.group.add_member_search', [
													'data' => $data,
													'errors' => $errors,
													'results' => $results,
													'memberType' => isset($_POST['memberType']) ? $_POST['memberType'] : 'char'
												]);
			
			$this->response->body($resp);
			return;
		}
		else if( $id == 2 )
		{
			if ($this->request->method() == "POST")
			{
				$action = $_POST['act'];

				if( $action == 'doAdd' )
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

					Message::add('success', 'Group member added');
					HTTP::redirect('manage/group/members');
				}
				else if ( $action == 'doForm' )
				{
					if( empty($_POST['eveID']) || empty($_POST['accessName']) || empty($_POST['memberType']) )
					{
						//bad request
						HTTP::redirect('manage/group/addMember');
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
						Message::add('error', ___('This member already has access to all chain maps possible'));
						HTTP::redirect('manage/group/addMember');
					}
					
					$resp = view('manage.group.add_member_selected', [
															'chainmaps' => $chainmaps,
															'eveID' => intval($_POST['eveID']),
															'memberType' =>  $_POST['memberType'],
														]);
					
					$this->response->body($resp);
					return;
				}
				else
				{
					//invalid
					HTTP::redirect('manage/group/addMember');
				}

			}
			else
			{
				//invalid
				HTTP::redirect('manage/group/addMember');
			}
		}
	}

	public function action_removeMember()
	{
		$id = intval($this->request->param('id'));

		$member = GroupMember::find($id);
		if( $member->groupID != Auth::$user->groupID )
		{
			Message::add('error', ___('Error: You do not have permission to remove that group member.'));
			HTTP::redirect('manage/group/members');
		}

		if ($this->request->method() == HTTP_Request::POST)
		{
			//trigger last_update value to change
			Auth::$user->group->save();
			Auth::$user->group->recacheMembers();

			HTTP::redirect('manage/group/members');
		}
		
		$resp = view('manage.group.delete_form', [
												'data' => $member->as_array(),
												'id' => $id,
											]);
		
		$this->response->body($resp);
	}
}
