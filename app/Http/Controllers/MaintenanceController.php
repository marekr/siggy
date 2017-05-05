<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller {

	public function getIndex()
	{
		if (app()->isDownForMaintenance()) {
			$data = json_decode(file_get_contents(storage_path('/framework/down')), true);
			
			$changelog = file_get_contents(base_path('changelog.md'));

			$parser = new \cebe\markdown\GithubMarkdown();
			$parser->html5 = true;
			$changelog = $parser->parse($changelog);

			return response()
						->view('errors.maintenance', [
													'message' => $data['message'],
													'wentDownAt' => Carbon::createFromTimestamp($data['time']),
													'changelog' => $changelog
												], 503)
						->header('Retry-After',$data['retry']);
		}
		else {
			return redirect('/');
		}
	}

}
