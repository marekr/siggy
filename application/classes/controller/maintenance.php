<?php

use Carbon\Carbon;

class Controller_Maintenance extends FrontController {
	protected $noAutoAuthRedirects = true;

	public function action_index()
	{
		
		if ($this->isDownForMaintenance()) {
			$data = json_decode(file_get_contents($this->storagePath().'/framework/down'), true);
			
			$this->response->headers('Retry-After',$data['retry']);

			$changelog = file_get_contents(DOCROOT.'changelog.md');

			$parser = new \cebe\markdown\GithubMarkdown();
			$parser->html5 = true;
			$changelog = $parser->parse($changelog);

			$this->response->status(503);
			$resp = view('errors.maintenance', [
													'message' => $data['message'],
													'wentDownAt' => Carbon::createFromTimestamp($data['time']),
													'changelog' => $changelog
												]);
												
			$this->response->body($resp);
		}
		else {
			HTTP::redirect('/');
		}
	}
}