<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Siggy\ESI\Client as ESIClient;
use \Corporation;

class ESICorpSearchCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'esi:corpsearch {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear old sesssions';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$name = $this->argument('name');
		
		$this->info("Searching eve api for {$name}");
		$result = Corporation::searchEVEAPI($name);

		$this->info("results");
		foreach($result as $corp)
		{
			print $corp->name . " " . $corp->id . "\r\n";
		}
	}
}
