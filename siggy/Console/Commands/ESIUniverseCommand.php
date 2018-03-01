<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Siggy\ESI\Client as ESIClient;

class ESIUniverseCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'esi:universe {mode}';

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

	public function doJumps()
	{
		$client = new ESIClient();

		print_r($client->getUniverseSystemJumpsV1());	
	}

	public function doKills()
	{
		$client = new ESIClient();

		print_r($client->getUniverseSystemKillsV2());	
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$mode = $this->argument('mode');

		$this->info("ESI Universe Mode: {$mode}");

		switch($mode)
		{
			case "jumps":
				$this->doJumps();
				break;
			case "kills":
				$this->doKills();
				break;
			default:
				$this->error("No mode given");
				break;
		}
	}
}
