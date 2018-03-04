<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Siggy\ESI\Client as ESIClient;

use App\Facades\Auth;
use App\Facades\SiggySession;
use Siggy\StandardResponse;

use Siggy\UserESITokenManager;

class CrestController extends Controller {

	public function waypoint(Request $request)
	{
		$req = json_decode($request->getContent(), true);
		$waypoint = ((bool)$req['waypoint'] != true);
		$systemId = (int)$req['system_id'];

		$success = false;
		$sso = Auth::user()->getActiveSSOCharacter();
		if( $sso == null )
		{
			return response()->json(StandardResponse::error("Something went horribly wrong, your sso token doesn't exist???"));
		}

		$tokenManager = new UserESITokenManager();
		if($sso->scope_esi_ui_write_waypoint)
		{
			$client = new ESIClient($tokenManager);

			try {
				$success = $client->postUiAutopilotWaypointV2($systemId, $waypoint, false);
			}
			catch(\Exception $e) {
				$success = false;
			}
		}

		if(!$success)
		{
			return response()->json(StandardResponse::error('Failed setting waypoint'));
		}

		return response()->json(StandardResponse::ok());
	}

}
