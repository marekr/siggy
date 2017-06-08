<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use OpenCrest\OpenCrest;
use SimpleCrest\Endpoint;
use Siggy\ESI\Client as ESIClient;

use \Auth;

class CrestController extends Controller {

	public function waypoint(Request $request)
	{
		$req = json_decode($request->getContent(), true);
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
		return response()->json($output);
	}

}
