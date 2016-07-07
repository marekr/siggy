<?php

namespace SimpleCrest;

use GuzzleHttp;
use GuzzleHttp\Psr7\Request as Request;
use SimpleCrest\Exceptions\apiException;
use SimpleCrest\Exceptions\OAuthException;

class Endpoint
{
	const APIVersionThree = 3;

	/**
	 * CREST base
	 *
	 * @var string
	 */
	private static $crestBase = "https://crest-tq.eveonline.com/";

	/**
	 * @var GuzzleHttp\Client
	 */
	private $client;
	/**
	 * @var GuzzleHttp\Psr7\Response
	 */
	private $response;

	/**
	 * @var FactoryInterface
	 */
	private $factory;

	/**
	 * @var ObjectInterface
	 */
	private $object;

	/**
	 * Crest api version selection
	 *
	 * @var string
	 */
	private $apiVersion = "3";

	private $token = '';
	private $oauth = false;
	private $uri = '';

	/**
	 * Endpoint constructor
	 *
	 * @param ObjectInterface $object
	 * @throws OAuthException
	 */
	public function __construct(int $apiVersion, string $uri, bool $oauth = false, string $token = '')
	{
		// When making AuthRequest but token isn't provided, throw OAuthException
		if ($oauth and empty($token)) {
			throw new OAuthException();
		}

		$this->apiVersion = $apiVersion;
		$this->oauth = $oauth;
		$this->token = $token;

		$this->client = new GuzzleHttp\Client($this->headers($oauth));

		$this->uri = $uri;
	}

	private function getApiVersion()
	{
		return "v" . $this->apiVersion;
	}

	/**
	 * Create Public and Auth headers for CREST
	 *
	 * @param bool $oauth
	 * @return array
	 */
	public function headers($oauth = FALSE)
	{
		if ($oauth) {
			$headers = [
				'base_uri' => self::$crestBase,
				'headers'  => [
					'User-Agent'    => 'siggy/SimpleCrest',
					'Accept'        => 'application/vnd.ccp.eve.Api-' . $this->getApiVersion() . '+json; charset=utf-8',
					'Authorization' => 'Bearer ' . $this->token,
				]
			];
		} else {
			$headers = [
				'base_uri' => self::$crestBase,
				'headers'  => [
					'User-Agent' => 'siggy/SimpleCrest',
					'Accept'     => 'application/vnd.ccp.eve.Api-' . $this->getApiVersion() . '+json; charset=utf-8',
				]
			];
		}

		return $headers;
	}

	/**
	 * Make HTTP Request
	 *
	 * @param string $method
	 * @param string $uri
	 * @param array  $options
	 * @return array
	 * @throws apiException
	 */
	private function http($method, $uri, $options = [])
	{
		try {
			$this->response = $this->client->request($method, $uri, $options);
		} catch (GuzzleHttp\Exception\RequestException $e) {
			$this->ExceptionHandler($e);
		}
		$return = $this->response->getBody()->getContents();

		return json_decode($return, TRUE);
	}

	/**
	 * @param GuzzleHttp\Exception\RequestException $e
	 * @throws apiException
	 */
	private function ExceptionHandler(GuzzleHttp\Exception\RequestException $e)
	{
		$json = $e->getResponse()->getBody()->getContents();
		$message = $json;

		throw new apiException($message);
	}

	/**
	 * @param ObjectInterface $body
	 * @param int|null        $id
	 * @param array           $options
	 * @return ObjectInterface|void
	 */
	function put($body, $id = NULL, $options = [])
	{
		$uri = $this->uri;

		if ($id) {
			$uri = str_replace('{id}', $id, $uri);
		}

		// Add body to options as json
		$options["json"] = $body;

		// If Async is enabled, we use httpAsync function to make requests
		$content = $this->http("put", $uri, $options);

		$resp = new Response($this->response->getStatusCode(), $content);

		return $resp;
	}

	/**
	 * Create GET request on specific resource or root uri
	 *
	 * @param int   $id
	 * @param array $options
	 * @return ObjectInterface|void
	 */
	public function get($id = NULL, $options = [])
	{
		$uri = $this->uri;

		if ($id) {
			$uri = str_replace('{id}', $id, $uri);
		}

		$content = $this->http("get", $uri, $options);

		$resp = new Response($this->response->getStatusCode(), $content);

		return $resp;
	}

	/**
	 * @param ObjectInterface|array $body
	 * @param integer|null          $id
	 * @param array                 $options
	 * @return ObjectInterface
	 */
	public function post($body, $id = NULL, $options = [])
	{
		$uri = $this->uri;

		if ($id) {
			$uri = str_replace('{id}', $id, $uri);
		}

		// Add body to options as json
		$options["json"] = $body;

		$content = $this->http("post", $uri, $options);

		if (!$content) {
			$content = [];
		}

		$resp = new Response($this->response->getStatusCode(), $content);

		return $resp;
	}
}