<?php 

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Siggy\Structure;
use Siggy\POS;
use \stdClass;
use \Auth;
use \ScribeCommandBus;

class SiggyController extends Controller {

	public function index($system = null)
	{
		// set default
		$sysData = new stdClass();
		$sysData->id = 30000142;
		$sysData->name = 'Jita';

	//	$activeChar = Auth::$user->getActiveSSOCharacter();
	//	ScribeCommandBus::UnfreezeCharacter($activeChar->character_owner_hash);

	}

}
