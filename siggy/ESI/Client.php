<?php

namespace Siggy\ESI;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\BadResponseException;
use Siggy\Redis\RedisTtlCounter;
use Siggy\ESI\ExpiredAuthorizationException;

use Siggy\ESI\ITokenManager;

use Illuminate\Support\Facades\Log;

class Client
{
	private $client = null;
	private $clientOptions = [];
	private $accessToken = "";
	private $accessTokenExpiration =  null;

	public static $esiStatisticTtl = 300;

	private $tokenManager = null;

	public function __construct(ITokenManager $tokenManager = null, int $timeout = 10)
	{
		$this->tokenManager = $tokenManager;

		$options = [
			'base_uri' => 'https://esi.tech.ccp.is/',
			'timeout'  => $timeout,
			'headers' => [
				'Accept'     => 'application/json',
				'User-Agent' => 'siggy ESI Client'
			]
		];

		$this->client = new GuzzleClient($options);
	}

	public function accessTokenExpired(): bool
	{
		if(time() >= $this->tokenManager->getAccessTokenExpirationTimestamp()) {
			return true;
		}

		return false;
	}

	public function refreshAccessToken(): ?OAuthTokenResponse {
		$options =	[
			'base_uri' => 'https://login.eveonline.com/',
			'query' => [
							'grant_type' => 'refresh_token',
							'refresh_token' => $this->tokenManager->getRefreshToken()
						]
		];

		try
		{
			$options['headers']['Authorization'] = 'Basic '.base64_encode($this->tokenManager->getAppClientId().':'.$this->tokenManager->getAppSecretKey());
			$response = $this->client->request('POST', '/oauth/token', $options);

			
			if( $response == null)
			{
				return null;
			}

			$resp = json_decode($response->getBody());

			return new OAuthTokenResponse($resp->access_token, $resp->expires_in, $resp->token_type, $resp->refresh_token);
		}
		catch(\Exception $e)
		{
			//error! o noes
		}
		
		return null;
	}

	private function accessTokenRequired()
	{
		if($this->tokenManager == null)
		{
			throw new \BadFunctionCallException("Missing access token");
		}

		if($this->accessTokenExpired() &&
			$this->tokenManager->shouldAutoRefreshTokens())
		{
			$newToken = $this->refreshAccessToken();

			$this->tokenManager->storeToken($newToken);
		}
		else
		{
			throw new ExpiredAuthorizationException();
		}
	}
	
	public static function getEsiSuccessCounter(): RedisTtlCounter
	{
		$esiCalls = new RedisTtlCounter('ttlc:esiSuccess', self::$esiStatisticTtl);
		return $esiCalls;
	}
	
	public static function getEsiFailureCounter(): RedisTtlCounter
	{
		$esiCalls = new RedisTtlCounter('ttlc:esiFailure', self::$esiStatisticTtl);
		return $esiCalls;
	}

	private function incrementStatistic(bool $success)
	{
		if($success)
		{
			$esiCalls = self::getEsiSuccessCounter();
			$esiCalls->add(uniqid());
		}
		else
		{
			$esiCalls = self::getEsiFailureCounter();
			$esiCalls->add(uniqid());
		}
	}

	private function request($method, $route, array $queryBits = [], $datasource = "tranquility")
	{
		$resp = null;
		try
		{
			$options =	[
				'query' => [
								'datasource' => $datasource
							]
			];

			$options['query'] = array_merge($options['query'],$queryBits);
			

			if($this->tokenManager != null)
			{
				$options['headers']['Authorization'] = 'Bearer '.$this->tokenManager->getAccessToken();
			}
			
			$resp = $this->client->request($method, $route, $options);

			if($resp->hasHeader('warning'))
			{
				Log::alert('warning header retrieved on route ' . $route . ' of: ' . $resp->getHeader('warning')[0]);
			}

			$this->incrementStatistic(true);
		}
		catch(ClientException $e)
		{
			$response = $e->getResponse();
			$responseBodyAsString = $response->getBody()->getContents();
			$responseJson = json_decode($responseBodyAsString);
			
			if(property_exists($responseJson, 'error')) {
				if($responseJson->error == 'expired') {
					throw new ExpiredAuthorizationException();
				}
			}
			//4xx errors
			//placeholder as we can error log here
			$this->incrementStatistic(false);
		}
		catch(ServerException $e)
		{
			//5xx errors
			//placeholder as we can error log here
			$this->incrementStatistic(false);
		}
		catch(\Exception $e)
		{
		}
		
		return $resp;
	}
	
	public function getCorporationWalletDivisionJournal(int $corporation_id, int $division, ?int $fromId = null): ?array
	{
		$this->accessTokenRequired();

		$opts = [];
		if($fromId != null) 
		{
			$opts['from_id'] = $fromId;
		}

		$response = $this->request('GET', "/v2/corporations/{$corporation_id}/wallets/{$division}/journal/",$opts);
		if( $response == null ||
			$response->getStatusCode() != 200)
		{
			return null;
		}
		
		$resp = $response->getBody();

		return json_decode($resp);
	}

	public function getCorporationInformationV4(int $corporation_id): ?\stdClass
	{
		$response = $this->request('GET', "/v4/corporations/{$corporation_id}/");
		if( $response == null ||
			$response->getStatusCode() != 200)
		{
			return null;
		}
		
		$resp = $response->getBody();

		return json_decode($resp);
	}
	
	public function getCharacterInformationV4(int $character_id): ?\stdClass
	{
		$response = $this->request('GET', "/v4/characters/{$character_id}/");
		
		if( $response == null ||
			$response->getStatusCode() != 200)
		{
			return null;
		}
		
		$resp = $response->getBody();

		return json_decode($resp);
	}

	public function getUniverseSystemJumpsV1(): ?array
	{
		$response = $this->request('GET', "/v1/universe/system_jumps/");
		
		if( $response == null ||
			$response->getStatusCode() != 200)
		{
			return null;
		}
		
		$hourEnd = Carbon::parse($response->getHeader('Last-Modified')[0]);

		$resp = [
			'dateStart' => $hourEnd->copy()->subHour(),
			'dateEnd' => $hourEnd,
			'records' =>  json_decode($response->getBody())
		];

		return $resp;
	}

	public function getUniverseSystemKillsV2(): ?array
	{
		$response = $this->request('GET', "/v2/universe/system_kills/");
		
		if( $response == null ||
			$response->getStatusCode() != 200)
		{
			return null;
		}
		
		$hourEnd = Carbon::parse($response->getHeader('Last-Modified')[0]);

		$resp = [
			'dateStart' => $hourEnd->copy()->subHour(),
			'dateEnd' => $hourEnd,
			'records' =>  json_decode($response->getBody())
		];

		return $resp;
	}
	
	public function getSearchV2(string $search, array $categories, string $language = 'en-us', $strict = false): ?\stdClass
	{
		$response = $this->request('GET', "/v2/search/",[
			'search' => $search,
			'categories' => implode(",",$categories),
			'language' => $language,
			'strict' => $strict ? 'true' : 'false'
		]);
		
		if( $response == null ||
			$response->getStatusCode() != 200)
		{
			return null;
		}
		
		$resp = $response->getBody();

		return json_decode($resp);
	}

	
	public function postUiAutopilotWaypointV2(int $destination_id, bool $clear_other_waypoints, bool $add_to_beginning): bool
	{
		$this->accessTokenRequired();

		$response = $this->request('POST', "/v2/ui/autopilot/waypoint/",[
			'destination_id' => $destination_id,
			'clear_other_waypoints' => $clear_other_waypoints ? 'true' : 'false',
			'add_to_beginning' => $add_to_beginning ? 'true' : 'false'
		]);
		
		if( $response == null ||
			$response->getStatusCode() != 204)
		{
			return false;
		}
		
		return true;
	}
}