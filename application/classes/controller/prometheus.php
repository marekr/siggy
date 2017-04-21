<?php

use Illuminate\Database\Capsule\Manager as DB;

use Siggy\ESI\Client as ESIClient;

class Controller_Prometheus extends FrontController {
	protected $noAutoAuthRedirects = true;

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}

	public function action_index()
	{
		$resp = '';

		$resp .= sprintf("esi_requests{result=\"success\",ttl=\"%d\"} %d\n", ESIClient::$esiStatisticTtl,  ESIClient::getEsiSuccessCounter()->count());
		$resp .= sprintf("esi_requests{result=\"failure\",ttl=\"%d\"} %d\n", ESIClient::$esiStatisticTtl, ESIClient::getEsiFailureCounter()->count());

		$this->response->headers('Content-Type', 'text/plain; version=0.0.4');
		$this->response->body($resp);
	}
}
