<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use \Group;
use \miscUtils;

class BillingChargeCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'billing:charge';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Charge groups';

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
		$groups = Group::where('billable',1)->get();
		foreach($groups as $group)
		{
			$numUsers = $group->getCharacterUsageCount();
			if( $numUsers == 0 )
			{
				continue;
			}

			$cost = miscUtils::computeCostPerDays($numUsers, 1);

			$message = 'Daily usage cost - ' . $numUsers . ' characters';

			$insert = array(
								'amount' => $cost,
								'date' => time(),
								'groupID' => $group->id,
								'memberCount' => $numUsers,
								'message' => $message
							);
			$result = DB::table('billing_charges')->insert($insert);

			$group->applyISKCharge( $cost );
		}

		$this->info('Charged groups');
	}
}
