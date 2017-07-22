<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Siggy\Redis\RedisTtlCounter;

use Siggy\ESI\Client as ESIClient;

class RedisCleanupCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'redis:cleanup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Redis cleanup';

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
		$this->info("Clearing esi success");
		ESIClient::getEsiSuccessCounter()->cleanup();
		$this->info("Clearing esi failure");
		ESIClient::getEsiFailureCounter()->cleanup();

		$ttlcUsers = new RedisTtlCounter('ttlc:users:daily', 86400);
		$ttlcUsers->cleanup();
	}
}
