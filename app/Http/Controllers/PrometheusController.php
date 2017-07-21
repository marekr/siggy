<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Siggy\Redis\RedisTtlCounter;
use Siggy\ESI\Client as ESIClient;

class PrometheusController extends Controller {
	
	public function index()
	{
		$resp = '';

		$resp .= sprintf("esi_requests{result=\"success\",ttl=\"%d\"} %d\n", ESIClient::$esiStatisticTtl,  ESIClient::getEsiSuccessCounter()->count());
		$resp .= sprintf("esi_requests{result=\"failure\",ttl=\"%d\"} %d\n", ESIClient::$esiStatisticTtl, ESIClient::getEsiFailureCounter()->count());
		
		$activeUsers = Redis::keys("siggy:actives:user#*");
		$resp .= sprintf("siggy_users{ttl=\"%d\"} %d\n", 60, count($activeUsers));
		
		$trackedCharacters = Redis::keys("siggy:location:character#*");
		$resp .= sprintf("siggy_tracked_characters %d\n", count($trackedCharacters));

		$ttlcUsers = new RedisTtlCounter('ttlc:users:daily', 86400);
		$resp .= sprintf("siggy_users{ttl=\"%d\"} %d\n", 86400, $ttlcUsers->count());

		return response($resp)->header('Content-Type', 'text/plain; version=0.0.4');
	}
}
