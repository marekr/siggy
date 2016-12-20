<?php

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

		$group = Auth::$user->group();
		$chainmaps = $group->chainMaps();

		$view->set('chainmaps', $chainmaps );
	}

	public function action_add()
	{
		$errors = array();

		$this->template->title = __('Add a Chain Map');

		$group = Auth::$user->group();

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
				$sg->chainmap_type = 'fixed';

				list($sg->chainmap_homesystems_ids, $sg->chainmap_homesystems) = $this->__process_home_system_input($_POST['chainmap_homesystems']);

				$sg->chainmap_skip_purge_home_sigs = intval($_POST['chainmap_skip_purge_home_sigs']);

				$sg->save();

				$chainmap = new chainmap($sg->chainmap_id, Auth::$user->data['groupID']);
				$chainmap->rebuild_map_data_cache();
				Auth::$user->group()->save([]);
				Auth::$user->group()->recacheChainmaps();

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

	private function __process_home_system_input($txt)
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
						$id = mapUtils::findSystemByEVEName($v);
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
		}

		return array( implode(',', $homeSystemIDs), implode(',', $homeSystems) );
	}

	public function action_remove_access()
	{
		$id = $this->request->param('id');
		list($chainmap_id, $groupMemberID) = explode('-', $id);

		$cm = ORM::factory('chainmap', $chainmap_id);
		if( $cm->group_id != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: You do not have permission for that chainmap.'));
			HTTP::redirect('manage/chainmaps');
		}

		$member = ORM::factory('groupmember', $groupMemberID);
		if( $member->groupID != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: The group member does not exist.'));
			HTTP::redirect('manage/group/members');
		}

		$view = View::factory('manage/chainmaps/remove_access');
		$view->set('id', $id);
		if ($this->request->method() == HTTP_Request::POST)
		{
			DB::query(Database::DELETE, 'DELETE FROM chainmaps_access
											WHERE group_id=:group_id AND chainmap_id=:chainmap
											AND groupmember_id=:member_id')
							->param(':group_id', Auth::$user->data['groupID'])
							->param(':chainmap', $chainmap_id)
							->param(':member_id', $groupMemberID)
							->execute();


			$count = DB::query(Database::SELECT, 'SELECT COUNT(*) as total
											FROM chainmaps_access
											WHERE groupmember_id=:member_id')
							->param(':member_id', $groupMemberID)
							->execute()
							->current();

			$eveID = $member->eveID;
			$memberType = $member->memberType;
			if( !isset($count['total']) || $count['total'] == 0)
			{
				$member->delete();
			}

			if( $memberType == 'corp' )
			{
				groupUtils::deleteCorpCache( $eveID );
			}
			elseif( $memberType == 'char' )
			{
				groupUtils::deleteCharCache( $eveID );
			}

			//trigger last_update value to change
			Auth::$user->group()->save([]);
			Auth::$user->group()->recacheChainmaps();

			HTTP::redirect('manage/group/members');
		}

		$view->set('data', $member->as_array() );

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
			HTTP::redirect('manage/chainmaps');
		}
		$group = Auth::$user->group();

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

				list($sg->chainmap_homesystems_ids, $sg->chainmap_homesystems) = $this->__process_home_system_input($_POST['chainmap_homesystems']);

				$sg->chainmap_skip_purge_home_sigs = intval($_POST['chainmap_skip_purge_home_sigs']);

				$sg->save();

				$chainmap->rebuild_map_data_cache();
				Auth::$user->group()->save([]);
				Auth::$user->group()->recacheChainmaps();

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

		$chainmap = ORM::factory('chainmap', $id);
		if( $chainmap->group_id != Auth::$user->data['groupID'] )
		{
			Message::add('error', __('Error: You do not have permission to remove that chainmap.'));
			HTTP::redirect('manage/chainmaps');
		}

		if( $chainmap->chainmap_type == 'default' )
		{
			Message::add('error', __('Error: You cannot delete your default chain map'));
			HTTP::redirect('manage/chainmaps');
		}

		$view = View::factory('manage/chainmaps/delete');
		$view->set('id', $id);
		if ( !empty($_POST)  )
		{
			try
			{
				DB::delete('chainmaps_access')->where('chainmap_id', '=', $chainmap->chainmap_id)->execute();
				DB::delete('activesystems')->where('chainmap_id', '=', $chainmap->chainmap_id)->execute();
				
				$groupmembers = DB::query(Database::SELECT, 'SELECT *
									FROM groupmembers
									WHERE groupID=:group')
					->param(':group', Auth::$user->data['groupID'])
					->execute()
					->as_array();
				if( count($groupmembers) > 0 )
				{
					foreach($groupmembers as $member)
					{
						$count = DB::query(Database::SELECT, 'SELECT COUNT(*) as total
										FROM chainmaps_access
										WHERE groupmember_id=:member_id')
						->param(':member_id', $member['id'])
						->execute()
						->current();
						
						
						if( !isset($count['total']) || $count['total'] == 0)
						{
							DB::query(Database::DELETE, 'DELETE FROM groupmembers
															WHERE id=:member_id')
											->param(':member_id', $member['id'])
											->execute();

						}

						if( $member['memberType'] == 'corp' )
						{
							groupUtils::deleteCorpCache( $member['eveID'] );
						}
						elseif( $member['memberType'] == 'char' )
						{
							groupUtils::deleteCharCache( $member['eveID'] );
						}
					}
				}
				
				Auth::$user->group()->save([]);
				Auth::$user->group()->recacheChainmaps();
				$chainmap->delete();

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

		$view->set('data', $chainmap->as_array());

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
