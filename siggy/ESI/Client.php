<?php

namespace Siggy\ESI;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
	private $client = null;
	private $clientOptions = [];
	private $accessToken = "";

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
		}
		catch(ClientException $e)
		{
			//4xx errors
			//placeholder as we can error log here
		}
		catch(ServerException $e)
		{
			//5xx errors
			//placeholder as we can error log here
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