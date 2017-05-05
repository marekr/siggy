<?php

use Illuminate\Database\Capsule\Manager as DB;

class Pathfinder {

	public $jumps = array();

	public function __construct()
	{
		$this->jumps = array();

		$cache = Cache::instance( CACHE_METHOD );
		$cacheName = 'pathfinder_jumps_cache';

		if( !($this->jumps = $cache->get( $cacheName, FALSE ) ) )
		{
			$data = DB::table('eve_map_solar_system_jumps')
					->orderBy('fromSolarSystemID', 'ASC')
					->get()
					->all();

			$this->jumps = [];
			foreach($data as $j)
			{
				if( !isset($this->jumps[$j->fromSolarSystemID]) )
				{
					$this->jumps[$j->fromSolarSystemID] = [];
				}
				$this->jumps[$j->fromSolarSystemID][] = $j->toSolarSystemID;
			}

			$cache->set($cacheName, $this->jumps);
		}
	}

	public function shortest($origin, $target)
	{
		$jumpResult = array(
			'origin' => $origin,
			'destination' => $target,
			'jumps' => 'N/A',
			'distance' => -1
		);

		// Start the fun
		if (isset($this->jumps[$origin]) && isset($this->jumps[$target])) {

			// Target and origin the same, no distance
			if ($target == $origin) {
				$jumpResult['jumps'] = $origin;
				$jumpResult['distance'] = 0;
			}

			// Target is a neigbour system of origin
			elseif (in_array($target, $this->jumps[$origin]))
			{
				$jumpResult['jumps'] = $origin . ',' . $target;
				$jumpResult['distance'] = 1;
			}

			// Lets start the fun
			else {
				// Will contain the system IDs
				$resultPath = array();
				// Already visited system
				$visitedSystems = array();
				// Limit the number of iterations
				$remainingJumps = 9000;
				// Systems we can reach from here
				$withinReach = array($origin);

				while (count($withinReach) > 0 && $remainingJumps > 0 && count($resultPath) < 1) {
					$remainingJumps--;

					// Jump to the first system within reach
					$currentSystem = array_shift($withinReach);

					// Get the IDs of the systems, connected to the current
					$links = $this->jumps[$currentSystem];
					$linksCount = count($links);

					// Test all connected systems
					for($i = 0; $i < $linksCount; $i++) {
						$neighborSystem = $links[$i];

						// If neighbour system is the target,
						// Build an array of ordered system IDs we need to
						// visit to get from thhe origin system to the
						// target system
						if ($neighborSystem == $target) {
							$resultPath[] = $neighborSystem;
							$resultPath[] = $currentSystem;
							while ($visitedSystems[$currentSystem] != $origin) {
								$currentSystem = $visitedSystems[$currentSystem];
								$resultPath[] = $currentSystem;
							}
							$resultPath[] = $origin;
							$resultPath = array_reverse($resultPath);
							break;
						}

						// Otherwise, store the current - neighbour
						// Connection in the visited systems and add the
						// neighbour to the systems within reach
						else if (!isset($visitedSystems[$neighborSystem])) {
							$visitedSystems[$neighborSystem] = $currentSystem;
							array_push($withinReach, $neighborSystem);
						}
					}
				}

				// If the result path is filled, we have a connection
				if (count($resultPath) > 1) {
					$jumpResult['distance'] = count($resultPath) - 1;
					$jumpResult['jumps'] = implode(',', $resultPath);
				}
			}
		}

		return $jumpResult;
	}
}
