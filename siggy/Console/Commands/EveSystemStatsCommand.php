<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \PhealHelper;
use Pheal\Pheal;

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
		$cutoff = time()-(3600*24*2);

		DB::table('apihourlymapdata')
			->where('hourStamp', '<=', $cutoff)
			->delete();
		DB::table('jumpstracker')
			->where('hourStamp', '<=', $cutoff)
			->delete();

		$systems = DB::table('solarsystems')->orderBy('id')->get()->all();
		foreach($systems as &$system)
		{
			$system->jumps = 0;
			$system->kills = 0;
			$system->npcKills = 0;
			$system->podKills = 0;
		}

		PhealHelper::configure();
		$pheal = new Pheal('','');
		$pheal->scope = 'map';

		$jumpsData = $pheal->Jumps();
		foreach($jumpsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]->jumps = $ss->shipJumps;
			}
		}

		$killsData = $pheal->Kills();
		foreach($killsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]->kills = $ss->shipKills;
				$systems[ $ss->solarSystemID ]->npcKills = $ss->factionKills;
				$systems[ $ss->solarSystemID ]->podKills = $ss->podKills;
			}
		}


		date_default_timezone_set('UTC');
		$requestDateInfo = getdate( time() - 3600 );
		$time = gmmktime($requestDateInfo['hours'],0,0,$requestDateInfo['mon'],$requestDateInfo['mday'],$requestDateInfo['year']);

		foreach($systems as $system)
		{
			DB::insert('INSERT INTO apihourlymapdata (`systemID`,`hourStamp`, `jumps`, `kills`, `npcKills`, `podKills`) 
				VALUES(:systemID, :hourStamp, :jumps, :kills, :npcKills, :podKills) ON DUPLICATE KEY UPDATE systemID=systemID',[
					'systemID' => $system->id,
					'hourStamp' => $time,
					'jumps' => $system->jumps,
					'kills' => $system->kills,
					'npcKills' => $system->npcKills,
					'podKills' => $system->podKills
				]);

		}

		$this->info('Updated system stats');
	}
}
