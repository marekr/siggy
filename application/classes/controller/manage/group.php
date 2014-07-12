<?php

require_once APPPATH.'classes/groupUtils.php';
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
		'subgroups' => array('can_manage_group_members'),
		'addMember' => array('can_manage_group_members'),
		'addSubGroup' => array('can_manage_group_members'),
		'editMember' => array('can_manage_group_members'),
		'editSubGroup' => array('can_manage_group_members'),
		'removeMember' => array('can_manage_group_members'),
		'removeSubGroup' => array('can_manage_group_members')
	);

	/**
	* View: Redirect admins to admin index, users to user profile.
	*/
	public function action_index() 
	{
		if( Auth::$user->isGroupAdmin() || Auth::$user->data['admin'] ) 
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

		$view->set('user', Auth::$user->data );

		$group = ORM::factory('group', Auth::$user->data['groupID']);
		$view->set('group', $group );
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
			
			
			$group = ORM::factory('group', Auth::$user->data['groupID']);
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
					try 
					{
						$member = ORM::factory('groupmember');
						$member->eveID = $_POST['eveID'];
						$member->accessName = $_POST['accessName'];
						$member->groupID = Auth::$user->data['groupID'];
						$member->memberType = $_POST['memberType'];
						if( isset( $_POST['subGroupID'] ) )
						{
							$member->subGroupID = $_POST['subGroupID'];
						}
						else
						{
							$member->subGroupID = 0;
						}
						$member->save();
						if( $member->memberType == 'corp' )
						{
							groupUtils::recacheCorpList();
							groupUtils::recacheCorp($member->eveID);
						}
						elseif( $member->memberType == 'char' )
						{
							groupUtils::recacheCharList();
							groupUtils::recacheChar($member->eveID);
						}
						
						Message::add('sucess', 'Group member added');
						HTTP::redirect('manage/group/members');
						return;
					} 
					catch (ORM_Validation_Exception $e) 
					{
						//something broke, restart because our form is currently idiot proof anyway
						HTTP::redirect('manage/group/addMember');
					}
				}
				else if ( $action == 'doForm' )
				{
					if( empty($_POST['eveID']) || empty($_POST['accessName']) || empty($_POST['memberType']) )
					{
						//bad request
						HTTP::redirect('manage/group/addMember');
					}
					
					$view = View::factory('manage/group/addMemberSimpleSelected');
					$group = ORM::factory('group', Auth::$user->data['groupID']);
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
		else
		{
			$errors = array();
			$data = array('eveID' => '',
						'accessName' => '',
						'subGroupID' => 0,
						'memberType' => 'corp');
	  
			$view = View::factory('manage/group/memberForm');
			$view->set('mode', 'add');
			$view->bind('errors', $errors);
			$view->bind('data', $data);
			
			if ($this->request->method() == "POST") 
			{
				try 
				{
					$member = ORM::factory('groupmember');
					$member->eveID = $_POST['eveID'];
					$member->accessName = $_POST['accessName'];
					$member->groupID = Auth::$user->data['groupID'];
					$member->memberType = $_POST['memberType'];
					if( isset( $_POST['subGroupID'] ) )
					{
							$member->subGroupID = $_POST['subGroupID'];
					}
					else
					{
							$member->subGroupID = 0;
					}
					$member->save();
					if( $member->memberType == 'corp' )
					{
							groupUtils::recacheCorpList();
							groupUtils::recacheCorp($member->eveID);
					}
					elseif( $member->memberType == 'char' )
					{
							groupUtils::recacheCharList();
							groupUtils::recacheChar($member->eveID);
					}
					HTTP::redirect('manage/group/members');
					return;
				} 
				catch (ORM_Validation_Exception $e) 
				{
					// Get errors for display in view
					// Note how the first param is the path to the message file (e.g. /messages/register.php)
					Message::add('error', __('Error: Values could not be saved.'));
					$errors = $e->errors('addMember');
					$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
					// Pass on the old form values

					$view->set('data', array('eveID' => $_POST['eveID'], 'accessName' => $_POST['accessName']) );
				}
			}
			$view->set('user', Auth::$user->data);

			$group = ORM::factory('group', Auth::$user->data['groupID']);
			$view->set('group', $group );

			$this->template->content = $view;
		}
	}

   
   
   public function action_editMember()
   {
		$id = $this->request->param('id');
		
		$this->template->title = __('Group management');

		$member = ORM::factory('groupmember', $id);
		if( $member->groupID != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: You do not have permission to edit that group member.'));
			HTTP::redirect('manage/group/members');
		}

		$errors = array();

		$view = View::factory('manage/group/memberForm');
		$view->bind('errors', $errors);
		$view->set('mode', 'edit');
		$view->set('id', $id);
	
		if ($this->request->method() == "POST") 
		{
			try 
			{
				//delete to prevent LOLs
				if( $member->memberType == 'corp' )
				{
					groupUtils::deleteCorpCache( $member->eveID );
				}
				else
				{
					groupUtils::deleteCharCache( $member->eveID );
				}

				$member->eveID = $_POST['eveID'];
				$member->accessName = $_POST['accessName'];
				$member->groupID = Auth::$user->data['groupID'];
				$member->memberType = $_POST['memberType'];
				if( isset( $_POST['subGroupID'] ) )
				{
					$member->subGroupID = $_POST['subGroupID'];
				}
				else
				{
					$member->subGroupID = 0;
				}
				
				$member->save();
				if( $member->memberType == 'corp' )
				{
					groupUtils::recacheCorpList();
					groupUtils::recacheCorp($member->eveID);
				}
				elseif( $member->memberType == 'char' )
				{
					groupUtils::recacheCharList();
					groupUtils::recacheChar($member->eveID);
				}
				
				HTTP::redirect('manage/group/members');
				return;
			} 
			catch (ORM_Validation_Exception $e) 
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				Message::add('error', __('Error: Values could not be saved.'));
				$errors = $e->errors('editMember');
				$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
				$view->set('errors', $errors);
				// Pass on the old form values

				$view->set('data', array('eveID' => $_POST['eveID'], 'accessName' => $_POST['accessName']) );
			}
		}

		$view->set('data', $member->as_array() );

		$view->set('user', Auth::$user->data);

		$group = ORM::factory('group', Auth::$user->data['groupID']);
		$view->set('group', $group );

		$this->template->content = $view;
   }   
   
	public function action_removeMember()
	{
		$id = intval($this->request->param('id'));
	
		$this->template->title = __('Group management');

		$member = ORM::factory('groupmember', $id);
		if( $member->groupID != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: You do not have permission to remove that group member.'));
			HTTP::redirect('manage/group/members');
		}

		$view = View::factory('manage/group/deleteForm');
		$view->set('id', $id);
		if ( !empty($_POST)  ) 
		{
			try 
			{
				if( $member->memberType == 'corp' )
				{
					groupUtils::recacheCorpList();
					groupUtils::deleteCorpCache( $member->eveID );
				}
				elseif( $member->memberType == 'char' )
				{
					groupUtils::recacheCharList();
					groupUtils::deleteCharCache( $member->eveID );
				}
				$member->delete();
				//$this->__recacheCorpMembers();
				HTTP::redirect('manage/group/members');
			} 
			catch (Exception $e) 
			{
				if($e instanceof HTTP_Exception)
				{
					throw $e;
				}
				else
				{
					Message::add('error', __('Error: Removal of member failed for unknown reasons.'));
				}
			}
		}

		$view->set('data', $member->as_array() );

		$this->template->content = $view;
	}
}