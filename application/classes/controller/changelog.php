<?php

use \cebe\markdown\Markdown;

class Controller_Changelog extends FrontController {
	public $template = 'template/public_bootstrap32';
	protected $noAutoAuthRedirects = true;

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}

	public function action_index()
	{
		$this->title = 'Changelog';
		$this->template->title = 'siggy';
		$this->template->selectedTab = 'siggy';
		$this->template->loggedIn = Auth::loggedIn();
		$this->template->user = Auth::$user;
		$this->template->layoutMode = 'blank';
		$changelog = file_get_contents(DOCROOT.'changelog.md');

		$parser = new \cebe\markdown\GithubMarkdown();
		$parser->html5 = true;
		$changelog = $parser->parse($changelog);

		$view = View::factory('changelog/index');
		$view->log = $changelog;
		$this->template->content = $view;
	}

}