<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;


use OAuth\OAuth2\Service\Eve;
use Siggy\OAuth2\Storage\LaravelSession as OLaravelSession;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;
use Carbon\Carbon;
use \Character;
use \GroupMember;

use \User;
use \Email;
use App\Facades\SiggySession;

class AccountController extends Controller {
	use ThrottlesLogins;

	/**
	* Laravel method used by abstractions such as ThrottlesLogins to get the username/email key
	*/
	public function username(): string
	{
		return "username";
	}

	public function postLogin(Request $request)
	{
        $this->validate($request, [
            'username' => 'required', 'password' => 'required',
        ]);

		if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

		if( Auth::attempt(['username' => $request->input('username'), 
							'password' => $request->input('password')], 
							$request->has('remember')) === true )
		{
			
			$request->session()->regenerate();
			$this->clearLoginAttempts($request);
			$session = $request->session();

			if( $session->pull('sso_login',false) )
			{
				Auth::user()->addSSOCharacter($session->pull('sso_character_owner_hash'), 
											$session->pull('sso_character_id'), 
											$session->pull('sso_access_token'), 
											$session->pull('sso_token_eol'), 
											$session->pull('sso_refresh_token'),
											$session->pull('sso_scopes'));
			}

			return redirect()->intended('/');
		}
		
		$this->incrementLoginAttempts($request);
	
		return redirect()->back()
			->withErrors(['Invalid login username or password'])
			->withInput($request->only('username', 'remember'));
	}

	public function getLogin(Request $request)
	{
		if( Auth::check() )
		{
			return redirect('/');
		}

		return view('account.login', $request->only('username', 'remember'));
	}

	

	public function getRegister(Request $request)
	{
		return view('account.register');
	}
	
	public function postRegister(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'username' => 'required|unique:users',
			'email' => 'required|email|unique:users',
			'password' => 'required|min:8|confirmed',
			'password_confirmation' => 'required',
		]);
		
		if( count( $validator->errors() ) )
		{
			return redirect('account/register')
				->withErrors($validator)
				->withInput();
		}
	
		$session = session();

		$userData = ['username' => $request->input('username'),
							'password' => $request->input('password'),
							'email' => $request->input('email'),
							'active' => 1
							];

		if( User::create( $userData ) != null )
		{
			Auth::attempt(['username' => $userData['username'], 'password' => $userData['password']]);

			if( $session->pull('sso_login',false) )
			{
				Auth::user()->addSSOCharacter($session->pull('sso_character_owner_hash'),
					$session->pull('sso_character_id'),
					$session->pull('sso_access_token'),
					$session->pull('sso_token_eol'),
					$session->pull('sso_refresh_token'),
					$session->pull('sso_scopes'));

				return redirect('/');
			}
			else
			{
				return redirect('account/connected');
			}
		}
		else
		{
			$validator->errors()->add('username', 'Unknown error has occured.');
			
			return redirect('account/register')
				->withErrors($validator)
				->withInput();
		}
	}

	private function getSelectableCharacters() : array
	{
		$list = [];

		$ssoChars = Auth::user()->ssoCharacters;

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

		return [
			'selectable' => $selectableChars,
			'unselectable' => $unselectableChars
		];
	}

	public function getCharacters()
	{
		$charID =  Auth::user()->char_id;
		$ssoChars = Auth::user()->ssoCharacters;
		if( !count($ssoChars) )
		{
			return redirect('/account/connected');
		}

		$chars = $this->getSelectableCharacters();

		return view('account.characters', [
													'selectableChars' => $chars['selectable'],
													'unselectableChars' =>  $chars['unselectable'],
													'selectedCharID' => $charID
												]);
	}
	
	public function getOverview()
	{
		return view('account.overview', [ 'user' =>  Auth::user() ]);
	}
	
	public function postCharacters(Request $request)
	{
		$charID = $request->input('charID');

		$chars = $this->getSelectableCharacters();
		$selectableChars = $chars['selectable'];
		
		if( $charID && isset( $selectableChars[ $charID ] ) )
		{
			Auth::user()->char_id = $charID;

			Auth::user()->save();
			SiggySession::reloadUserSession();

			return redirect('/');
		}
	}
	
	public function getConnected()
	{
		$charID =  Auth::user()->char_id;
		$ssoChars = Auth::user()->ssoCharacters;

		$charData = [];
		foreach($ssoChars as $ssoChar)
		{
			$char = Character::find($ssoChar->character_id);
			if($char != null)
			{
				$charData[ $char->id ] = $char;
			}
		}
		
		return view('account.connected', [
													'characters' => $ssoChars,
													'character_data' => $charData
												]);
	}

	public function postDisconnect(Request $request)
	{
		$charId = (int)$request->input('character_id');

		Auth::user()->removeSSOCharacter($charId);

		flash('The character has been disconnected from your siggy account. You must remove the character permissions on the EVE Online website if you want to ensure siggy no longer has permission to access the character (not required)')->success();

		return redirect('account/connected');
	}

	public function getLogout()
	{
		// Sign out the user
		Auth::logout();
		
		SiggySession::destroy();

		return redirect('/');
	}
	
	public function getConnect()
	{
		$session = session();

		$session->put('sso_connect', true);
		
		return redirect('/account/sso/eve');
	}
	
	public function sso($id, Request $request)
	{
		$session = $request->session();
		
		$sso_type = $id;

		if( $sso_type == 'eve' )
		{

			/** @var $serviceFactory \OAuth\ServiceFactory An OAuth service factory. */
			$serviceFactory = new ServiceFactory();
			// Session storage
			$storage = new OLaravelSession($session);

			$serviceFactory->registerService('Eve', \Siggy\OAuth2\Service\Eve::class);

			/**
			 * Create a new instance of the URI class with the current URI, stripping the query string
			 */
			$uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
			$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
			$currentUri->setQuery('');


			$credentials = new Credentials(
				config('sso.credentials.key'),
				config('sso.credentials.secret'),
				url('account/sso/eve')
			);

			$eveService = $serviceFactory->createService('Eve', 
															$credentials, 
															$storage, [
																		\Siggy\OAuth2\Service\Eve::SCOPE_CHARACTER_LOCATION_READ,
																		\Siggy\OAuth2\Service\Eve::SCOPE_CHARACTER_NAVIGATION_WRITE,
																		\Siggy\OAuth2\Service\Eve::SCOPE_ESI_UI_WRITE_WAYPOINT,
																		\Siggy\OAuth2\Service\Eve::SCOPE_ESI_LOCATION_READ_LOCATION,
																		\Siggy\OAuth2\Service\Eve::SCOPE_ESI_LOCATION_READ_SHIP_TYPE,
																		\Siggy\OAuth2\Service\Eve::SCOPE_ESI_LOCATION_READ_ONLINE,
																		\Siggy\OAuth2\Service\Eve::SCOPE_ESI_UI_OPEN_WINDOW
																	]);

			$dbScopes = [
				'scope_esi_location_read_location' => 1,
				'scope_esi_location_read_ship_type' => 1,
				'scope_esi_location_read_online' => 1,
				'scope_esi_ui_write_waypoint' => 1,
				'scope_esi_ui_open_window' => 1,
				'scope_character_location_read' => 1,
				'scope_character_navigation_write' => 1,
			];

			if ( !empty($_GET['code']) )
			{
				// retrieve the CSRF state parameter
				$state = isset($_GET['state']) ? $_GET['state'] : null;

				$token = null;
				$result = null;
				// This was a callback request from reddit, get the token
				try
				{
					$token = $eveService->requestAccessToken($_GET['code'], $state);
					$result = json_decode($eveService->request('https://login.eveonline.com/oauth/verify'), true);
				}
				catch(\OAuth\Common\Http\Exception\TokenResponseException $e)
				{
					return redirect('account/login')
						->withErrors(['Error getting OAuth token from EVE, please try again.']);
				}


				//force us to get some info about the character in the table
				$charData = Character::find($result['CharacterID']);

				if( $session->pull('sso_connect',false) && Auth::check() )	//if already logged in
				{
					if( !is_array($result) )
					{
						flash('Error getting SSO data.')->error();
						return redirect('/account/connected');
					}

					$expiration = Carbon::createFromTimeStampUTC($token->getEndOfLife())->toDateTimeString();

					$user = User::findUserByCharacterOwnerHash( $result['CharacterOwnerHash'] );

					if( $user != null && 
						$user->id == Auth::user()->id )
					{
						flash('The character\'s connection has been updated successfully.')->success();

						Auth::user()->updateSSOCharacter($result['CharacterID'],
															$token->getAccessToken(),
															$token->getRefreshToken(),
															$expiration,
															$dbScopes);

						return redirect('/account/connected');
					}
					else if ( $user == null )
					{
						flash('The character has been successfully connected to your siggy account.')->success();
						Auth::user()->addSSOCharacter($result['CharacterOwnerHash'], 
													$result['CharacterID'], 
													$token->getAccessToken(), 
													$expiration, 
													$token->getRefreshToken(),
													$dbScopes);
						
						return redirect('/account/connected');
					}
					else
					{
						flash('The character is connected to a different account. You must disconnect it first if you want to connect it to this one.')->error();
						return redirect('/account/connected');
					}
				}
				else
				{
					//find username by CharacterOwnerHash
					if( !is_array($result) )
					{
						return redirect('/');
					}

					$expiration = Carbon::createFromTimeStampUTC($token->getEndOfLife())->toDateTimeString();

					$user = User::findUserByCharacterOwnerHash( $result['CharacterOwnerHash'] );
					if( $user != null )
					{
						$status = Auth::login($user, true);	//login and remember

						$user->updateSSOCharacter($result['CharacterID'],
														$token->getAccessToken(),
														$token->getRefreshToken(),
														$expiration,
														$dbScopes);

						return redirect('/');
					}
					else
					{
						$session->put('sso_login', true);
						$session->put('sso_character_owner_hash', $result['CharacterOwnerHash']);
						$session->put('sso_character_id', $result['CharacterID']);
						$session->put('sso_access_token', $token->getAccessToken());
						$session->put('sso_refresh_token', $token->getRefreshToken());
						$session->put('sso_scopes', $dbScopes);

						$session->put('sso_token_eol', $expiration);
						
						return redirect('/account/sso/complete');
					}
				}
			}
			else
			{
				//force cast it to string or else the redirect handlers tries and break it down as an array of objects
				$url = (string)$eveService->getAuthorizationUri();

				return redirect()->to($url);
			}
		}
	}
	
	public function getChangePassword()
	{
		return view('account.password_change');
	}

	public function postChangePassword()
	{
		$validator = Validator::make($_POST, [
			'current_password' => 'required',
			'password' => 'required|min:8|confirmed',
			'password_confirmation' => 'required',
		]);

		if(!$validator->fails())
		{
			if( \Siggy\SiggyUserProvider::hash($_POST['current_password']) != Auth::user()->password )
			{
				$validator->errors()->add('current_password', 'This is not current password.');
			}
		}

		if( !count($validator->errors()) )
		{
			Auth::user()->updatePassword($_POST['password']);
			return redirect('account/overview');
		}
		else
		{
            return redirect('account/changePassword')
                        ->withErrors($validator)
                        ->withInput();
		}
	}

	public function getForgotPassword()
	{
		return view('account.forgot_password');
	}

	public function postForgotPassword()
	{
		$validator = Validator::make($_POST, [
			'reset_email' => 'required|email'
		]);

		if($validator->passes())
		{
			$user = User::findByEmail($_POST['reset_email']);

			if ( $user != null )
			{
				// send an email with the account reset token
				$user->reset_token = md5($user->email . \Illuminate\Support\Str::random(32));
				$user->save();

				$message = new \App\Mail\ForgotPassword($user);
				Mail::to($user->email)->send($message);
			}

			return view('account.forgot_password_sent', [ 
											'email' => $_POST['reset_email']
											]);
		}
		else
		{
            return redirect('account/password_reset')
                        ->withErrors($validator)
                        ->withInput();
		}
	}

	public function getSSOComplete()
	{
		return view('account.sso_complete', [ 
										 'invalidLogin' => false
										]);
	}

	public function getCompletePasswordReset($token, Request $request)
	{
		$user = User::findByResetToken($token);
		if($user == null)
		{
			return redirect('account/password_reset');
		}
		
		return view('account.complete_password_reset_form', ['token' => $token]);
	}


	public function postCompletePasswordReset($token, Request $request)
	{
		$user = User::findByResetToken($token);
		if($user == null)
		{
			return redirect('account/password_reset');
		}

		$validator = Validator::make($request->all(), [
			'password' => 'required|min:8|confirmed',
			'password_confirmation' => 'required',
		]);

		if($validator->passes())
		{
			$user->reset_token = '';
			$user->updatePassword($request->input('password'));

			$message = new \App\Mail\PasswordResetCompleted($user);
			Mail::to($user->email)->send($message);
			
			return view('account.password_reset_completed');
		}
		
		return redirect('account/completePasswordReset')
					->withErrors($validator)
					->withInput();
	}
}
