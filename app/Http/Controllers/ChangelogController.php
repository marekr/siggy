<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChangelogController extends Controller {

	public function index()
	{
		$path = file_get_contents(base_path('CHANGELOG.md'));
		$parser = new \cebe\markdown\GithubMarkdown();
		$parser->html5 = true;
		$changelog = $parser->parse($path);
		
		return view('changelog.index', [
												'log' => $changelog,
												'title' => 'Changelog',
												'selectedTab' => 'changelog',
												'layoutMode' => 'blank'
											]);
	}

}
