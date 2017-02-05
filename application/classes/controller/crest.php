<?php

use Carbon\Carbon;
use OpenCrest\OpenCrest;
use SimpleCrest\Endpoint;
use Siggy\ESI\Client as ESIClient;

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
		$this->response->noCache();

		$req = json_decode($this->request->body(), true);
		$waypoint = ((bool)$req['waypoint'] != true);
		$systemId = (int)$req['system_id'];

		$sso = Auth::$user->getActiveSSOCharacter();
		if( $sso == null )
		{
			return;
		}

		if($sso->scope_character_navigation_write)
		{
			$waypoints = new Endpoint(Endpoint::APIVersionThree,"/characters/{id}/ui/autopilot/waypoints/", true, $sso->access_token);

			$body = [
				"clearOtherWaypoints" => $waypoint,
				"first" => false,
				"solarSystem" => [
					"href" => "https://crest-tq.eveonline.com/solarsystems/".$systemId."/",
					"id" => $systemId
				]
			];

			$resp = $waypoints->post($body,$sso->character_id);
		}
		else if($sso->scope_esi_ui_write_waypoint)
		{
			$client = new ESIClient($sso->access_token);
			$client->postUiAutopilotWaypointV2($systemId, $waypoint, false);
		}

		$output = [];
		$this->response->json($output);
	}

}
