<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


use OAuth\OAuth2\Service\Eve;
use Siggy\OAuth2\Storage\LaravelSession as OLaravelSession;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;
use Carbon\Carbon;

use \miscUtils;
use \Email;
use App\Facades\SiggySession;

class BackendController extends Controller {

	public function esi(Request $request)
	{
		if(empty(config('backend.esi.user_id')) ||
			Auth::user()->id != config('backend.esi.user_id'))
		{
			flash('Invalid user id.')->error();
			return redirect('manage/backend/esi');
		}

		$session = $request->session();

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
			config('backend.esi.client_id'),
			config('backend.esi.secret_key'),
			url('backend/esi')
		);

		$eveService = $serviceFactory->createService('Eve', 
														$credentials, 
														$storage, [
																	\Siggy\OAuth2\Service\Eve::SCOPE_ESI_CORPORATION_READ_WALLET
																]);
		
		$code = $request->input('code','');
		if ( !empty($code) )
		{
			// retrieve the CSRF state parameter
			$state = isset($_GET['state']) ? $_GET['state'] : null;

			$token = null;
			$result = null;
			// This was a callback request from reddit, get the token
			try
			{
				$token = $eveService->requestAccessToken($code, $state);
				$result = json_decode($eveService->request('https://login.eveonline.com/oauth/verify'), true);
			}
			catch(\OAuth\Common\Http\Exception\TokenResponseException $e)
			{
				flash('Error getting OAuth token from EVE, please try again.')->error();
				return redirect('manage/backend/esi');
			}

			if( !is_array($result) )
			{
				flash('Error getting SSO data.')->error();
				return redirect('manage/backend/esi');
			}

			miscUtils::storeDBCacheItem( 'backendEsiAccessToken', (string)$token->getAccessToken() );
			miscUtils::storeDBCacheItem( 'backendEsiRefreshToken', (string)$token->getRefreshToken() );
			
			miscUtils::storeDBCacheItem( 'backendEsiAccessTokenExpiration', (string)$token->getEndOfLife() );
			miscUtils::storeDBCacheItem( 'backendEsiToken', serialize($token) );

			flash('The character\'s connection has been updated successfully.')->success();
			return redirect('manage/backend/esi');
		}
		else
		{
			//force cast it to string or else the redirect handlers tries and break it down as an array of objects
			$url = (string)$eveService->getAuthorizationUri();

			return redirect()->to($url);
		}
	}
}