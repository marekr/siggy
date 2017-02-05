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
		$dayAgo = Carbon::now()->subDay();
		$groups = Group::where('billable',1)
			->where( function($q) use ($dayAgo) {
				$q->where('last_billing_charge_at', '<',$dayAgo)
					->orWhereNull('last_billing_charge_at');
			})
			->chunk(50, function ($groups) use ($dayAgo) {
				foreach($groups as $group)
				{
					$this->info("processing group {$group->id}");
					$activeChars = $group->activeCharsFromDate($dayAgo);
					if( $activeChars == 0 )
					{
						continue;
					}

					$cost = miscUtils::computeCostPerDays($activeChars, 1);

					$message = "Daily usage cost - {$activeChars} characters";
					$this->info($message);

					$insert = array(
										'amount' => $cost,
										'date' => time(),
										'groupID' => $group->id,
										'memberCount' => $activeChars,
										'message' => $message
									);
					$result = DB::table('billing_charges')->insert($insert);

					$group->applyISKCharge( $cost );
					$group->last_billing_charge_at = Carbon::now();
					$group->save();
				}
			});

		

		$this->info('Charged groups');
	}
}
