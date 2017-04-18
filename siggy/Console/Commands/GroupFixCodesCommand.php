<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use \Group;
use \miscUtils;

class GroupFixCodesCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'groups:fixcodes';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fix codes';

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
		Group::where('payment_code', '=','')
			->chunk(20, function ($groups) {
				foreach ($groups as $group) {
					$this->info("fixing corp {$group->id} {$group->name}");
					
					$group->payment_code = miscUtils::generateString(14);
					$group->save();
				}
			});
	}
}
