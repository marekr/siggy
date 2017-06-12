<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use OpenCrest\OpenCrest;
use SimpleCrest\Endpoint;
use Siggy\ESI\Client as ESIClient;

use \Auth;
use Siggy\StandardResponse;

class CrestController extends Controller {

	public function waypoint(Request $request)
	{
		$req = json_decode($request->getContent(), true);
		$waypoint = ((bool)$req['waypoint'] != true);
		$systemId = (int)$req['system_id'];

		$success = false;
		$sso = Auth::$user->getActiveSSOCharacter();
		if( $sso == null )
		{
			return response()->json(StandardResponse::error("Something went horribly wrong, your sso token doesn't exist???"));
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

			$resp = null;
			try
			{
				$resp = $waypoints->post($body,$sso->character_id);
			}
			catch(\Exception $e)
			{
				$success = false;
			}

			if($resp != null && $resp->getStatusCode() == 200)
			{
				$success = true;
			}
		}
		else if($sso->scope_esi_ui_write_waypoint)
		{
			$client = new ESIClient($sso->access_token);
			$success = $client->postUiAutopilotWaypointV2($systemId, $waypoint, false);
		}

		if(!$success)
		{
			return response()->json(StandardResponse::error('Failed setting waypoint'));
		}

		return response()->json(StandardResponse::ok());
	}

}
