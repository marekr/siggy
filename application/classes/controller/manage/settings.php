<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class Controller_Manage_Settings extends Controller_App
{
   /**
    * @var string Filename of the template file.
    */
   public $template = 'template/manage';

   /**
    * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
    *
    * See Controller_App for how this implemented.
    *
    * Can be set to a string or an array, for example array('login', 'admin') or 'login'
    */
   public $auth_required = 'gadmin';

   public $secure_actions = array(
      // user actions
      'members' => array('login','gadmin')
      );

	public function action_index() 
	{
		if( Auth::$user->isGroupAdmin() || Auth::$user->data['admin'] ) 
		{
			HTTP::redirect('manage/logs/activity');
		} 
		else 
		{
			HTTP::redirect('account/overview');
		}
	}
	
	public function action_chain_map()
	{
		$this->template->title = __('Chain Map settings');

		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$errors = array();
		$view = $this->template->content = View::factory('manage/settings/chain_map');
		
		$view->bind('errors', $errors);
					
		if ($this->request->method() == "POST") 
		{
			try 
			{
				$group->jumpLogEnabled = intval($_POST['jumpLogEnabled']);
				$group->jumpLogRecordNames = intval($_POST['jumpLogRecordNames']);
				$group->jumpLogRecordTime = intval($_POST['jumpLogRecordTime']);
				$group->jumpLogDisplayShipType = intval($_POST['jumpLogDisplayShipType']);
				$group->alwaysBroadcast = intval($_POST['alwaysBroadcast']);
				$group->chain_map_show_actives_ships = intval($_POST['chain_map_show_actives_ships']);
				
				$group->save();
					
				Message::add('success', __('Chain map settings saved.'));
				
				groupUtils::recacheGroup( Auth::$user->data['groupID'] );
				
				HTTP::redirect('manage/settings/chain_map');
				return;
			} 
			catch (ORM_Validation_Exception $e) 
			{
				Message::add('error', __('Error: Values could not be saved.'));
				$errors = $e->errors('chainMapSettings');
				$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));

				$view->set('data', array('jumpLogEnabled' => $_POST['jumpLogEnabled'],
										'jumpLogRecordNames' => $_POST['jumpLogRecordNames'], 
										'jumpLogRecordTime' => $_POST['jumpLogRecordTime'], 
										'jumpLogDisplayShipType' => $_POST['jumpLogDisplayShipType'], 
										'alwaysBroadcast' => $_POST['alwaysBroadcast'],
										'chain_map_show_actives_ships' => $_POST['chain_map_show_actives_ships']
									 ) 

				);
			}
		}

		$view->set('data', $group->as_array() );

		$this->template->content = $view;
	}	
   
	public function action_general()
	{
		$this->template->title = __('General settings');

		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$errors = array();
		$view = $this->template->content = View::factory('manage/settings/general');
		
		$view->bind('errors', $errors);
					
		if ($this->request->method() == "POST") 
		{
			try 
			{
				$group->groupName = $_POST['groupName'];
				$group->groupTicker = $_POST['groupTicker'];
				$group->authMode = $_POST['authMode'];
				$group->showSigSizeCol = intval($_POST['showSigSizeCol']);
				$group->statsEnabled = intval($_POST['statsEnabled']);
				
				if( !empty($_POST['password']) && !empty($_POST['password_confirm']) )
				{
						if( $_POST['password'] == $_POST['password_confirm'] )
						{
								$group->authPassword = sha1($_POST['password'].$group->authSalt);
						}
						else
						{
								Message::add( 'error', __('Error: The password was not saved because it did not match between the two fields.') );
						}
				}
			
				$homeSystems = trim($_POST['homeSystems']);
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
												$id = miscUtils::findSystemByName(trim($v));
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
						$group->homeSystemIDs = implode(',', $homeSystemIDs);
						$group->homeSystems = implode(',', $homeSystems);
				}
				else
				{
					$group->homeSystems = '';
					$group->homeSystemIDs = '';
				}
				
				$group->recordJumps = intval($_POST['recordJumps']);
				$group->skipPurgeHomeSigs = intval($_POST['skipPurgeHomeSigs']);
				$group->sysListShowReds = intval($_POST['sysListShowReds']);
				
				$group->save();
					
				Message::add('success', __('Settings saved.'));
				//$this->__recacheCorpMembers();
				groupUtils::recacheGroup( Auth::$user->data['groupID'] );
				HTTP::redirect('manage/settings/general');
				return;
			} 
			catch (ORM_Validation_Exception $e) 
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				Message::add('error', __('Error: Values could not be saved.'));
				$errors = $e->errors('editSubGroup');
				$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
				// Pass on the old form values

				$view->set('data',array('groupName' => $_POST['groupName'],
										'groupTicker' => $_POST['groupTicker'], 
										'authMode' => $_POST['authMode'], 
										'homeSystems' => $_POST['homeSystems'], 
										'recordJumps' => $_POST['recordJumps'], 
										'skipPurgeHomeSigs' => $_POST['skipPurgeHomeSigs'],
										'sysListShowReds' => $_POST['sysListShowReds']
									 ) 
				);
			}
		}

		$view->set('data', $group->as_array() );

		$this->template->content = $view;
	}
  
    public function action_noaccess() 
    {
		$this->template->title = __('Access not allowed');
		$view = $this->template->content = View::factory('user/noaccess');
    }

}