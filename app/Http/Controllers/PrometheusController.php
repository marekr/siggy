<?php

namespace App\Http\Controllers;

use Siggy\ESI\Client as ESIClient;

class PrometheusController extends Controller {
	
	public function index()
	{
		$resp = '';

		$resp .= sprintf("esi_requests{result=\"success\",ttl=\"%d\"} %d\n", ESIClient::$esiStatisticTtl,  ESIClient::getEsiSuccessCounter()->count());
		$resp .= sprintf("esi_requests{result=\"failure\",ttl=\"%d\"} %d\n", ESIClient::$esiStatisticTtl, ESIClient::getEsiFailureCounter()->count());

		return response($resp)->header('Content-Type', 'text/plain; version=0.0.4');
	}
}
