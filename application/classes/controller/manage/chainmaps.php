<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

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
			HTTP::redirect('account/overview');
		}
	}

	public function action_list()
	{
		$this->template->title = __('Group management');

		$view = $this->template->content = View::factory('manage/chainmaps/list');

		$view->set('user', Auth::$user->data);

		$group = ORM::factory('group', Auth::$user->data['groupID']);
		$view->set('group', $group );
	}

   public function action_add()
   {
		$this->template->title = __('Subgroup management');

		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$errors = array();
		$data = array('sgName' => '',
						'sgHomeSystems' => '',
						'sgSysListShowReds' => 1,
						'sgSkipPurgeHomeSigs' => 0,
						);
		$view = View::factory('manage/chainmaps/add_edit_form');
		$view->bind('errors', $errors);
		$view->bind('data', $data);
		
		$view->set('mode', 'add');
		
		if ($this->request->method() == "POST") 
		{
			try 
			{
				$sg = ORM::factory('subgroup');
				$sg->sgName = $_POST['sgName'];
				$sg->groupID = Auth::$user->data['groupID'];


				if( !empty($_POST['password']) && !empty($_POST['password_confirm']) )
				{
					if( $_POST['password'] == $_POST['password_confirm'] )
					{
						$sg->sgAuthPassword = sha1($_POST['password'].$group->authSalt);
					}
					else
					{
						Message::add( 'error', __('Error: The password was not saved because it did not match between the two fields.') );
					}
				}


				$homeSystems = trim($_POST['sgHomeSystems']);
				if( !empty($homeSystems) )
				{
					$homeSystems = explode(',', $homeSystems);
					$homeSystemIDs = array();
					if( is_array( $homeSystems ) )
					{
						foreach($homeSystems as $k => $v)
						{
							if( trim($v) != '' )
							{
								$id = $this->__findSystemByName(trim($v));
								if( $id != 0 )
								{
									$homeSystemIDs[] = $id;
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
					$sg->sgHomeSystemIDs = implode(',', $homeSystemIDs);
					$sg->sgHomeSystems = implode(',', $homeSystems);
				}
				else
				{
					$sg->sgHomeSystems = '';
					$sg->sgHomeSystemIDs = '';
				}												

				$sg->sgSkipPurgeHomeSigs = intval($_POST['sgSkipPurgeHomeSigs']);
				$sg->sgSysListShowReds = intval($_POST['sgSysListShowReds']);

				$sg->save();

				//$this->__recacheCorpMembers();
				groupUtils::recacheSubGroup($sg->subGroupID);
				HTTP::redirect('manage/chainmaps/list');
				return;
			} 
			catch (ORM_Validation_Exception $e) 
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				Message::add('error', __('Error: Values could not be saved.'));
				$errors = $e->errors('addSubGroup');
				$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
				$view->set('errors', $errors);
				// Pass on the old form values

				$view->set('data', array(
											'sgName' => $_POST['sgName'],
											'sgHomeSystems' => $_POST['sgHomeSystems'],
											'sgSysListShowReds' => $_POST['sgSysListShowReds'],
											'sgSkipPurgeHomeSigs' => $_POST['sgSkipPurgeHomeSigs']
										) 
						);
			}
		}
		  
		$this->template->content = $view;
   }
   
	public function action_edit()
	{
		$id = intval($this->request->param('id'));
			
		$this->template->title = __('Editing sub group');

		$sg = ORM::factory('subgroup', $id);
		if( $sg->groupID != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: You do not have permission to edit that subgroup.'));
			HTTP::redirect('manage/group/subgroups');
		}
		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$errors = array();

		$view = View::factory('manage/chainmaps/add_edit_form');
		$view->bind('errors', $errors);
		$view->set('mode', 'edit');
		$view->set('id', $id);
		
		if ( !empty($_POST)  ) 
		{
			try 
			{
				$sg->sgName = $_POST['sgName'];

				if( !empty($_POST['password']) && !empty($_POST['password_confirm']) )
				{
					if( $_POST['password'] == $_POST['password_confirm'] )
					{
						$sg->sgAuthPassword = sha1($_POST['password'].$group->authSalt);
					}
					else
					{
						Message::add( 'error', __('Error: The password was not saved because it did not match between the two fields.') );
					}
				}


				$homeSystems = trim($_POST['sgHomeSystems']);
				if( !empty($homeSystems) )
				{
					$homeSystems = explode(',', $homeSystems);
					$homeSystemIDs = array();
					if( is_array( $homeSystems ) )
					{
						foreach($homeSystems as $k => $v)
						{
							if( trim($v) != '' )
							{
								$id = $this->__findSystemByName(trim($v));
								if( $id != 0 )
								{
									$homeSystemIDs[] = $id;
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
					$sg->sgHomeSystemIDs = implode(',', $homeSystemIDs);
					$sg->sgHomeSystems = implode(',', $homeSystems);
				}
				else
				{
					$sg->sgHomeSystems = '';
					$sg->sgHomeSystemIDs = '';
				}						

				$sg->sgSkipPurgeHomeSigs = intval($_POST['sgSkipPurgeHomeSigs']);
				$sg->sgSysListShowReds = intval($_POST['sgSysListShowReds']);

				$sg->save();

				//$this->__recacheCorpMembers();
				groupUtils::recacheSubGroup($sg->subGroupID);
				HTTP::redirect('manage/chainmaps/list');
				return;
			} 
			catch (ORM_Validation_Exception $e) 
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				Message::add('error', __('Error: Values could not be saved.'));
				$errors = $e->errors('editSubGroup');
				$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
				$view->set('errors', $errors);
				// Pass on the old form values

				$view->set('data', array( 'sgName' => $_POST['sgName'],
											'sgHomeSystems' => $_POST['sgHomeSystems'],
											'sgSysListShowReds' => $_POST['sgSysListShowReds'],
											'sgSkipPurgeHomeSigs' => $_POST['sgSkipPurgeHomeSigs']
										 ) );
			}
		}
	  
		$view->set('data', $sg->as_array() );
  
		$this->template->content = $view;
	}   
   

	public function action_remove()
	{
		$id = intval($this->request->param('id'));

		$this->template->title = __('Removing sub group');

		$sg = ORM::factory('subgroup', $id);
		if( $sg->groupID != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: You do not have permission to remove that subgroup.'));
			HTTP::redirect('manage/group/subgroups');
		}

		$view = View::factory('manage/chainmaps/delete');
		$view->set('id', $id);
		if ( !empty($_POST)  ) 
		{
			try 
			{
				DB::update('groupmembers')->set( array('subGroupID' => 0 ) )->where( 'subGroupID', '=', $sg->subGroupID )->execute();
				DB::delete('activesystems')->where('subGroupID', '=', $sg->subGroupID)->execute();

				groupUtils::deleteSubGroupCache($sg->subGroupID);
				$sg->delete();

				//$this->__recacheCorpMembers();
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
					Message::add('error', __('Error: Removal of subgroup failed for unknown reasons.'));
				}
			}
		}

		$view->set('data', $sg->as_array() );

		$this->template->content = $view;
	}
	
	/**
	* View: Access not allowed.
	*/
	public function action_noaccess() 
	{
		$this->template->title = __('Access not allowed');
		$view = $this->template->content = View::factory('user/noaccess');
	}
}