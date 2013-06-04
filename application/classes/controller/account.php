<?php

require_once APPPATH.'classes/FrontController.php';
require_once APPPATH.'classes/access.php';

class Controller_Account extends FrontController
{
		private $auth;
		private $user;
		public $template = 'template/public';
		protected $noAutoAuthRedirects = true;

		function __construct(Kohana_Request $request, Kohana_Response $response)
		{
			parent::__construct($request, $response);
		}
		
		public function before()
		{
			switch( $this->request->action() )
			{
				case 'login':
				case 'register':
				case 'forgotPassword':
				case 'completePasswordReset':
					break;
				default:
					if( !Auth::loggedIn() )
					{
						$this->request->redirect('/account/login');
					}
					break;
			}
			parent::before();
		}
		
		public function after()
		{
			switch( $this->request->action() )
			{
				case 'login':
				case 'forgotPassword':
				case 'completePasswordReset':
					$this->template->selectedTab = 'login';
					$this->template->layoutMode = 'blank';
					break;
				case 'register':
					$this->template->selectedTab = 'register';
					$this->template->layoutMode = 'blank';
					break;
				default: 
					$this->template->layoutMode = 'leftMenu';
					$this->template->selectedTab = 'account';
					$view = View::factory('templatebits/accountMenu');
					$this->template->leftMenu = $view;
					break;
			}
			
			$this->template->loggedIn = Auth::loggedIn();
			$this->template->user = Auth::$user->data;
			
		
			parent::after();
		}
		
		public function action_overview()
		{
			$this->template->title = "Account overview";
			
			$view = View::factory('account/overview');
			$view->user = Auth::$user->data;
			
			$this->template->content = $view;
		}
		
		public function action_changeEmailAddress()
		{
				$this->template->title = __('Change Email Address');
				
				$this->template->content = View::factory('account/changeEmail');
		}
		
		public function action_register()
		{
				$this->template->title = __('Register a new account');
				
				if( Auth::loggedIn() )
				{
					$this->request->redirect('/account');
				}
				
				$errors = array();
				
				if ($this->request->method() == "POST") 
				{
					$validator = Validation::factory($_POST)
								->rule('username', 'not_empty')
								->rule('email', 'not_empty')
								->rule('password', 'not_empty')
								->rule('password', 'min_length', array(':value', '6'))
								->rule('password_confirm',  'matches', array(':validation', 'password_confirm', 'password'));
								
								
					if ($validator->check())
					{
							
							if( Auth::usernameExists( $_POST['username'] ) )
							{
									$errors['username'] = 'Username is already in use.';
							}
							
							if( Auth::emailExists( $_POST['email'] ) )
							{
									$errors['email'] = 'Email is already in use.';
							}
							
					
							if( empty( $errors ) )
							{
									$userData = array(
																	 'username' => $_POST['username'],
																	 'password' => $_POST['password'],
																	 'email' => $_POST['email'],
																	 'active' => 1,
																	 'created' => time()
																	 );
									if( Auth::createUser( $userData ) )
									{
											Auth::processLogin($_POST['username'], $_POST['password']);
											$this->request->redirect('account/setAPI');
									}
									else
									{
											$errors['username'] = 'Unknown error has occured.';
									}
							}

							
					}
					else
					{
							$errors = $validator->errors('account/register');
					}
				}				
				
				$this->template->content =  View::Factory('account/register')->bind('errors', $errors);
		}
		
		public function action_setAPI()
		{
				if( !Auth::loggedIn() )
				{
						$this->request->redirect('/');
						return;
				}
				
				$this->template->title = __('siggy: set api key');
		
		
				if( Auth::$user->data['apiID'] == 0 || Auth::$user->data['apiKey'] == '' )
				{
						$status = 'missing';
				}
				elseif( Auth::$user->data['apiFailures'] > 3 )
				{
						$status = 'failed';
				}
				elseif ( Auth::$user->data['apiInvalid'] )
				{
						$status = 'invalid';
				}
				else
				{
						$status = 'good';
				}
				
				$errors = array();
				
				if( isset($_POST['set']) )
				{
							if( empty( $_POST['apiID'] ) )
							{
									$errors['apiID'] = 'An API ID must be provided';
							}
							
							if( empty( $_POST['apiKey'] ) )
							{
									$errors['apiKey'] = 'An API key must be provided';
							}
							
							if( !(count($errors) > 0 ) )
							{
									require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
									spl_autoload_register( "Pheal::classload" );
									PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
									PhealConfig::getInstance()->http_ssl_verifypeer = false;
									$pheal = new Pheal( $_POST['apiID'], $_POST['apiKey'] );
									
									
									try 
									{
											$result = $pheal->accountScope->APIKeyInfo();
											
											$success = true;
									}
									catch(PhealException $e)
									{    
											$success = false;
									}
									
									
									if( $success )
									{
											define('CHARINFO_PRIV', 16777216);
											define('CHARINFO_PUB', 8388608);

											$accessMask = $this->bitMask($result->key->accessMask);
											if( in_array( CHARINFO_PRIV, $accessMask ) && in_array( CHARINFO_PUB, $accessMask ) )
											{
													//$this->auth->update_user( Auth::$user['id'], array('apiID' => intval($_POST['apiID']), 'apiKey' => $_POST['apiKey'], 'apiLastCheck' => 0,'apiInvalid' => 0, 'apiFailures' => 0 ) );
													//$this->auth->reload_user();
													
													Auth::$user->data['apiID'] = intval($_POST['apiID']);
													Auth::$user->data['apiKey'] = $_POST['apiKey'];
													Auth::$user->data['apiLastCheck'] = 0;
													Auth::$user->data['apiInvalid'] = 0;
													Auth::$user->data['apiFailures'] = 0;
													
													Auth::$user->save();
													
													$this->request->redirect('/account/characterSelect');
											}
											else
											{
													$errors['apiKey'] = 'The API key does not provide the proper access to CharacterInfo(private) and CharacterInfo(Public).';
											}
									}
									else
									{
											$errors['apiKey'] = 'The API key is invalid.';
									}
							}
				}
				$view = View::factory('account/setAPI');
				$view->set('status', $status);
				$view->set('errors', $errors);
				$this->template->content = $view;
		}

		function bitMask($mask = 0)
		{
				$return = array();
				while ($mask > 0) {
						for($i = 0, $n = 0; $i <= $mask; $i = 1 * pow(2, $n), $n++) {
								$end = $i;
						}
						$return[] = $end;
						$mask = $mask - $end;
				}
				sort($return);
				return $return;
		}
		
		public function action_changePassword()
		{
				if( !Auth::loggedIn() )
				{
						$this->request->redirect('/');
						return;
				}		
				
				$this->template->title = __('siggy: change password');
				$view = View::factory('account/changePassword');
				
				$errors = array();
				if ($this->request->method() == "POST") 
				{						
							
							if( empty( $_POST['current_password'] ) || (Auth::hash($_POST['current_password']) != Auth::$user->data['password'])  )
							{
									$errors['current_password'] = 'This is not current password.';
							}
							
							if( empty( $_POST['password'] ) )
							{
									$errors['password'] = 'You must enter a new password.';
							}
							if( empty( $_POST['password_confirm'] ) )
							{
									$errors['password_confirm'] = 'You must confirm your new selected password.';
							}
							if( $_POST['password'] != $_POST['password_confirm'] )
							{
									$errors['password_confirm'] = 'The password did not match the new one above.';
							}
							
							if( !count( $errors ) )
							{
								 Auth::$user->updatePassword($_POST['password']);
								 
								 $this->request->redirect('/');
							}
				}
				
				$view->bind('errors',$errors);
				$this->template->content = $view;
		}
		
		public function action_changeEmail()
		{
				if( !Auth::loggedIn() )
				{
						$this->request->redirect('/');
						return;
				}		
				
				$this->template->title = __('siggy: change email');
		}
		
		public function action_forgotPassword()
		{
				if( Auth::loggedIn() )
				{
						$this->request->redirect('/');
						return;
				}		
				
				$this->template->title = __('siggy: forgot password');
				
				$errors = array();
				if ($this->request->method() == "POST") 
				{
						if( empty( $_POST['reset_email'] ) )
						{
								$errors['reset_email'] = 'You must enter a valid email address.';
						}
						
						if( !count( $errors ) ) 
						{
								$user = new User();
								$user->loadByEmail($_POST['reset_email']);
								

								if ( isset( $user->data['id'] ) ) 
								{
										// send an email with the account reset token
										$user->data['reset_token'] = Auth::generatePassword(32);
										$user->save();
										
										$message = "You have requested a password reset for your siggy account. To confirm the password reset, please the follow the proceeding url:\n\n"
										.":reset_token_link\n\n"
										."If the above link is not clickable, please visit the following page:\n"
										.":reset_link\n\n"
										."and copy/paste the following reset token: :reset_token\nYour user account name is: :username\n";
										
										$mailer = Email::connect();
										// Create complex Swift_Message object stored in $message
										// MUST PASS ALL PARAMS AS REFS
										$subject = __('siggy: Account password reset');
										
										$to = $_POST['reset_email'];
										$from = Kohana::$config->load('useradmin')->email_address;
										
										
										$body =  __($message, array(
												':reset_token_link' => URL::site('account/completePasswordReset?reset_token='.$user->data['reset_token'].'&reset_email='.$_POST['reset_email'], TRUE),
												':reset_link' => URL::site('account/completePasswordReset', TRUE),
												':reset_token' => $user->data['reset_token'],
												':username' => $user->data['username']
										));
										
										$message_swift = Swift_Message::newInstance($subject, $body)
														->setFrom($from)
														->setTo($to);
										
										try
										{
												$mailer->send($message_swift);									
										}
										catch( Exception $e )
										{
										}
								}
								
								$view = $this->template->content = View::factory('messages/forgotPasswordSent');
								$view->email = $_POST['reset_email'];
								return;
						}
						
				
				}
				
				$this->template->content = $view = View::factory('account/forgotPassword');
				$view->errors = $errors;
				
		}
		
		public function action_completePasswordReset()
		{
				if( Auth::loggedIn() )
				{
						$this->request->redirect('/');
						return;
				}		
				$this->template->title = __('siggy: complete password reset');
				
				if( isset( $_REQUEST['reset_token'] ) )
				{
						$errors = array();
						
						if( empty( $_REQUEST['reset_email'] ) )
						{
								$errors['reset_email'] = 'You must enter a valid email address.';
						}
						
						if( empty( $_REQUEST['reset_token'] ) )
						{
								$errors['reset_token'] = 'You must enter a valid reset token.';
						}
				
				
						if( count( $errors ) ) 
						{
								$view = $this->template->content = View::factory('account/completePasswordReset');
								$view->errors = $errors;
						}
						else
						{
								$user = new User();
								$user->loadByEmail($_REQUEST['reset_email']);
								
								if( $user->data['reset_token'] != $_REQUEST['reset_token'] )
								{
										$errors['reset_token'] = 'The reset token you have entered is invalid';
										$view = $this->template->content = View::factory('account/completePasswordReset');
										$view->errors = $errors;
								}
								else if ( isset($user->data['id']) && ($user->data['reset_token'] == $_REQUEST['reset_token']) ) 
								{
										$password = Auth::generatePassword();
										$user->data['reset_token'] = '';
										$user->updatePassword($password);

										$message = "You have completed the password reset process. Please use the following randomly generated password to login upon which you may change it to anything you desire\n\n"
										.":password\n\n"
										."You may login at:\n"
										.":url\n";
										
										$mailer = Email::connect();
										// Create complex Swift_Message object stored in $message
										// MUST PASS ALL PARAMS AS REFS
										$subject = __('siggy: Your new password');
										
										$to = $_REQUEST['reset_email'];
										$from = Kohana::$config->load('useradmin')->email_address;
										
										$body =  __($message, array(
												':url' => URL::site('', TRUE),
												':password' => $password
										));
										
										try
										{
										$message_swift = Swift_Message::newInstance($subject, $body)
														->setFrom($from)
														->setTo($to);
										
										$mailer->send($message_swift);																			
										}
										catch(Exception $e)
										{
											print_r($e);
										}
										$view = $this->template->content = View::factory('messages/passwordResetCompleteMessage');
								}
								else
								{
										die("something stupid has occured");
								}
						}
						
				}
				else
				{
						$view = $this->template->content = View::factory('account/completePasswordReset');
				}
				
		}
		
		public function action_noAPIAccess()
		{
				if( !Auth::loggedIn() )
				{
						$this->request->redirect('/');
						return;
				}
				$this->template->title = __('siggy: no access');
						
				$view = View::factory('siggy/noAPIAccess');
				
				if( Auth::$user->data['apiID'] > 0 && ( Auth::$user->data['apiCharID'] == 0 || Auth::$user->data['apiCorpID'] == 0 ) )
				{
						//char select
						$view->messageType = 'selectChar';
				}
				elseif ( Auth::$user->data['apiID'] == 0 )
				{
						$view->messageType = 'missingAPI';
				}
				elseif( Auth::$user->data['apiInvalid'] == 1 )
				{
						$view->messageType = 'badAPI';
				}
				else
				{
						$view->messageType = 'noAccess';
				}
				
				$this->template->content = $view;
		}
		
		
		public function action_characterSelect()
		{
				if( !Auth::loggedIn() )
				{
						$this->request->redirect('/');
						return;
				}		
		
				if( !( Auth::$user->data['apiID'] > 0) ||  Auth::$user->data['apiKey'] == '' )
				{
						$this->request->redirect('/account/setAPI');
				}
				
				
				$this->template->title = __('siggy: character selection');
				
				$charID =  Auth::$user->data['apiCharID'];
				
				require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
				spl_autoload_register( "Pheal::classload" );
				PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
				PhealConfig::getInstance()->http_ssl_verifypeer = false;
				$pheal = new Pheal( Auth::$user->data['apiID'],  Auth::$user->data['apiKey']);
				
				$corpList = $this->getCorpList();
				$charList = $this->getCharList();				
				
				$apiError = FALSE;
				try
				{
						//$result = $pheal->eveScope->CharacterInfo( array( 'characterID' => 460256976 )  );
					//	print_r($result);
						$chars = array();
						$result = $pheal->accountScope->Characters();
						
						foreach($result->characters as $char )
						{
								if( in_array($char->corporationID, $corpList) || in_array($char->characterID, $charList) )
								{
										$chars[ $char->characterID ] = array( 'name' => $char->name, 'corpID' => $char->corporationID, 'corpName' => $char->corporationName, 'charID' => $char->characterID );
								}
						}
						
				}
				catch(PhealAPIException $e)
				{
					$apiError = true;
				}
				
				
				if( isset($_POST['charID']) )
				{
					$charID = intval($_POST['charID']);
					
					if( $charID && isset( $chars[ $charID ] ) )
					{
							Auth::$user->data['apiCorpID'] = $chars[ $charID ]['corpID'];
							Auth::$user->data['apiCharName'] = $chars[ $charID ]['name'];
							Auth::$user->data['apiCharID'] = $charID;
							Auth::$user->data['apiLastCheck'] = 0;
							Auth::$user->data['apiInvalid'] = 0;
							Auth::$user->data['apiFailures'] = 0;
					
							Auth::$user->save();
							
							$this->request->redirect('/');
					}
				}
				
				$view = View::factory('account/characterSelect');
				$view->chars = $chars;
				$view->selectedCharID = $charID;
				$view->apiError = $apiError;
				$this->template->content = $view;
		}
		
		public function action_login()
		{
				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				$this->template->title = __('siggy: login required');
				
				if( Auth::loggedIn() )
				{
					$this->request->redirect('/');
				}
				
				if( isset($_POST['login']) )
				{
						$rememberMe = (isset($_POST['rememberMe']) ? TRUE : FALSE);
						if( Auth::processLogin($_POST['username'], $_POST['password'], $rememberMe) === Auth::LOGIN_SUCCESS )
						{
								if( isset($_REQUEST['bounce'] ) )
								{
									$this->request->redirect(URL::base(TRUE, TRUE) . $_REQUEST['bounce']);
								}
								else
								{
									$this->request->redirect('/');
								}
						}
						else
						{
								$view = View::factory('account/login');
								$view->invalidLogin = true;
								$view->bounce = isset($_REQUEST['bounce']) ? $_REQUEST['bounce'] : '';
								$view->set('username', $_POST['username']);
								 $this->template->content = $view;
						}
				}
				else
				{
						$view = View::factory('account/login');
						$view->invalidLogin = false;
						$view->bounce = isset($_REQUEST['bounce']) ? $_REQUEST['bounce'] : '';
						$this->template->content =$view;
				}
		}
	

		public function action_logout()
		{
				// Sign out the user
				Auth::processLogout();

				$this->request->redirect('/');
		}			
		

		public function getCorpList()
		{
				$cache = Cache::instance(CACHE_METHOD);
				
				$corpList = $cache->get('corpList');
				
				if( $corpList != null )
				{
						return $corpList;
				}
				else
				{
						$corps = DB::query(Database::SELECT, "SELECT eveID FROM groupmembers WHERE memberType='corp'")
														->execute()->as_array('eveID');  
						
						$corpList = array();
						foreach($corps as $c)
						{
							$corpList[] = $c['eveID'];
						}
						
						$corpList = array_unique( $corpList );
						
						$cache->set('corpList', $corpList);    	
								
						return $corpList;
				}
		}		

		public function getCharList()
		{
				$cache = Cache::instance(CACHE_METHOD);
				
				$charList = $cache->get('charList');
				
				if( $charList != null )
				{
						return $charList;
				}
				else
				{
						$chars = DB::query(Database::SELECT, "SELECT eveID FROM groupmembers WHERE memberType='char'")
														->execute()->as_array('eveID');  
						
						$charList = array();
						foreach($chars as $c)
						{
							$charList[] = $c['eveID'];
						}
						
						$cache->set('charList', $charList);    	
								
						return $charList;
				}
		}			


	

}