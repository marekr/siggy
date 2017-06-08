<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;
use Illuminate\Support\Facades\DB;

class StaticDataImporter extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'staticdata:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'C';

	/**
	 * An SQL INSERT query will execute every time this number of rows
	 * are read from the CSV. Without this, large INSERTS will silently
	 * fail.
	 *
	 * @var int
	 */
	public $insert_chunk_size = 50;

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
		DB::disableQueryLog();
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');

		$files = [
			[
				'table' => 'sites',
				'file' => 'staticdata/sites.csv'
			],
			[
				'table' => 'pos_types',
				'file' => 'staticdata/pos_types.csv'
			],
			[
				'table' => 'site_class_map',
				'file' => 'staticdata/site_class_map.csv'
			],
			[
				'table' => 'staticmap',
				'file' => 'staticdata/staticmap.csv'
			],
			[
				'table' => 'statics',
				'file' => 'staticdata/statics.csv'
			],
			[
				'table' => 'structure_types',
				'file' => 'staticdata/structure_types.csv'
			],
			[
				'table' => 'themes',
				'file' => 'staticdata/themes.csv'
			],
			[
				'table' => 'wormhole_class_map',
				'file' => 'staticdata/wormhole_class_map.csv'
			],
			[
				'table' => 'systemeffects',
				'file' => 'staticdata/systemeffects.csv'
			],
		];

		foreach($files as $file)
		{
			$this->info("Importing " . $file['table']);
			DB::table($file['table'])->truncate();
			$this->seedFromCSV($file['table'], storage_path($file['file']), ',');
		}
		
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}


	/**
	 * Strip UTF-8 BOM characters from the start of a string
	 *
	 * @param  string $text
	 * @return string       String with BOM stripped
	 */
	public function stripUtf8Bom( $text )
	{
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $text);

		return $text;
	}

	/**
		* Opens a CSV file and returns it as a resource
		*
		* @param $filename
		* @return FALSE|resource
		*/
	public function openCSV($filename)
	{
		if ( !file_exists($filename) || !is_readable($filename) )
		{
			$this->error("CSV insert failed: CSV " . $filename . " does not exist or is not readable.");
			return FALSE;
		}

		// check if file is gzipped
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$file_mime_type = finfo_file($finfo, $filename);
		finfo_close($finfo);
		$gzipped = strcmp($file_mime_type, "application/x-gzip") == 0;

		$handle = $gzipped ? gzopen($filename, 'r') : fopen($filename, 'r');

		return $handle;
	}

	/**
	 * Collect data from a given CSV file and return as array
	 *
	 * @param string $filename
	 * @param string $deliminator
	 * @return array|bool
	 */
	public function seedFromCSV($table, $filename, $deliminator = ",", $offsetRows = 0, $providedMapping = [])
	{
		$handle = $this->openCSV($filename);

		// CSV doesn't exist or couldn't be read from.
		if ( $handle === FALSE )
			return [];

		$header = NULL;
		$row_count = 0;
		$data = [];
		$mapping = $providedMapping ?: [];
		$offset = $offsetRows;

		while ( ($row = fgetcsv($handle, 0, $deliminator)) !== FALSE )
		{
			// Offset the specified number of rows

			while ( $offset > 0 )
			{
				$offset--;
				continue 2;
			}

			// No mapping specified - grab the first CSV row and use it
			if ( !$mapping )
			{
				$mapping = $row;
				$mapping[0] = $this->stripUtf8Bom($mapping[0]);

				// skip csv columns that don't exist in the database
				foreach($mapping  as $index => $fieldname){
					if (!DB::getSchemaBuilder()->hasColumn($table, $fieldname)){
						array_pull($mapping, $index);
					}
				}
			}
			else
			{
				$row = $this->readRow($row, $mapping);

				// insert only non-empty rows from the csv file
				if ( !$row )
					continue;

				$data[$row_count] = $row;

				// Chunk size reached, insert
				if ( ++$row_count == $this->insert_chunk_size )
				{
					$this->insert($table, $data);
					$row_count = 0;
					// clear the data array explicitly when it was inserted so
					// that nothing is left, otherwise a leftover scenario can
					// cause duplicate inserts
					$data = array();
				}
			}
		}
		
		// Insert any leftover rows
		//check if the data array explicitly if there are any values left to be inserted, if insert them
		if ( count($data)  )
			$this->insert($table, $data);

		fclose($handle);

		return $data;
	}

	/**
	* Read a CSV row into a DB insertable array
	*
	* @param array $row        List of CSV columns
	* @param array $mapping    Array of csvCol => dbCol
	* @return array
	*/
	public function readRow( array $row, array $mapping )
	{
		$row_values = [];

		foreach ($mapping as $csvCol => $dbCol) {
			if (!isset($row[$csvCol]) || $row[$csvCol] === '') {
				$row_values[$dbCol] = NULL;
			}
			else {
				$row_values[$dbCol] = $row[$csvCol];
			}
		}

		return $row_values;
	}

	/**
	* Seed a given set of data to the DB
	*
	* @param array $seedData
	* @return bool   TRUE on success else FALSE
	*/
	public function insert( string $table, array $seedData )
	{
		try {
			DB::table($table)->insert($seedData);
		} catch (\Exception $e) {
			$this->error("CSV insert failed: " . $e->getMessage() );
			return FALSE;
		}

		return TRUE;
	}
}
