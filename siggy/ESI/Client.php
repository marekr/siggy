<?php

namespace Siggy\ESI;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use Siggy\Redis\RedisTtlCounter;

class Client
{
	private $client = null;
	private $clientOptions = [];
	private $accessToken = "";
	public static $esiStatisticTtl = 300;

	public function __construct($accessToken = '', $timeout = 10)
	{
		$this->accessToken = $accessToken;

		$options = [
			'base_uri' => 'https://esi.tech.ccp.is/',
			'timeout'  => $timeout,
			'headers' => [
				'Accept'     => 'application/json',
				'User-Agent' => 'siggy ESI Client'
			]
		];

		if($accessToken != null)
		{
			$options['headers']['Authorization'] = 'Bearer '.$accessToken;
		}

		$this->client = new GuzzleClient($options);

	}

	private function accessTokenRequired()
	{
		if(empty($this->accessToken))
		{
			throw new \BadFunctionCallException("Missing access token");
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
			$resp = $this->client->request($method, $route, $options);

			$this->incrementStatistic(true);
		}
		catch(BadResponseException $e)
		{
			$this->incrementStatistic(false);
			//4xxand 5xx errors
		}
		catch (\Exception $e) 
		{
			
		}
		finally
		{
			return $resp;
		}
	}

	public function getCorporationInformationV2(int $corporation_id): ?\stdClass
	{
		$response = $this->request('GET', "/v2/corporations/{$corporation_id}/");
		
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

	public function getUniverseSystemKillsV1(): ?array
	{
		$response = $this->request('GET', "/v1/universe/system_kills/");
		
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
	
	public function getSearchV1(string $search, array $categories, string $language = 'en-us', $strict = false): ?\stdClass
	{
		$response = $this->request('GET', "/v1/search/",[
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