<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Siggy\ESI\Client as ESIClient;
use \Character;

class CharInfoCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'char:info {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Find esi character data by id';

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
		$id = $this->argument('id');
		
		$this->info("Searching eve api for {$id}");
		$result = Character::find($id);

		$this->info("results");
		print_r($result);
	}
}
