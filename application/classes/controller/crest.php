<?php

use Carbon\Carbon;
use OpenCrest\OpenCrest;
use SimpleCrest\Endpoint;

class Controller_Crest extends FrontController {
	protected $output_array = array();

	public function before()
	{
		parent::before();

		$this->validateCSRF();
	}

	public function action_waypoint()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$req = json_decode($this->request->body(), true);
		$waypoint = ((bool)$req['waypoint'] != true);
		$systemId = (int)$req['system_id'];

		$sso = Auth::$user->getActiveSSOCharacter();
		if( $sso == null )
		{
			return;
		}

		$waypoints = new Endpoint(Endpoint::APIVersionThree,"/characters/{id}/ui/autopilot/waypoints/", true, $sso['access_token']);

		$body = [
			"clearOtherWaypoints" => $waypoint,
			"first" => false,
			"solarSystem" => [
				"href" => "https://crest-tq.eveonline.com/solarsystems/".$systemId."/",
				"id" => $systemId
			]
		];

		$resp = $waypoints->post($body,$sso['character_id']);

		$output = [];
		$this->response->body(json_encode($output));
	}

}
