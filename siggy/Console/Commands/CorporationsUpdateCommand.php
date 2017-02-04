<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use \Corporation;

class CorporationsUpdateCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'corps:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update corps';

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
		$this->info("syncing corps");
		Corporation::where('last_sync_attempt_at', '<',Carbon::now()->subMinutes(Corporation::SYNC_INTERVAL_MINUTES))
                      ->orWhere('last_sync_attempt_at')
			->chunk(20, function ($corps) {
				foreach ($corps as $corp) {
					$this->info("syncing corp {$corp->id} {$corp->name}");
					$corp->syncWithApi();
				}
			});
	}
}
