<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Siggy\Ship;

class EveShips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eve:ships';

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
        DB::table('ships')->truncate();
		$ships = DB::select('SELECT t.*,
												   g.groupName,
												   r.raceName,
												   m.marketGroupName marketGroupName,
												   mp.marketGroupName marketParentGroupName
												FROM
												  eve_chr_races AS r
												  INNER JOIN eve_inv_types AS t ON r.raceID = t.raceID
												  INNER JOIN eve_inv_groups AS g ON t.groupID = g.groupID
												  LEFT OUTER JOIN eve_inv_market_groups AS m ON t.marketGroupID = m.marketGroupID
												  LEFT JOIN eve_inv_market_groups AS mp ON m.parentGroupID = mp.marketGroupID
												WHERE
												  (g.categoryID = 6 AND t.published = 1)
                                                    OR t.groupID=29
												ORDER BY
												  t.typeName ASC');

        foreach($ships as $ship)
        {
			if( $ship->typeID == 29988 ) //proteus
			{
				$ship->mass = 15000000;
			}
			else if( $ship->typeID == 29986 ) // legion
			{
				$ship->mass = 16000000;
			}
			else if( $ship->typeID == 29984 ) // tengu
			{
				$ship->mass = 14000000;
			}
			else if( $ship->typeID == 29990 ) // loki
			{
				$ship->mass = 15000000;
			}

			$insert = [
                        'id' => $ship->typeID,
                        'name' => $ship->typeName,
                        'mass' => (double)$ship->mass,
                        'class' => $ship->groupName
                        ];
            Ship::create($insert);
        }
    }
}
