<?php

use \Michelf\Markdown;
use Illuminate\Database\Capsule\Manager as DB;

class Controller_Announcements extends FrontController {
	protected $noAutoAuthRedirects = true;

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}

	public function action_index()
	{
		$resultCount = DB::selectOne("SELECT COUNT(*) as total
									FROM announcements
									WHERE datePublished != 0 and visibility = 'all'");


		$pagination = new ZebraPagination2();
		$pagination->records($resultCount->total);
		$pagination->records_per_page(5);

		$paginationHTML = $pagination->render(true);
		$offset = $pagination->next_page_offset();

		$results = DB::select("SELECT *
									FROM announcements
									WHERE datePublished != 0 and visibility = 'all'
									ORDER BY datePublished DESC
									LIMIT ".$offset.",5");
		
		$resp = view('announcements.index', [
												'announcements' => $results,
												'pagination' => $paginationHTML,
												'title' => "siggy: announcements",
												'selectedTab' => 'announcements',
												'layoutMode' => 'blank'
											]);
		$this->response->body($resp);
	}

	public function action_view()
	{
		$this->profiler = NULL;

		$id = (int)$_GET['id'];

		$result = DB::selectOne("SELECT *
									FROM announcements
									WHERE datePublished != 0 and visibility = 'all'
									AND id = ?",[$id]);

		$this->response->json($result);
	}
}
