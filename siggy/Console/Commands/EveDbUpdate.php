<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Schema;

class EveDbUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eve:dbupdate';

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
        $files = [
            [
                'source' => 'invGroups.sql.bz2',
                'original_table_name' => 'invGroups',
                'final_table_name' => 'eve_inv_groups',
            ],
            [
                'source' => 'invMarketGroups.sql.bz2',
                'original_table_name' => 'invMarketGroups',
                'final_table_name' => 'eve_inv_market_groups',
            ],
            [
                'source' => 'invTypes.sql.bz2',
                'original_table_name' => 'invTypes',
                'final_table_name' => 'eve_inv_types',
            ],
            [
                'source' => 'mapRegions.sql.bz2',
                'original_table_name' => 'mapRegions',
                'final_table_name' => 'eve_map_regions',
            ],
            [
                'source' => 'mapSolarSystems.sql.bz2',
                'original_table_name' => 'mapSolarSystems',
                'final_table_name' => 'eve_map_solar_systems',
            ],
            [
                'source' => 'mapConstellations.sql.bz2',
                'original_table_name' => 'mapConstellations',
                'final_table_name' => 'eve_map_constellations',
            ],
            [
                'source' => 'chrRaces.sql.bz2',
                'original_table_name' => 'chrRaces',
                'final_table_name' => 'eve_chr_races',
            ],
            [
                'source' => 'mapLocationWormholeClasses.sql.bz2',
                'original_table_name' => 'mapLocationWormholeClasses',
                'final_table_name' => 'eve_map_location_wormhole_classes',
            ],
            [
                'source' => 'mapDenormalize.sql.bz2',
                'original_table_name' => 'mapDenormalize',
                'final_table_name' => 'eve_map_denormalize',
            ],
            [
                'source' => 'mapSolarSystemJumps.sql.bz2',
                'original_table_name' => 'mapSolarSystemJumps',
                'final_table_name' => 'eve_map_solar_system_jumps',
            ],
        ];

        foreach($files as $file)
        {
            $fullFilePath = storage_path('evedb/'.$file['source']);
            $postFilePath = storage_path('evedb/'.str_replace('.bz2','',$file['source']));
            
            @unlink($postFilePath);
            $this->info("extracting {$file['source']}");
            $extract = sprintf('bzip2 -d -k "%s"',
                                $fullFilePath);

            $process = new Process($extract);
            $process->run();

            if (!$process->isSuccessful()) {
                @unlink($postFilePath);
                throw new ProcessFailedException($process);
            }
            
            $this->info("importing {$file['source']}");

			$dbConnectionBase = 'database.connections.'.config('database.default');

            $command = sprintf("mysql -u %s -p%s %s < %s",
                                config("{$dbConnectionBase}.username"),
                                config("{$dbConnectionBase}.password"),
                                config("{$dbConnectionBase}.database"),
                                $postFilePath);

            $process = new Process($command);
            $process->run();

            if (!$process->isSuccessful()) {
                @unlink($postFilePath);
                throw new ProcessFailedException($process);
            }
            
            @unlink($postFilePath);

            Schema::dropIfExists($file['final_table_name']);
            Schema::rename($file['original_table_name'],$file['final_table_name']);
        }
    }
}
