<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use \Michelf\Markdown;

class AnnouncementController extends BaseController {

	public function list()
	{
		$resultCount = DB::selectOne("SELECT COUNT(*) as total
									FROM announcements
									WHERE datePublished != 0 and visibility = 'all'");

		$pagination = new \ZebraPagination2();
		$pagination->records($resultCount->total);
		$pagination->records_per_page(5);

		$paginationHTML = $pagination->render(true);
		$offset = $pagination->next_page_offset();

		$results = DB::select("SELECT *
									FROM announcements
									WHERE datePublished != 0 and visibility = 'all'
									ORDER BY datePublished DESC
									LIMIT ".$offset.",5");
		
		return view('announcements.index', [
												'announcements' => $results,
												'pagination' => $paginationHTML,
												'title' => "announcements",
												'selectedTab' => 'announcements',
												'layoutMode' => 'blank'
											]);
	}

	public function view($id)
	{
		$result = DB::selectOne("SELECT *
									FROM announcements
									WHERE datePublished != 0 and visibility = 'all'
									AND id = ?",[$id]);

		return response()->json($result);
	}
}
