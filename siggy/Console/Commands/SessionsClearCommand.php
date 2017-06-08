<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionsClearCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sessions:clear';

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

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		//30 minute cutoff
		$cutoff = Carbon::now()->subMinutes(30)->toDateTimeString();

		DB::table('sessions')
			->where('updated_at', '<=', $cutoff)
			->delete();

		$this->info('Deleted old sessions');
	}
}
