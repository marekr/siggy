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
			ESI\Api\Configuration::getDefaultConfiguration()->setAccessToken($sso->access_token);
			$api_instance = new ESI\Api\UserInterfaceApi();
			$destination_id = $systemId; // int | The destination to travel to, can be solar system, station or structure's id
			$clear_other_waypoints = $waypoint; // bool | Whether clean other waypoints beforing adding this one
			$add_to_beginning = false; // bool | Whether this solar system should be added to the beginning of all waypoints
			$datasource = "tranquility"; // string | The server name you would like data from
			try {
				$api_instance->postUiAutopilotWaypoint($destination_id, $clear_other_waypoints, $add_to_beginning, $datasource);
			} catch (Exception $e) {
			}
		}

		$output = [];
		$this->response->json($output);
	}

}
