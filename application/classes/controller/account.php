<?php

require Kohana::find_file('vendor', 'OAuth\bootstrap');
use OAuth\OAuth2\Service\Eve;
use OAuth\Common\Storage\Session as OSession;
use OAuth\Common\Consumer\Credentials;
use Pheal\Pheal;
use Carbon\Carbon;

class Controller_Account extends FrontController {
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
			case 'sso_complete':
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
			case 'sso_complete':
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

	public function action_sso_complete()
	{
		$this->template->title = "Complete SSO Login";
		$this->template->content =  View::Factory('account/sso_complete')->set('invalidLogin', false);
	}

	public function action_sso()
	{
		$session = Session::instance();

		$sso_type = $this->request->param('id');

		if( $sso_type == 'eve' )
		{

			/** @var $serviceFactory \OAuth\ServiceFactory An OAuth service factory. */
			$serviceFactory = new \OAuth\ServiceFactory();
			// Session storage
			$storage = new OSession();


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

			$eveService = $serviceFactory->createService('Eve', $credentials, $storage, [
																						\OAuth\OAuth2\Service\Eve::SCOPE_CHARACTER_LOCATION_READ,
																						\OAuth\OAuth2\Service\Eve::SCOPE_CHARACTER_NAVIGATION_WRITE
																						]);
			if ( !empty($_GET['code']) )
			{
				// retrieve the CSRF state parameter
				$state = isset($_GET['state']) ? $_GET['state'] : null;

				// This was a callback request from reddit, get the token
				$token = $eveService->requestAccessToken($_GET['code'], $state);

				$result = json_decode($eveService->request('https://login.eveonline.com/oauth/verify'), true);

				if( $session->get('sso_connect') )
				{
					if( !is_array($result) )
					{
						HTTP::redirect('/account/connected');
					}

					$expiration = Carbon::now()->addSeconds($token->getEndOfLife())->toDateTimeString();

					if( $userID = Auth::characterOwnerHashTied( $result['CharacterOwnerHash'] ) )
					{
						Auth::$user->updateSSOCharacter($result['CharacterID'],
															$token->getAccessToken(),
															$token->getRefreshToken(),
															$expiration);

						HTTP::redirect('/account/connected');
					}
					else
					{
						Auth::$user->addSSOCharacter($result['CharacterOwnerHash'], 
													$result['CharacterID'], 
													$token->getAccessToken(), 
													$expiration, 
													$token->getRefreshToken());
						
						HTTP::redirect('/account/connected');
					}
				}
				else
				{
					//find username by CharacterOwnerHash
					if( !is_array($result) )
					{
						HTTP::redirect('/');
					}

					$expiration = Carbon::createFromTimeStampUTC($token->getEndOfLife())->toDateTimeString();

					if( $userID = Auth::characterOwnerHashTied( $result['CharacterOwnerHash'] ) )
					{
						$status = Auth::forceLogin($userID);

						Auth::$user->updateSSOCharacter($result['CharacterID'],
														$token->getAccessToken(),
														$token->getRefreshToken(),
														$expiration);

						HTTP::redirect('/');
					}
					else
					{
						$session->set('sso_login', true);
						$session->set('sso_character_owner_hash', $result['CharacterOwnerHash']);
						$session->set('sso_character_id', $result['CharacterID']);
						$session->set('sso_access_token', $token->getAccessToken());
						$session->set('sso_refresh_token', $token->getRefreshToken());

						$session->set('sso_token_eol', $expiration);
						
						HTTP::redirect('/account/sso/complete');
					}
				}
			}
			else
			{
				HTTP::redirect($eveService->getAuthorizationUri());
			}
		}
	}
	
	public function action_connect()
	{
		$session = Session::instance();

		$session->set('sso_connect', true);
		HTTP::redirect('/account/sso/eve');
	}

	public function action_overview()
	{
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

	public function action_addAPI()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}

		$this->apiKeyForm('add');
	}

	public function action_removeAPI()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}


		$entryID = intval($this->request->param('id',0));


		$keyData = DB::query(Database::SELECT, "SELECT * FROM apikeys
												WHERE entryID=:entryID AND userID=:userID")
										->param(':entryID', $entryID)
										->param(':userID', Auth::$user->data['id'])
										->execute()
										->current();
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

	public function action_changePassword()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
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
					$from = Kohana::$config->load('auth')->sender_email_address;


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
					$from = Kohana::$config->load('auth')->sender_email_address;

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

	public function action_characters()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}


		$charID =  Auth::$user->data['char_id'];
		$ssoChars = Auth::$user->getSSOCharacters();
		if( !count($ssoChars) )
		{
			HTTP::redirect('/account/apiKeys');
		}

		$this->template->title = __('siggy: characters');

		$chars = [];
		$selectableChars = [];
		$unselectableChars = [];
		foreach($ssoChars as $ssoChar)
		{
			$corpList = $this->getCorpList();
			$charList = $this->getCharList();

			$char = Character::find($ssoChar['character_id']);

			if( in_array($char->corporation_id, $corpList) || in_array($char->id, $charList) )
			{
				$selectableChars[ $char->id ] = $char;
			}
			else 
			{
				$unselectableChars[ $char->id ] = $char;
			}
		}

		if ($this->request->method() == "POST")
		{
			$charID = intval($_POST['charID']);
			if( $charID && isset( $selectableChars[ $charID ] ) )
			{
				Auth::$user->data['corp_id'] = $selectableChars[ $charID ]->corporation_id;
				Auth::$user->data['char_name'] = $selectableChars[ $charID ]->name;
				Auth::$user->data['char_id'] = $charID;

				Auth::$user->save();

				HTTP::redirect('/');
			}
		}

		$view = View::factory('account/characters');
		$view->selectableChars = $selectableChars;
		$view->unselectableChars = $unselectableChars;
		$view->selectedCharID = $charID;
		$this->template->content = $view;
	}

	public function action_connected()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}

		$charID =  Auth::$user->data['char_id'];
		$ssoChars = Auth::$user->getSSOCharacters();

		
		$charData = [];
		foreach($ssoChars as $ssoChar)
		{
			$corpList = $this->getCorpList();
			$charList = $this->getCharList();

			$char = Character::find($ssoChar['character_id']);
			$charData[ $char->id ] = $char;
		}

		$this->template->title = __('siggy: connected accounts');

		$view = View::factory('account/connected');
		$view->characters = $ssoChars;
		$view->character_data = $charData;
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
				$session = Session::instance();

				if( $session->get_once('sso_login',false) )
				{
					Auth::$user->addSSOCharacter($session->get_once('sso_character_owner_hash'), 
												$session->get_once('sso_character_id'), 
												$session->get_once('sso_access_token'), 
												$session->get_once('sso_token_eol'), 
												$session->get_once('sso_refresh_token'));
				}

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
		
		$session = Session::instance();
		$session->destroy();

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
