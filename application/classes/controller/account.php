<?php

require_once APPPATH.'classes/access.php';

class Controller_Account extends Controller_Template
{
		private $auth;
		private $user;
		public $template = 'template/account';

		function __construct(Kohana_Request $request, Kohana_Response $response)
		{
		
			Cookie::$salt = 'y[$e.swbDs@|Gd(ndtUSy^';
			$this->igb = $this->isIGB();
			$this->trusted = $this->getTrust();	
			
			$this->auth = simpleauth::instance();
			$this->user = $this->auth->get_user();
			
			parent::__construct($request, $response);
		}

		
		public function action_register()
		{
				$this->template->title = __('siggy: register a new account');
				
				if( isset( $_POST['register'] ) )
				{
							$errors = array();
							
							if( empty( $_POST['username'] ) )
							{
									$errors['username'] = 'Username must not be empty.';
							}
							if( empty( $_POST['email'] ) )
							{
									$errors['email'] = 'A valid email address is required.';
							}
							if( empty( $_POST['password'] ) )
							{
									$errors['password'] = 'You must enter a password.';
							}
							if( empty( $_POST['password_confirm'] ) )
							{
									$errors['password_confirm'] = 'You must confirm your selected password.';
							}
							
							if( strlen( $_POST['password'] ) <= 6 )
							{
									$errors['password'] = 'Password must be at least 6 characters long';
							}
							
							if( $_POST['password'] != $_POST['password_confirm'] )
							{
									$errors['password_confirm'] = 'The password did not match the one above.';
							}
							
							if( $this->auth->username_exists( $_POST['username'] ) )
							{
									$errors['username'] = 'Username is already in use.';
							}
							
							if( $this->auth->email_exists( $_POST['email'] ) )
							{
									$errors['username'] = 'Username is already in use.';
							}
							
							if( count($errors) > 0 )
							{
									$view = View::factory('siggy/register');
									// Get errors for display in view
									// Note how the first param is the path to the message file (e.g. /messages/register.php)
									// Move external errors to main array, for post helper compatibility
									$view->set('errors', $errors);
									// Pass on the old form values
									$_POST['password'] = $_POST['password_confirm'] = '';
									$view->set('defaults', $_POST);
									$this->template->content = $view;
							}
							else
							{
									$userData = array(
																	 'username' => $_POST['username'],
																	 'password' => $_POST['password'],
																	 'email' => $_POST['email'],
																	 'active' => 1 
																	 );
									if( $this->auth->create_user( $userData, TRUE ) )
									{
									// sign the user in
										$this->auth->login($_POST['username'], $_POST['password']);
										$this->request->redirect('account/setAPI');
									// redirect to the user account
									}
									else
									{
											$view = View::factory('siggy/register');
											$errors['username'] = 'Unknown error has occured.';
											$view->set('errors', $errors);
											// Pass on the old form values
											$_POST['password'] = $_POST['password_confirm'] = '';
											$view->set('defaults', $_POST);
											$this->template->content = $view;
									}
							}

				}
				else
				{
						$view = View::factory('siggy/register');
						$this->template->content = $view;
				}
		}
		
		public function action_setAPI()
		{
				if( !$this->auth->logged_in() )
				{
						$this->request->redirect('/');
						return;
				}
				
				$this->template->title = __('siggy: set api key');
		
		
				if( $this->user->apiID == 0 || $this->user->apiKey == '' )
				{
						$status = 'missing';
				}
				elseif( $this->user->apiFailures > 3 )
				{
						$status = 'failed';
				}
				elseif ( $this->user->apiInvalid )
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
									PhealConfig::getInstance()->api_base = 'http://api.eveonline.com/';
									PhealConfig::getInstance()->api_customkeys = true;
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
													$this->auth->update_user( $this->user->id, array('apiID' => intval($_POST['apiID']), 'apiKey' => $_POST['apiKey'], 'apiLastCheck' => 0,'apiInvalid' => 0, 'apiFailures' => 0 ) );
													$this->auth->reload_user();
													
													$this->request->redirect('/account/characterSelect');
											}
											else
											{
													$errors['apiKey'] = 'The API key does not provide the proper access to CharacterInfo(private) and CharacterInfo(Public).';
													$view = View::factory('siggy/setAPI');
													$view->set('status', $status);
													$view->set('errors', $errors);
													$this->template->content = $view;
											}
									}
									else
									{
											$errors['apiKey'] = 'The API key is invalid.';
											$view = View::factory('siggy/setAPI');
											$view->set('status', $status);
											$view->set('errors', $errors);
											$this->template->content = $view;
									}
							}
							else
							{
									$view = View::factory('siggy/setAPI');
									$view->set('status', $status);
									$view->set('errors', $errors);
									$this->template->content = $view;
							}
				}
				else
				{							
							$view = View::factory('siggy/setAPI');
							$view->set('status', $status);
							$view->set('errors', $errors);
							$this->template->content = $view;
				}
		}

		function bitMask($mask = 0) {
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
				if( !$this->auth->logged_in() )
				{
						$this->request->redirect('/');
						return;
				}		
				
				$this->template->title = __('siggy: change password');
				$view = $this->template->content = View::factory('siggy/changePassword');
				
				$errors = array();
				if( isset($_POST['confirm']) )
				{						
							
							if( empty( $_POST['current_password'] ) || ($this->auth->hash($_POST['current_password']) != $this->user->password)  )
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
								 $this->auth->update_user( $this->user->id, array('password' => $_POST['password'] ) );
								 $this->auth->reload_user();
								 
								 $this->request->redirect('/');
							}
							else
							{
									$view->errors = $errors;
							}
				}		
		}
		
		public function action_changeEmail()
		{
				if( !$this->auth->logged_in() )
				{
						$this->request->redirect('/');
						return;
				}		
				
				$this->template->title = __('siggy: change email');
		}
		
		public function action_forgotPassword()
		{
				if( $this->auth->logged_in() )
				{
						$this->request->redirect('/');
						return;
				}		
				
				$this->template->title = __('siggy: forgot password');
				
				if( isset( $_POST['send'] ) )
				{
						$errors = array();
						if( empty( $_POST['reset_email'] ) )
						{
								$errors['reset_email'] = 'You must enter a valid email address.';
						}
						
						if( count( $errors ) ) 
						{
								$view = $this->template->content = View::factory('siggy/forgotPassword');
								$view->errors = $errors;
						}
						else
						{
								$user = new Model_Auth_Users;
								$user->getUserByEmail($_POST['reset_email']);
								if ( is_numeric( $user->id ) ) 
								{
										// send an email with the account reset token
										$user->reset_token = $this->auth->generatePassword(32);
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
										$from = Kohana::config('useradmin')->email_address;
										
										$body =  __($message, array(
												':reset_token_link' => URL::site('account/completePasswordReset?reset_token='.$user->reset_token.'&reset_email='.$_POST['reset_email'], TRUE),
												':reset_link' => URL::site('account/completePasswordReset', TRUE),
												':reset_token' => $user->reset_token,
												':username' => $user->username
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
						}
						
				
				}
				else
				{
						$view = $this->template->content = View::factory('siggy/forgotPassword');
				}
				
		}
		
		public function action_completePasswordReset()
		{
				if( $this->auth->logged_in() )
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
								$view = $this->template->content = View::factory('siggy/completePasswordReset');
								$view->errors = $errors;
						}
						else
						{
								
								$user = new Model_Auth_Users;
								$user->getUserByEmail($_REQUEST['reset_email']);
								
								if( $user->reset_token != $_REQUEST['reset_token'] )
								{
										$errors['reset_token'] = 'The reset token you have entered is invalid';
										$view = $this->template->content = View::factory('siggy/completePasswordReset');
										$view->errors = $errors;
								}
								else if ( is_numeric($user->id) && ($user->reset_token == $_REQUEST['reset_token']) ) 
								{
										$password = $this->auth->generatePassword();
										$this->auth->update_user( $user->id, array('password' => $password,'reset_token' => '' ) );

										$message = "You have completed the password reset process. Please use the following randomly generated password to login upon which you may change it to anything you desire\n\n"
										.":password\n\n"
										."You may login at:\n"
										.":url\n";
										
										$mailer = Email::connect();
										// Create complex Swift_Message object stored in $message
										// MUST PASS ALL PARAMS AS REFS
										$subject = __('siggy: Your new password');
										
										$to = $_REQUEST['reset_email'];
										$from = Kohana::config('useradmin')->email_address;
										
										$body =  __($message, array(
												':url' => URL::site('', TRUE),
												':password' => $password
										));
										
										$message_swift = Swift_Message::newInstance($subject, $body)
														->setFrom($from)
														->setTo($to);
										
										$mailer->send($message_swift);																			
										
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
						$view = $this->template->content = View::factory('siggy/completePasswordReset');
				}
				
		}
		
		public function action_noAPIAccess()
		{
				if( !$this->auth->logged_in() )
				{
						$this->request->redirect('/');
						return;
				}
				$this->template->title = __('siggy: no access');
						
				$view = View::factory('siggy/noAPIAccess');
				
				if( $this->user->apiID > 0 && ( $this->user->apiCharID == 0 || $this->user->apiCorpID == 0 ) )
				{
						//char select
						$view->messageType = 'selectChar';
				}
				elseif ( $this->user->apiID == 0 )
				{
						$view->messageType = 'missingAPI';
				}
				elseif( $this->user->apiInvalid == 1 )
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
				if( !$this->auth->logged_in() )
				{
						$this->request->redirect('/');
						return;
				}		
		
				if( !($this->user->apiID > 0) || $this->user->apiKey == '' )
				{
						$this->request->redirect('/account/setAPI');
				}
				
				$this->template->title = __('siggy: character selection');
				
				require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
				spl_autoload_register( "Pheal::classload" );
				PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
				PhealConfig::getInstance()->http_ssl_verifypeer = false;
				$pheal = new Pheal( $this->user->apiID, $this->user->apiKey );
				
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
							$this->auth->update_user( $this->user->id, array('apiCorpID' => $chars[ $charID ]['corpID'], 'apiCharName' => $chars[ $charID ]['name'], 'apiCharID' => $charID, 'apiLastCheck' => 0,'apiInvalid' => 0, 'apiFailures' => 0 ) );
							$this->auth->reload_user();
							
							$this->request->redirect('/');
					}
					else
					{
							$view = View::factory('siggy/characterSelect');
							$view->chars = $chars;
							$this->template->content = $view;
					}
				}
				else
				{
						$view = View::factory('siggy/characterSelect');
						$view->chars = $chars;
						$this->template->content = $view;
				}
		}
		
		public function action_login()
		{
				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				$this->template->title = __('siggy: login required');
				
				if( $this->auth->logged_in() )
				{
					return;
				}
				
				if( isset($_POST['login']) )
				{
						if( $this->auth->login($_POST['username'], $_POST['password']) )
						{
								$this->request->redirect('/');
						}
						else
						{
								$view = View::factory('siggy/userLogin');
								$view->invalidLogin = true;
								$view->set('username', $_POST['username']);
								 $this->template->content = $view;
						}
				}
				else
				{
						$view = View::factory('siggy/userLogin');
						$view->invalidLogin = false;
						 $this->template->content =$view;
				}
		}
	

		public function action_logout()
		{
				// Sign out the user
				$this->auth->logout();

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


		private function isIGB() 
		{
				if ( isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'EVE-IGB') !== false ) 
				{
						return TRUE;
				}
				return FALSE;
		}
			
		private function getTrust() 
		{
				if ( $this->isIGB() ) 
				{
					 //because CCP cant use integers.
					 if (strtolower($_SERVER['HTTP_EVE_TRUSTED'])=='no') 
					 {
							return false;
					 } 
					 else 
					 {
							return true;
					 }
				}
				return false;
		}
	

}