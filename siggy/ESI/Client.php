<?php

namespace Siggy\ESI;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
	private $client = null;
	private $clientOptions = [];

	public function __construct($accessToken = '', $timeout = 10)
	{
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

	private function request($method, $route, $datasource = "tranquility")
	{
		$resp = null;
		try
		{
			$resp = $this->client->request('GET', $route, [
				'query' => [
								'datasource' => $datasource
							]
			]);
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
}