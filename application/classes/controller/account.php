<?php

require Kohana::find_file('vendor', 'OAuth\bootstrap');
use OAuth\OAuth2\Service\Eve;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

require_once APPPATH.'classes/FrontController.php';

class Controller_Account extends FrontController
{
	private $auth;
	private $user;
	public $template = 'template/public_bootstrap32';
	protected $noAutoAuthRedirects = true;

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}
	
	public function before()
	{
		switch( $this->request->action() )
		{
			case 'sso':
			case 'login':
			case 'logout':
			case 'register':
			case 'forgotPassword':
			case 'completePasswordReset':
				break;
			default:
				if( !Auth::loggedIn() )
				{
					HTTP::redirect('/account/login');
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
				$view = View::factory('account/menu');
				$this->template->leftMenu = $view;
				break;
		}

		$this->template->loggedIn = Auth::loggedIn();
		$this->template->user = Auth::$user->data;

		parent::after();
	}
	
	public function action_sso()
	{
		$sso_type = $this->request->param('id');
		
		if( $sso_type == 'eve' )
		{
			/** @var $serviceFactory \OAuth\ServiceFactory An OAuth service factory. */
			$serviceFactory = new \OAuth\ServiceFactory();
			// Session storage
			$storage = new Session();

					
			/**
			 * Create a new instance of the URI class with the current URI, stripping the query string
			 */
			$uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
			$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
			$currentUri->setQuery('');
			
			
			$credentials = null;
			if( Kohana::$environment == Kohana::PRODUCTION )
			{
				$credentials = new Credentials(
					Kohana::$config->load('sso.production.eve.key'),
					Kohana::$config->load('sso.production.eve.secret'),
					 URL::base(TRUE).'account/sso/eve'
				);
			}
			else
			{
				$credentials = new Credentials(
					Kohana::$config->load('sso.development.eve.key'),
					Kohana::$config->load('sso.development.eve.secret'),
					 URL::base(TRUE).'account/sso/eve'
				);
			}

			$eveService = $serviceFactory->createService('Eve', $credentials, $storage);
			if ( !empty($_GET['code']) )
			{
				// retrieve the CSRF state parameter
				$state = isset($_GET['state']) ? $_GET['state'] : null;

				// This was a callback request from reddit, get the token
				$eveService->requestAccessToken($_GET['code'], $state);

				$result = json_decode($eveService->request('https://login.eveonline.com/oauth/verify'), true);

				//find username by CharacterOwnerHash
				if( !is_array($result) )
				{
					HTTP::redirect('/');
				}
				
				$fakeEmail = $result['CharacterOwnerHash'].'@eveonline.com';
				if( $userID = Auth::usernameExists( $fakeEmail ) )
				{
					$fakeEmail = $result['CharacterOwnerHash'].'@eveonline.com';
					$status = Auth::processLogin($fakeEmail, $result['CharacterOwnerHash']);
					HTTP::redirect('/');
				}
				else
				{
					
					$data = array( 'email' => $fakeEmail,
									'username' => $fakeEmail,
									'char_id' => $result['CharacterID'],
									'char_name' => $result['CharacterName'],
									'password' => $result['CharacterOwnerHash'],		//we aren't using password but this is a good placeholder (non blank)
									'provider' => 1			//provider 1 == eve sso for now
									);
									
					
					if( User::create( $data ) )
					{
						Auth::processLogin($fakeEmail, $result['CharacterOwnerHash']);
						HTTP::redirect('/');
					}
					else
					{
						$errors['username'] = 'Unknown error has occured.';
					}
				}
			}
			else
			{
				HTTP::redirect($eveService->getAuthorizationUri());
			} 
		}
		
		exit();
	}
		
	public function action_overview()
	{
		if( !Auth::$user->isLocal() )
		{
			HTTP::redirect('/');
		}


		$this->template->title = "Account overview";

		$view = View::factory('account/overview');
		$view->user = Auth::$user->data;

		$this->template->content = $view;
	}

	public function action_register()
	{
		$this->template->title = __('Register a new account');
		
		if( Auth::loggedIn() )
		{
			HTTP::redirect('/account');
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
					$userData = array('username' => $_POST['username'],
									 'password' => $_POST['password'],
									 'email' => $_POST['email'],
									 'active' => 1,
									 'created' => time()
									 );

					if( User::create( $userData ) )
					{
						Auth::processLogin($_POST['username'], $_POST['password']);
						HTTP::redirect('account/apiKeys');
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
		
	public function action_apiKeys()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}
		
		if( !Auth::$user->isLocal() )
		{
			HTTP::redirect('/');
		}
		
		$view = View::factory('account/apiKeys');
		$view->set('keys', Auth::$user->getAPIKeys());
		$this->template->content = $view;
		$this->template->title = 'siggy - set api';
	}
		
	public function action_addAPI()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}
		
		if( !Auth::$user->isLocal() )
		{
			HTTP::redirect('/');
		}

		$this->apiKeyForm('add');
	}
	
	
	public function action_editAPI()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}
		
		if( !Auth::$user->isLocal() )
		{
			HTTP::redirect('/');
		}

		$this->apiKeyForm('edit');
	}
		
	public function action_removeAPI()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}
		
		if( !Auth::$user->isLocal() )
		{
			HTTP::redirect('/');
		}
		
		
		$entryID = intval($this->request->param('id',0));
		
		
		$keyData = DB::query(Database::SELECT, "SELECT * FROM apikeys WHERE entryID=:entryID AND userID=:userID")
										->param(':entryID', $entryID)->param(':userID', Auth::$user->data['id'])
										->execute()->current();				
		if( !isset($keyData['entryID']) )
		{
			HTTP::redirect('/account/apiKeys');
		}
		
		DB::delete('apikeys')->where('entryID', '=', $entryID)->execute();
		
		if( Auth::$user->data['selected_apikey_id'] == $keyData['entryID'] )
		{
			Auth::$user->data['corp_id'] = 0;
			Auth::$user->data['char_id'] = 0;
			Auth::$user->data['char_name'] = '';
			Auth::$user->data['selected_apikey_id'] = 0;
			
			Auth::$user->save();
		}
		HTTP::redirect('/account/apiKeys');	
	}
	
	private function apiKeyForm($mode)
	{
		$errors = array();
		
		$entryID = intval($this->request->param('id',0));
		
		
		$keyData = array('apiID' => '', 'apiKey' => '', 'entryID' => 0);
		if( $mode == 'edit' )
		{
			$keyData = DB::query(Database::SELECT, "SELECT * FROM apikeys WHERE entryID=:entryID AND userID=:userID")
											->param(':entryID', $entryID)
											->param(':userID', Auth::$user->data['id'])
											->execute()->current();
											
											
			if( !isset($keyData['entryID']) )
			{
				HTTP::redirect('/account/apiKeys');
			}
		}
		
		if ($this->request->method() == "POST") 
		{
			if( empty( $_POST['apiID'] ) )
			{
				$errors['apiID'] = 'An API ID must be provided';
			}
			
			if( empty( $_POST['apiKey'] ) )
			{
				$errors['apiKey'] = 'An API key must be provided';
			}
				
			if( $mode != 'edit' )
			{
				$keyData = DB::query(Database::SELECT, "SELECT * FROM apikeys WHERE apiID=:apiID AND userID=:userID")
												->param(':apiID', $_POST['apiID'])
												->param(':userID', Auth::$user->data['id'])
												->execute()->current();
				if( isset($keyData['apiID']) )
				{
					$errors['apiID'] = 'API ID already on your account';
				}
			}
			
			if( !(count($errors) > 0 ) )
			{
				require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
				spl_autoload_register( "Pheal::classload" );
				PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
				PhealConfig::getInstance()->http_ssl_verifypeer = false;
				PhealConfig::getInstance()->http_user_agent = 'siggy '.SIGGY_VERSION.' borkedlabs@gmail.com';
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
					$data['apiID'] = intval($_POST['apiID']);
					$data['apiKey'] = $_POST['apiKey'];
					$data['apiLastCheck'] = 0;
					$data['apiKeyInvalid'] = 0;
					$data['apiFailures'] = 0;
					$data['userID'] = Auth::$user->data['id'];
					
					if( $mode == 'edit' )
					{
						DB::update('apikeys')->set( $data )->where('entryID', '=',  $entryID)->execute();
						
						
						if( Auth::$user->data['selected_apikey_id'] == $entryID )
						{
							Auth::$user->data['corp_id'] = 0;
							Auth::$user->data['char_id'] = 0;
							Auth::$user->data['char_name'] = '';
							Auth::$user->data['selected_apikey_id'] = 0;
							
							Auth::$user->save();
						}
					}
					else
					{
						DB::insert('apikeys', array_keys($data) )->values(array_values($data))->execute();
					}
					
					HTTP::redirect('/account/characterSelect');
				}
				else
				{
					$errors['apiKey'] = 'The API key is invalid.';
				}
			}
		}
		$this->template->title = 'siggy - api key';
		$view = View::factory('account/apiKeyForm');
		$view->set('mode', $mode);
		$view->set('errors', $errors);
		$view->set('keyData', $keyData);
		$this->template->content = $view;
	}
	
	public function action_changePassword()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}

		if( !Auth::$user->isLocal() )
		{
			HTTP::redirect('/');
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
				HTTP::redirect('/');
			}
		}
		
		$view->bind('errors',$errors);
		$this->template->content = $view;
	}
	
	public function action_forgotPassword()
	{
		if( Auth::loggedIn() )
		{
			HTTP::redirect('/');
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
			HTTP::redirect('/');
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
				HTTP::redirect('/');
				return;
		}
		$this->template->title = __('siggy: no access');
				
		$view = View::factory('siggy/noAPIAccess');
	
		if(	!isset(Auth::$user->data['selected_apikey_id']) )
		{
			Auth::$user->loadByID(Auth::$user->data['id']);
			Auth::$user->save();
		}
	
		if( Auth::$user->data['selected_apikey_id'] && ( Auth::$user->data['char_id'] == 0 || Auth::$user->data['corp_id'] == 0 ) )
		{
			//char select
			$view->messageType = 'selectChar';
		}
		elseif ( !count(Auth::$user->getAPIKeys()) )
		{
			$view->messageType = 'missingAPI';
		}
		elseif( Auth::$user->data['apiKeyInvalid'] == 1 )
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
			HTTP::redirect('/');
			return;
		}

		if( !Auth::$user->isLocal() )
		{
			HTTP::redirect('/');
		}

		$keys = Auth::$user->getAPIKeys();
		if( !count($keys) )
		{
			HTTP::redirect('/account/apiKeys');
		}
		
		$this->template->title = __('siggy: character selection');
		
		Auth::$user->loadByID(Auth::$user->data['id']);
		Auth::$user->save();
		$charID =  Auth::$user->data['char_id'];
		
		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
		PhealConfig::getInstance()->http_user_agent = 'siggy '.SIGGY_VERSION.' borkedlabs@gmail.com';
		
		$chars = array();
		foreach($keys as $key)
		{
			try
			{
				try
				{
					$pheal = new Pheal( $key['apiID'], $key['apiKey']);
					
					$corpList = $this->getCorpList();
					$charList = $this->getCharList();				
					
					$apiError = FALSE;
					$result = $pheal->accountScope->Characters();
					
					foreach($result->characters as $char )
					{
							if( in_array($char->corporationID, $corpList) || in_array($char->characterID, $charList) )
							{
								$chars[ $char->characterID ] = array( 'name' => $char->name, 
																	'corpID' => $char->corporationID, 
																	'corpName' => $char->corporationName,
																	'charID' => $char->characterID, 
																	'entryID' => $key['entryID'] 
																	);
							}
					}
					
						
				}
				catch(PhealAPIException $e)
				{
					$apiError = true;
				}
			}
			catch(PhealHTTPException $e)
			{
				$apiError = true;
			}
		}
			
		if ($this->request->method() == "POST") 
		{
			$charID = intval($_POST['charID']);
			if( $charID && isset( $chars[ $charID ] ) )
			{
				Auth::$user->data['corp_id'] = $chars[ $charID ]['corpID'];
				Auth::$user->data['char_name'] = $chars[ $charID ]['name'];
				Auth::$user->data['char_id'] = $charID;
				Auth::$user->data['selected_apikey_id'] = $chars[ $charID ]['entryID'];
				
				Auth::$user->data['apiLastCheck'] = 0;
				Auth::$user->data['apiInvalid'] = 0;
				Auth::$user->data['apiFailures'] = 0;
		
				Auth::$user->save();
				
				HTTP::redirect('/');
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
			HTTP::redirect('/');
		}
		
		if( $this->request->method() == "POST" )
		{
			$rememberMe = (isset($_POST['rememberMe']) ? TRUE : FALSE);
			if( Auth::processLogin($_POST['username'], $_POST['password'], $rememberMe) === Auth::LOGIN_SUCCESS )
			{
				if( isset($_REQUEST['bounce'] ) )
				{
					HTTP::redirect(URL::base(TRUE, TRUE) . $_REQUEST['bounce']);
				}
				else
				{
					HTTP::redirect('/');
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

		HTTP::redirect('/');
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