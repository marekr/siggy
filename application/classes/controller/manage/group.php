<?php

require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';


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
			HTTP::redirect('account/overview');
		}
	}
	/**
	* View: Access not allowed.
	*/
	public function action_noaccess()
	{
		$this->template->title = __('Access not allowed');
		$view = $this->template->content = View::factory('user/noaccess');
	}

	public function action_members()
	{
		$this->template->title = __('Group management');

		$view = $this->template->content = View::factory('manage/group/members');

		$view->set('user', Auth::$user );

		$group = Auth::$user->group;

		$chainmaps = DB::query(Database::SELECT, "SELECT * FROM chainmaps WHERE group_id=:group")
						->param(':group', Auth::$user->groupID)
						->execute()
						->as_array('chainmap_id');


		foreach($chainmaps as $c)
		{
			$html = View::factory('manage/group/members_table');

			$members = DB::query(Database::SELECT, "SELECT gm.* FROM groupmembers gm
													LEFT JOIN chainmaps_access a ON(gm.id=a.groupmember_id)
													WHERE chainmap_id=:chainmap")
							->param(':chainmap', $c['chainmap_id'])
							->execute()
							->as_array();
			$html->set('members', $members);
			$html->set('chainmap_id', $c['chainmap_id']);

			$membersHTML[ $c['chainmap_id'] ] = $html;
		}

		$view->set('group', $group );
		$view->set('chainmaps', $chainmaps );
		$view->set('membersHTML', $membersHTML );
	}

	public function action_addMember()
	{
		$this->template->title = __('Group management');

		$id = intval($this->request->param('id'));

		if( $id == 0 || $id == 1 )
		{
			$results = array();
			$errors = array();
			$data = array('memberType' => 'corp');
			$view = View::factory('manage/group/addMemberSimple');

			if ($this->request->method() == "POST")
			{
				$errors = array();
				if( empty($_POST['searchName']) )
				{
					$errors['searchName'] = 'Name cannot be empty';
				}

				if( !count($errors) )
				{
					$results = miscUtils::searchEVEEntityByName( $_POST['searchName'], $_POST['memberType'] );
					$view->bind('memberType', $_POST['memberType'] );
				}
			}


			$group = Auth::$user->group;
			$view->set('group', $group );
			$view->bind('data', $data);
			$view->bind('errors', $errors);
			$view->bind('results', $results);

			$this->template->content = $view;
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
						DB::insert('chainmaps_access', array_keys($insert) )->values(array_values($insert))->execute();
					}

					Auth::$user->group->save();
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

					$view = View::factory('manage/group/addMemberSimpleSelected');
					$group = Auth::$user->group;

					//see if member exists?
					$member = GroupMember::findByGroupAndType(Auth::$user->groupID, $_POST['memberType'], (int)$_POST['eveID']);

					$chainmaps = array();
					if( $member != null )
					{
						$chainmaps = DB::query(Database::SELECT, "SELECT * FROM chainmaps
																	WHERE group_id=:group AND
																	chainmap_id NOT IN(
																		SELECT chainmap_id FROM chainmaps_access
																		WHERE group_id=:group AND groupmember_id=:member
																	)
																	")
										->param(':group', Auth::$user->groupID)
										->param(':member', $member->id)
										->execute()
										->as_array();
					}
					else
					{
						$chainmaps = DB::query(Database::SELECT, "SELECT * FROM chainmaps
																	WHERE group_id=:group")
										->param(':group', Auth::$user->groupID)
										->execute()
										->as_array();
					}

					if( !count($chainmaps) )
					{
						Message::add('error', __('This member already has access to all chain maps possible'));
						HTTP::redirect('manage/group/addMember');
					}

					$view->set('chainmaps', $chainmaps);
					$view->set('group', $group );

					$view->set('eveID', intval($_POST['eveID']) );
					$view->set('accessName', $_POST['accessName'] );
					$view->set('memberType', $_POST['memberType'] );
					$this->template->content = $view;
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

	public function action_editMember()
	{
		$id = $this->request->param('id');

		$this->template->title = __('Group management');

		$member = GroupMember::find($id);
		if( $member->groupID != Auth::$user->groupID )
		{
			Message::add('error', __('Error: You do not have permission to edit that group member.'));
			HTTP::redirect('manage/group/members');
		}

		$errors = array();

		$view = View::factory('manage/group/memberForm');
		$view->bind('errors', $errors);
		$view->set('mode', 'edit');
		$view->set('id', $id);

		if ($this->request->method() == HTTP_Request::POST)
		{
			//delete to prevent LOLs
			$save = [
				'eveID' => $_POST['eveID'],
				'accessName' => $_POST['accessName'],
				'groupID' => Auth::$user->groupID,
				'memberType' => $_POST['memberType']
			];

			$member->fill($save);
			$member->save();

			Auth::$user->group->save();
			Auth::$user->group->recacheMembers();

			HTTP::redirect('manage/group/members');
			return;
		}

		$view->set('data', $member->as_array() );

		$view->set('user', Auth::$user);

		$group = Auth::$user->group;
		$view->set('group', $group );

		$this->template->content = $view;
	}

	public function action_removeMember()
	{
		$id = intval($this->request->param('id'));

		$this->template->title = __('Group management');

		$member = GroupMember::find($id);
		if( $member->groupID != Auth::$user->groupID )
		{
			Message::add('error', __('Error: You do not have permission to remove that group member.'));
			HTTP::redirect('manage/group/members');
		}

		$view = View::factory('manage/group/deleteForm');
		$view->set('id', $id);
		if ($this->request->method() == HTTP_Request::POST)
		{
			//trigger last_update value to change
			Auth::$user->group->save();
			Auth::$user->group->recacheMembers();

			HTTP::redirect('manage/group/members');
		}

		$view->set('data', $member->as_array() );

		$this->template->content = $view;
	}
}
