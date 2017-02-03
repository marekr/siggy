<?php

use \cebe\markdown\Markdown;

class Controller_Changelog extends FrontController {
	protected $noAutoAuthRedirects = true;

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}

	public function action_index()
	{
		$changelog = file_get_contents(DOCROOT.'changelog.md');

		$parser = new \cebe\markdown\GithubMarkdown();
		$parser->html5 = true;
		$changelog = $parser->parse($changelog);
		
		$resp = view('changelog.index', [
												'log' => $changelog,
												'title' => 'Changelog',
												'selectedTab' => 'changelog',
												'layoutMode' => 'blank'
											]);
		$this->response->body($resp);
	}

}