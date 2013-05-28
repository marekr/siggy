<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';
/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Controller_Manage_Group extends Controller_App {

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

   /** Controls access for separate actions
    *
    *  See Controller_App for how this implemented.
    *
    *  Examples:
    * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
    * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    */
   public $secure_actions = array(
      // user actions
      'members' => array('login','gadmin')
      // the others are public (forgot, login, register, reset, noaccess)
      // logout is also public to avoid confusion (e.g. easier to specify and test post-logout page)
      );

   // USER SELF-MANAGEMENT

   /**
    * View: Redirect admins to admin index, users to user profile.
    */
   public function action_index() 
   {
      if( Auth::$user->isGroupAdmin() ) 
      {
         $this->request->redirect('manage/group/dashboard');
      } else 
      {
         $this->request->redirect('account/overview');
      }
   }
   
   public function action_dashboard()
   {
      $this->template->title = __('Manage');
      $view = View::factory('manage/group/dashboard');
      
      
		$news = DB::query(Database::SELECT, "SELECT * FROM announcements WHERE visibility = 'manage' OR visibility = 'all' ORDER BY datePublished DESC LIMIT 0,3")
										->execute()->as_array();
		
      $view->bind('news', $news);
      
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

   public function action_members() 
   {
      $this->template->title = __('Group management');
      
      $view = $this->template->content = View::factory('manage/group/members');
      
      $view->set('user', Auth::$user->data );
      
      $group = ORM::factory('group', Auth::$user->data['groupID']);
      $view->set('group', $group );
   }
   
   public function action_subgroups() 
   {
      $this->template->title = __('Group management');
      
      $view = $this->template->content = View::factory('manage/group/subgroups');
      
      $view->set('user', Auth::$user->data);
      
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
									$this->request->redirect('manage/group/members');
									return;
							} 
							catch (ORM_Validation_Exception $e) 
							{
								//something broke, restart because our form is currently idiot proof anyway
								$this->request->redirect('manage/group/addMember');
							}
						}
						else if ( $action == 'doForm' )
						{
							if( empty($_POST['eveID']) || empty($_POST['accessName']) || empty($_POST['memberType']) )
							{
								//bad request
								$this->request->redirect('manage/group/addMember');
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
							$this->request->redirect('manage/group/addMember');
						}
						
					}
					else
					{
						//invalid
						$this->request->redirect('manage/group/addMember');
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
									$this->request->redirect('manage/group/members');
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
	
	
	public function action_chainMapSettings()
	{
		$this->template->title = __('Chain Map settings');

		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$errors = array();
		$view = $this->template->content = View::factory('manage/group/chainMapSettings');
		
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
							
							$group->save();
							
						Message::add('success', __('Chain map settings saved.'));
						
						groupUtils::recacheGroup( Auth::$user->data['groupID'] );
						
						$this->request->redirect('manage/group/chainMapSettings');
						return;
				} 
				catch (ORM_Validation_Exception $e) 
				{
					Message::add('error', __('Error: Values could not be saved.'));
					$errors = $e->errors('chainMapSettings');
					$errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));

					$view->set('data', array( 'jumpLogEnabled' => $_POST['jumpLogEnabled'],
																			'jumpLogRecordNames' => $_POST['jumpLogRecordNames'], 
																			'jumpLogRecordTime' => $_POST['jumpLogRecordTime'], 
																			'jumpLogDisplayShipType' => $_POST['jumpLogDisplayShipType'], 
																			'alwaysBroadcast' => $_POST['alwaysBroadcast']
																		 ) 

					);
				}
			}

			$view->set('data', $group->as_array() );

			$this->template->content = $view;      
	  
	}	
   
	public function action_settings()
	{
		$this->template->title = __('Group settings');

		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$errors = array();
		$view = $this->template->content = View::factory('manage/group/settings');
		
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
						$this->request->redirect('manage/group/settings');
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

					$view->set('data', array( 'groupName' => $_POST['groupName'],
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
   
	private function __findSystemByName($name)
	{
			$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
																->param(':name', $name )->execute()->get('id', 0);
																
			
			return $systemID;
	}   
   
   public function action_addSubGroup()
   {
			$this->template->title = __('Subgroup management');

			$group = ORM::factory('group', Auth::$user->data['groupID']);

			$errors = array();
			$data = array('sgName' => '',
							'sgHomeSystems' => '',
							'sgSysListShowReds' => 1,
							'sgSkipPurgeHomeSigs' => 0,
							);
			$view = View::factory('manage/group/subGroupForm');
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


							$systems = DB::select('id')->from('solarsystems')->order_by('id', 'ASC')->execute()->as_array();

							foreach($systems as $system)
							{		
								DB::query(Database::INSERT, 'INSERT INTO activesystems (`systemID`,`groupID`,`subGroupID`) VALUES(:systemID, :groupID, :subGroupID) ON DUPLICATE KEY UPDATE systemID=systemID')
																		->param(':systemID', $system['id'] )->param(':groupID', $sg->groupID )->param(':subGroupID', $sg->subGroupID )->execute();
							}

							//$this->__recacheCorpMembers();
							groupUtils::recacheSubGroup($sg->subGroupID);
							$this->request->redirect('manage/group/subgroups');
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
   
   public function action_editMember()
   {
			$id = $this->request->param('id');
			
			$this->template->title = __('Group management');

			$member = ORM::factory('groupmember', $id);
			if( $member->groupID != Auth::$user->data['groupID'] )
			{
					Message::add('error', __('Error: You do not have permission to edit that group member.'));
					$this->request->redirect('manage/group/members');
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
						$this->request->redirect('manage/group/members');
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
   
		public function action_editSubGroup()
		{
				$id = intval($this->request->param('id'));
					
				$this->template->title = __('Editing sub group');

				$sg = ORM::factory('subgroup', $id);
				if( $sg->groupID != Auth::$user->data['groupID'] )
				{
				 Message::add('error', __('Error: You do not have permission to edit that subgroup.'));
				 $this->request->redirect('manage/group/subgroups');
				}
				$group = ORM::factory('group', Auth::$user->data['groupID']);

				$errors = array();

				$view = View::factory('manage/group/subGroupForm');
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
								$this->request->redirect('manage/group/subgroups');
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
   
	public function action_removeMember()
	{
		$id = intval($this->request->param('id'));
	
		$this->template->title = __('Group management');

		$member = ORM::factory('groupmember', $id);
		if( $member->groupID != Auth::$user->data['groupID'] )
		{
				Message::add('error', __('Error: You do not have permission to remove that group member.'));
				$this->request->redirect('manage/group/members');
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
						$this->request->redirect('manage/group/members');
						return;
				} 
				catch (Exception $e) 
				{
						Message::add('error', __('Error: Removal of member failed for unknown reasons.'));
				}
		}

		$view->set('data', $member->as_array() );

		$this->template->content = $view;
	}
   
	public function action_removeSubGroup()
	{
		$id = intval($this->request->param('id'));
		
			$this->template->title = __('Removing sub group');

			$sg = ORM::factory('subgroup', $id);
			if( $sg->groupID != Auth::$user->data['groupID'] )
			{
					Message::add('error', __('Error: You do not have permission to remove that subgroup.'));
					$this->request->redirect('manage/group/subgroups');
			}

			$view = View::factory('manage/group/deleteSubGroupForm');
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
							$this->request->redirect('manage/group/subgroups');
							return;
					} 
					catch (Exception $e) 
					{
							Message::add('error', __('Error: Removal of member failed for unknown reasons.'));
					}
			}

			$view->set('data', $sg->as_array() );

			$this->template->content = $view;
	}   
}