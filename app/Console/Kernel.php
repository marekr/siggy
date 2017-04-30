<?php

namespace App\Console;

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
		\Siggy\Console\Commands\AssetsCompileCommand::class,
		\Siggy\Console\Commands\GroupFixCodesCommand::class,
		\Siggy\Console\Commands\EveSystemStatsCommand::class,
		\Siggy\Console\Commands\SignaturesClearCommand::class,
		\Siggy\Console\Commands\BillingChargeCommand::class,
		\Siggy\Console\Commands\SessionsClearCommand::class,
		\Siggy\Console\Commands\BillingPaymentsCommand::class,
		\Siggy\Console\Commands\NotificationsClearCommand::class,
		\Siggy\Console\Commands\CorporationsUpdateCommand::class,
		\Siggy\Console\Commands\RedisCleanupCommand::class
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
		          ->hourlyAt(00);
		$schedule->command('notifications:clear')
		          ->daily();
		$schedule->command('billing:payments')
		          ->hourlyAt(05);
		$schedule->command('billing:charge')
		          ->daily();
		$schedule->command('eve:systemstats')
		          ->hourlyAt(01);
		$schedule->command('signatures:clear')
		          ->dailyAt('01:00');
		$schedule->command('corps:update')
		          ->dailyAt('02:00');
		$schedule->command('redis:cleanup')
		          ->dailyAt('03:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
