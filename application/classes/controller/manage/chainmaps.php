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
		$this->template->title = __('Chain Map Management');

		$view = $this->template->content = View::factory('manage/chainmaps/list');

		$view->set('user', Auth::$user->data);

		$group = ORM::factory('group', Auth::$user->data['groupID']);
		$chainmaps = $group->chainmaps->find_all();
		
		$view->set('chainmaps', $chainmaps );
	}

   public function action_add()
   {
		$errors = array();
		
		$this->template->title = __('Add a Chain Map');

		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$data = array(	'chainmap_name' => '',
						'chainmap_homesystems' => '',
						'chainmap_skip_purge_home_sigs' => 1
						);
		$view = View::factory('manage/chainmaps/add_edit_form');
		$view->bind('errors', $errors);
		$view->bind('data', $data);
		
		$view->set('mode', 'add');
		
		if ($this->request->method() == "POST") 
		{
			try 
			{
				$sg = ORM::factory('chainmap');
				$sg->chainmap_name = $_POST['chainmap_name'];
				$sg->group_id = Auth::$user->data['groupID'];

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

				$homeSystems = trim($_POST['chainmap_homesystems']);
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
								$id = mapUtils::findSystemByName(trim($v));
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
					$sg->chainmap_homesystems_ids = implode(',', $chainmap_homesystems_ids);
					$sg->chainmap_homesystems = implode(',', $homeSystems);
				}
				else
				{
					$sg->chainmap_homesystems = '';
					$sg->chainmap_homesystems_ids = '';
				}													

				$sg->chainmap_skip_purge_home_sigs = intval($_POST['chainmap_skip_purge_home_sigs']);

				$sg->save();
	
				$chainmap = new chainmap($sg->chainmap_id, Auth::$user->data['groupID']);
				$chainmap->rebuild_map_data_cache();
				groupUtils::recacheGroup(Auth::$user->data['groupID']);
				
				
				HTTP::redirect('manage/chainmaps/list');
				return;
			} 
			catch (ORM_Validation_Exception $e) 
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				Message::add('error', __('Error: Values could not be saved.'));
				$errors = $e->errors('add_chain_map');
				$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
				$view->set('errors', $errors);
				// Pass on the old form values

				$view->set('data', $data);
			}
		}
		  
		$this->template->content = $view;
   }
   
	public function action_edit()
	{
		$id = intval($this->request->param('id'));
			
		$this->template->title = __('Edit Chain Map');

		$sg = ORM::factory('chainmap', $id);
		if( $sg->group_id != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: You do not have permission to edit that chainmap.'));
			HTTP::redirect('manage/chinmaps');
		}
		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$errors = array();

		$view = View::factory('manage/chainmaps/add_edit_form');
		$view->bind('errors', $errors);
		$view->set('mode', 'edit');
		$view->set('id', $id);
		
		$chainmap = new chainmap($id, Auth::$user->data['groupID']);
		
		if ( !empty($_POST)  ) 
		{
			try 
			{
				$sg->chainmap_name = $_POST['chainmap_name'];

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


				$homeSystems = trim($_POST['chainmap_homesystems']);
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
								$id = mapUtils::findSystemByName(trim($v));
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
					$sg->chainmap_homesystems_ids = implode(',', $chainmap_homesystems_ids);
					$sg->chainmap_homesystems = implode(',', $homeSystems);
				}
				else
				{
					$sg->chainmap_homesystems = '';
					$sg->chainmap_homesystems_ids = '';
				}						

				$sg->chainmap_skip_purge_home_sigs = intval($_POST['chainmap_skip_purge_home_sigs']);

				$sg->save();

				$chainmap->rebuild_map_data_cache();
				groupUtils::recacheGroup(Auth::$user->data['groupID']);
				
				HTTP::redirect('manage/chainmaps/list');
				return;
			} 
			catch (ORM_Validation_Exception $e) 
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				Message::add('error', __('Error: Values could not be saved.'));
				$errors = $e->errors('edit_chain_map');
				$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
				$view->set('errors', $errors);
				// Pass on the old form values

				$view->set('data', array( 'chainmap_name' => $_POST['chainmap_name'],
											'chainmap_homesystems' => $_POST['chainmap_homesystems'],
											'chainmap_skip_purge_home_sigs' => $_POST['chainmap_skip_purge_home_sigs']
										 ) );
			}
		}
	  
		$view->set('data', $sg->as_array() );
  
		$this->template->content = $view;
	}   
   

	public function action_remove()
	{
		$id = intval($this->request->param('id'));

		$this->template->title = __('Remove Chain Map');

		$sg = ORM::factory('chainmap', $id);
		if( $sg->group_id != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: You do not have permission to remove that chainmap.'));
			HTTP::redirect('manage/chainmaps');
		}

		$view = View::factory('manage/chainmaps/delete');
		$view->set('id', $id);
		if ( !empty($_POST)  ) 
		{
			try 
			{
				DB::update('groupmembers')->set( array('chainmap_id' => 0 ) )->where( 'chainmap_id', '=', $sg->chainmap_id )->execute();
				DB::delete('activesystems')->where('chainmap_id', '=', $sg->chainmap_id)->execute();

				//groupUtils::deleteSubGroupCache($sg->chainmap_id);
				groupUtils::recacheGroup(Auth::$user->data['groupID']);
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