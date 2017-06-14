<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Siggy\SolarSystemJump;
use Siggy\SolarSystemKill;
use Siggy\ESI\Client as ESIClient;

class EveSystemStatsCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'eve:systemstats';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update system statistics';

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
		$client = new ESIClient();
		SolarSystemJump::where('date_end','<=', Carbon::now()->subDay(1))->delete();

		$this->info('Updating jumps...');
		$jumps = $client->getUniverseSystemJumpsV1();
		try
		{
			if($jumps != null)
			{
				foreach($jumps['records'] as $entry)
				{
					SolarSystemJump::create([
						'system_id' => $entry->system_id,
						'ship_jumps' => $entry->ship_jumps,
						'date_start' => $jumps['dateStart'],
						'date_end' => $jumps['dateEnd'],
					]);
				}
			}
		}
		catch(\Exception $e)
		{
			$this->info('Error updating jumps, most likely duplicate records, skipping');
		}
		finally
		{
			unset($jumps);
		}

		$this->info('Updating kills...');
		$kills = $client->getUniverseSystemKillsV1();
		try
		{
			if($kills != null)
			{
				foreach($kills['records'] as $entry)
				{
					SolarSystemKill::create([
						'system_id' => $entry->system_id,
						'ship_kills' => $entry->ship_kills - $entry->npc_kills, // bug fix for v1 endpoint
						'pod_kills' => $entry->pod_kills,
						'npc_kills' => $entry->npc_kills,
						'date_start' => $kills['dateStart'],
						'date_end' => $kills['dateEnd'],
					]);
				}
			}
		}
		catch(\Exception $e)
		{
			$this->info('Error updating kills, most likely duplicate records, skipping');
		}
		finally
		{
			unset($kills);
		}

		$this->info('Updated system stats');
	}
}
