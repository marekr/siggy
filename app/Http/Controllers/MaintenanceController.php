<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceController extends Controller {

	public function index()
	{
		if ($this->isDownForMaintenance()) {
			$data = json_decode(file_get_contents(storage_path('/framework/down')), true);
			
			response()->headers('Retry-After',$data['retry']);

			$changelog = file_get_contents(DOCROOT.'changelog.md');

			$parser = new \cebe\markdown\GithubMarkdown();
			$parser->html5 = true;
			$changelog = $parser->parse($changelog);

			return response()->view('errors.maintenance', [
													'message' => $data['message'],
													'wentDownAt' => Carbon::createFromTimestamp($data['time']),
													'changelog' => $changelog
												], 503);
		}
		else {
			HTTP::redirect('/');
		}
	}

}
