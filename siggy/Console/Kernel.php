<?php

namespace Siggy\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		\Siggy\Console\Commands\EveSystemStatsCommand::class,
		\Siggy\Console\Commands\SignaturesClearCommand::class,
		\Siggy\Console\Commands\BillingChargeCommand::class,
		\Siggy\Console\Commands\SessionsClearCommand::class,
		\Siggy\Console\Commands\BillingPaymentsCommand::class,
		\Siggy\Console\Commands\NotificationsClearCommand::class
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('sessions:clear')
		          ->hourly();
		$schedule->command('notifications:clear')
		          ->daily();
		$schedule->command('billing:payments')
		          ->hourly();
		$schedule->command('billing:charges')
		          ->dailyAt('00:00');
		$schedule->command('eve:systemstats')
		          ->hourly();
		$schedule->command('signatures:clear')
		          ->daily();
	}

	/**
	 * Register the Closure based commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{

	}
}
