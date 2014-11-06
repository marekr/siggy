<?php

require_once APPPATH.'classes/FrontController.php';
require_once APPPATH.'classes/Zebra_Pagination2.php';

class Controller_Announcements extends FrontController
{
	private $auth;
	private $user;
	public $template = 'template/public_bootstrap32';
	protected $noAutoAuthRedirects = true;

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}

	public function action_index()
	{
		$resultCount = DB::query(Database::SELECT, "SELECT COUNT(*) as total
									FROM announcements
									WHERE datePublished != 0 and visibility = 'all'")
						->execute()
						->current();


		$pagination = new Zebra_Pagination2();
		$pagination->records($resultCount['total']);
		$pagination->records_per_page(5);

		$paginationHTML = $pagination->render(true);
		$offset = $pagination->next_page_offset();

		$view = View::factory('announcements/index');

		$results = DB::query(Database::SELECT, "SELECT *
									FROM announcements
									WHERE datePublished != 0 and visibility = 'all'
									ORDER BY datePublished DESC
									LIMIT ".$offset.",5")
								->execute()
								->as_array();


		$view->pagination = $paginationHTML;
		$view->announcements = $results;
		$this->template->content = $view;
	}

	public function before()
	{
		parent::before();

		$this->template->title = "siggy: announcements";
		$this->template->selectedTab = "announcements";
		$this->template->loggedIn = Auth::loggedIn();
		$this->template->user = Auth::$user->data;
		$this->template->layoutMode = 'blank';
	}
}
