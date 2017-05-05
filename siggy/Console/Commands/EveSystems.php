<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use \System;

class EveSystems extends Command
{
	/**
	* The name and signature of the console command.
	*
	* @var string
	*/
	protected $signature = 'eve:systems';

	/**
	* The console command description.
	*
	* @var string
	*/
	protected $description = 'Command description';

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
		DB::table('solarsystems')->truncate();

		$classMap = DB::table('eve_map_location_wormhole_classes')
							->select(['locationID', 'wormholeClassID'])
							->get()
							->keyBy('locationID')
							->all();
							
		$systems = DB::table('eve_map_solar_systems')
						->orderBy('solarSystemID', 'ASC')
						->chunk(20, function($systems){
			foreach($systems as $system)
			{
				$insert = [
					'id' => $system->solarSystemID,
					'name' => $system->solarSystemName,
					'region' => $system->regionID,

					'truesec' => $system->security,
					'sec' => round($system->security,1),
					'constellation' => $system->constellationID,
					'radius' => (($system->radius/1000)/149598000),
					'class' => 9,
					'x' => $system->x,
					'y' => $system->y,
					'z' => $system->z
				];

				if( isset( $classMap[ $system->regionID ] ) )
				{
					$insert['class'] = $classMap[ $system->regionID ]->wormholeClassID;
				}

				//system class maps override region
				if( isset( $classMap[ $system->solarSystemID ] ) )
				{
					$insert['class'] = $classMap[ $system->solarSystemID ]->wormholeClassID;
				}

				$insert['planets'] = DB::table('eve_map_denormalize')
										->where('solarSystemID', $system->solarSystemID)
										->where('groupID', 7)
										->count();
				
				$insert['moons'] = DB::table('eve_map_denormalize')
										->where('solarSystemID', $system->solarSystemID)
										->where('groupID', 8)
										->count();

				$insert['belts'] = DB::table('eve_map_denormalize')
										->where('solarSystemID', $system->solarSystemID)
										->where('groupID', 9)
										->count();
										

				$insert['effect'] = DB::table('eve_map_denormalize')
										->where('solarSystemID', $system->solarSystemID)
										->where('groupID', 995)
										->value('typeID');


				System::create($insert);
			}
		});
	}
}
