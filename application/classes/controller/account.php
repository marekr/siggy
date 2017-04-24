<?php

require Kohana::find_file('vendor', 'OAuth\bootstrap');
use OAuth\OAuth2\Service\Eve;
use OAuth\Common\Storage\Session as OSession;
use OAuth\Common\Consumer\Credentials;
use Carbon\Carbon;
use Siggy\View;

class Controller_Account extends FrontController {
	protected $noAutoAuthRedirects = true;

	function ___construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::___construct($request, $response);
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
		parent::after();
	}

	public function action_index()
	{
		HTTP::redirect('account/overview');
	}

	public function action_sso_complete()
	{
		$resp = view('account.sso_complete', [ 
										 'invalidLogin' => false
										]);
		$this->response->body($resp)
						->noCache();
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
																						\OAuth\OAuth2\Service\Eve::SCOPE_CHARACTER_NAVIGATION_WRITE,
																						\OAuth\OAuth2\Service\Eve::SCOPE_ESI_UI_WRITE_WAYPOINT,
																						\OAuth\OAuth2\Service\Eve::SCOPE_ESI_LOCATION_READ_LOCATION,
																						\OAuth\OAuth2\Service\Eve::SCOPE_ESI_LOCATION_READ_SHIP_TYPE,
																						\OAuth\OAuth2\Service\Eve::SCOPE_ESI_UI_OPEN_WINDOW
																						]);

			$dbScopes = [
				'scope_esi_location_read_location' => 1,
				'scope_esi_location_read_ship_type' => 1,
				'scope_esi_ui_write_waypoint' => 1,
				'scope_esi_ui_open_window' => 1,
				'scope_character_location_read' => 1,
				'scope_character_navigation_write' => 1,
			];

			if ( !empty($_GET['code']) )
			{
				// retrieve the CSRF state parameter
				$state = isset($_GET['state']) ? $_GET['state'] : null;

				// This was a callback request from reddit, get the token
				$token = $eveService->requestAccessToken($_GET['code'], $state);

				$result = json_decode($eveService->request('https://login.eveonline.com/oauth/verify'), true);

				if( $session->get_once('sso_connect',false) )
				{
					if( !is_array($result) )
					{
						Message::add('danger', ___('Error getting SSO data.'));
						HTTP::redirect('/account/connected');
					}

					$expiration = Carbon::createFromTimeStampUTC($token->getEndOfLife())->toDateTimeString();

					$userID = Auth::characterOwnerHashTied( $result['CharacterOwnerHash'] );

					if( $userID == Auth::$user->id )
					{
						Message::add('info', ___('The character\'s connection has been updated successfully.'));

						Auth::$user->updateSSOCharacter($result['CharacterID'],
															$token->getAccessToken(),
															$token->getRefreshToken(),
															$expiration,
															$dbScopes);

						HTTP::redirect('/account/connected');
					}
					else if ( $userID == null )
					{
						Message::add('success', ___('The character has been successfully connected to your siggy account.'));
						Auth::$user->addSSOCharacter($result['CharacterOwnerHash'], 
													$result['CharacterID'], 
													$token->getAccessToken(), 
													$expiration, 
													$token->getRefreshToken(),
													$dbScopes);
						
						HTTP::redirect('/account/connected');
					}
					else
					{
						Message::add('danger', ___('The character is connected to a different account. You must disconnect it first if you want to connect it to this one.'));
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
														$expiration,
														$dbScopes);

						HTTP::redirect('/');
					}
					else
					{
						$session->set('sso_login', true);
						$session->set('sso_character_owner_hash', $result['CharacterOwnerHash']);
						$session->set('sso_character_id', $result['CharacterID']);
						$session->set('sso_access_token', $token->getAccessToken());
						$session->set('sso_refresh_token', $token->getRefreshToken());
						$session->set('sso_scopes', $dbScopes);

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
		$resp = view('account.overview', [ 'user' =>  Auth::$user ]);
		$this->response->body($resp)
						->noCache();
	}

	public function action_register()
	{
		if( Auth::loggedIn() )
		{
			HTTP::redirect('/account');
		}

		$errors = array();

		if ($this->request->method() == "POST")
		{
			$validator = Validator::make($_POST, [
				'username' => 'required',
				'email' => 'required|email',
				'password' => 'required|min:8|confirmed',
				'password_confirmation' => 'required',
			]);

			if(!$validator->fails())
			{
				if( Auth::usernameExists( $_POST['username'] ) )
				{
					$validator->errors()->add('username', 'Username is already in use.');
				}

				if( Auth::emailExists( $_POST['email'] ) )
				{
					$validator->errors()->add('email', 'Email is already in use.');
				}

				if( empty( $errors ) )
				{
					$session = Session::instance();

					$userData = array('username' => $_POST['username'],
									 'password' => $_POST['password'],
									 'email' => $_POST['email'],
									 'active' => 1
									 );

					if( User::create( $userData ) )
					{
						Auth::processLogin($_POST['username'], $_POST['password']);

						if( $session->get_once('sso_login',false) )
						{
							Auth::$user->addSSOCharacter($session->get_once('sso_character_owner_hash'),
								$session->get_once('sso_character_id'),
								$session->get_once('sso_access_token'),
								$session->get_once('sso_token_eol'),
								$session->get_once('sso_refresh_token'),
								$session->get_once('sso_scopes'));

							HTTP::redirect('/');
						}
						else
						{
							HTTP::redirect('account/connected');
						}
					}
					else
					{
						$validator->errors()->add('username', 'Unknown error has occured.');
					}
				}
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}

		$resp = view('account.register');
	
		$this->response->body($resp)
						->noCache();
	}

	public function action_changePassword()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}

		$errors = array();
		if ($this->request->method() == "POST")
		{
			$validator = Validator::make($_POST, [
				'current_password' => 'required',
				'password' => 'required|min:8|confirmed',
				'password_confirmation' => 'required',
			]);

			if(!$validator->fails())
			{
				if( Auth::hash($_POST['current_password']) != Auth::$user->password )
				{
					$validator->errors()->add('current_password', 'This is not current password.');
				}
			}

			if( !count($validator->errors()) )
			{
				Auth::$user->updatePassword($_POST['password']);
				HTTP::redirect('/');
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}
		
		$resp = view('account.password_change');

		$this->response->body($resp)
						->noCache();
	}

	public function action_forgotPassword()
	{
		if( Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}

		if ($this->request->method() == "POST")
		{
			$validator = Validator::make($_POST, [
				'reset_email' => 'required|email'
			]);

			if($validator->passes())
			{
				$user = User::findByEmail($_POST['reset_email']);

				if ( isset( $user->id ) )
				{
					// send an email with the account reset token
					$user->reset_token = Auth::generatePassword(32);
					$user->save();

					$message = "You have requested a password reset for your siggy account. To confirm the password reset, please the follow the proceeding url:\n\n"
					.":reset_token_link\n\n"
					."If the above link is not clickable, please visit the following page:\n"
					.":reset_link\n\n"
					."and copy/paste the following reset token: :reset_token\nYour user account name is: :username\n";

					$mailer = Email::connect();
					// Create complex Swift_Message object stored in $message
					// MUST PASS ALL PARAMS AS REFS
					$subject = ___('siggy: Account password reset');

					$to = $_POST['reset_email'];
					$from = Kohana::$config->load('auth')->sender_email_address;


					$body =  ___($message, array(
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

				$resp = view('account.forgot_password_sent', [ 
												'email' => $_POST['reset_email']
												]);
				$this->response->body($resp)
								->noCache();
				return;
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}
		
		$resp = view('account.forgot_password');
		$this->response->body($resp)
						->noCache();
	}

	public function action_completePasswordReset()
	{
		if( Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}

		$errors = [];
		if( isset( $_REQUEST['reset_token'] ) )
		{
			$validator = Validator::make($_POST, [
				'reset_email' => 'required|email',
				'reset_token' => 'required'
			]);

			if($validator->passes())
			{
				$user = User::findByEmail($_REQUEST['reset_email']);

				if ( isset($user->id) && ($user->reset_token == $_REQUEST['reset_token']) )
				{
					$password = Auth::generatePassword();
					$user->reset_token = '';
					$user->updatePassword($password);

					$message = "You have completed the password reset process. Please use the following randomly generated password to login upon which you may change it to anything you desire\n\n"
					.":password\n\n"
					."You may login at:\n"
					.":url\n";

					$mailer = Email::connect();
					// Create complex Swift_Message object stored in $message
					// MUST PASS ALL PARAMS AS REFS
					$subject = ___('siggy: Your new password');

					$to = $_REQUEST['reset_email'];
					$from = Kohana::$config->load('auth')->sender_email_address;

					$body =  ___($message, array(
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
					
					$resp = view('account.password_reset_completed', [ ]);
					$this->response->body($resp)
									->noCache();
					return;
				}
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}
		
		$resp = view('account.complete_password_reset_form', [ 
										'errors' => $errors
										]);
		$this->response->body($resp)
						->noCache();
	}

	public function action_characters()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}


		$charID =  Auth::$user->char_id;
		$ssoChars = Auth::$user->ssoCharacters;
		if( !count($ssoChars) )
		{
			HTTP::redirect('/account/connected');
		}

		$chars = [];
		$selectableChars = [];
		$unselectableChars = [];

		foreach($ssoChars as $ssoChar)
		{
			if( $ssoChar->valid != 1 )
				continue;
			
			$char = Character::find($ssoChar->character_id);

			if($char != null && $char->corporation != null)
			{
				$gmChars = GroupMember::findByType(GroupMember::TypeChar, $char->id);
				$gmCorps = GroupMember::findByType(GroupMember::TypeCorp, $char->corporation_id);

				if( count($gmCorps) || count($gmChars) > 0 )
				{
					$selectableChars[ $char->id ] = $char;
				}
				else
				{
					$unselectableChars[ $char->id ] = $char;
				}
			}
		}

		if ($this->request->method() == "POST")
		{
			$charID = intval($_POST['charID']);
			if( $charID && isset( $selectableChars[ $charID ] ) )
			{
				Auth::$user->char_id = $charID;

				Auth::$user->save();
				Auth::$session->reloadUserSession();

				HTTP::redirect('/');
			}
		}

		
		$resp = view('account.characters', [
													'selectableChars' => $selectableChars,
													'unselectableChars' => $unselectableChars,
													'selectedCharID' => $charID
												]);
		$this->response->body($resp)
						->noCache();
	}

	public function action_connected()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}

		$charID =  Auth::$user->char_id;
		$ssoChars = Auth::$user->ssoCharacters;

		$charData = [];
		foreach($ssoChars as $ssoChar)
		{
			$char = Character::find($ssoChar->character_id);
			if($char != null)
			{
				$charData[ $char->id ] = $char;
			}
		}
		
		$resp = view('account.connected', [
													'characters' => $ssoChars,
													'character_data' => $charData
												]);
		$this->response->body($resp)
						->noCache();
	}

	public function action_disconnect()
	{
		if( !Auth::loggedIn() )
		{
			HTTP::redirect('/');
			return;
		}

		$this->validateCSRF();

		if( $this->request->method() == "POST" ) {

			$charId = (int)$_POST['character_id'];

			Auth::$user->removeSSOCharacter($charId);

			Message::add('success', ___('The character has been disconnected from your siggy account. You must remove the character permissions on the EVE Online website if you want to ensure siggy no longer has permission to access the character (not required)'));

			HTTP::redirect('account/connected');
		}
	}

	public function action_login()
	{
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
												$session->get_once('sso_refresh_token'),
												$session->get_once('sso_scopes'));
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
				$invalidLogin = true;
			}
		}
		else
		{
			$invalidLogin = false;
		}

		
		$resp = view('account.login', [ 
										'bounce' => isset($_REQUEST['bounce']) ? $_REQUEST['bounce'] : '',
										 'username' => isset($_POST['username']) ? $_POST['username'] : '',
										 'invalidLogin' => $invalidLogin
										]);
		$this->response->body($resp)
						->noCache();
	}


	public function action_logout()
	{
		// Sign out the user
		Auth::processLogout();
		
		$session = Session::instance();
		$session->destroy();

		HTTP::redirect('/');
	}
}
