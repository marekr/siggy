<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

class NotificationsClearCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'notifications:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear old notifications';

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
		//15 day cutoff
		$cutoff = time()-60*60*24*15;

		$query = DB::delete("DELETE FROM notifications WHERE created_at <= ?",[$cutoff]);

		$this->info('Deleted old notifications');
	}
}
