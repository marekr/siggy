<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SignaturesClearCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'signatures:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear old signatures';

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
		//two days?
		$cutoff = Carbon::now()->subDays(26)->toDateTimeString();
		$whCutoff = Carbon::now()->subDays(2)->toDateTimeString();

		$groups = DB::select("SELECT id,skip_purge_home_sigs FROM groups");
		foreach( $groups as $group )
		{
			$ignoreSys = '';
			$chains = DB::select("SELECT homesystems_ids FROM chainmaps
													WHERE group_id = ? AND
													skip_purge_home_sigs=1",[$group->id]);

			if( $chains != null )
			{
				$ignoreSys = array();
				foreach( $chains as $c )
				{
					if( !empty($c->homesystems_ids) )
					{
						$ignoreSys[] = $c->homesystems_ids;
					}
				}

				$ignoreSys = implode(',', $ignoreSys);
			}

			$ignoreSysExtra = '';
			if( !empty($ignoreSys) )
			{
				$ignoreSysExtra = "systemID NOT IN(".$ignoreSys.") AND ";
			}

			
			$query = DB::delete("DELETE FROM systemsigs 
								WHERE sig != 'POS' AND
									groupID=:groupID AND
									{$ignoreSysExtra}
									( created_at <= :cutoff OR (type = 'wh' AND created_at <= :whcutoff))",[
										'cutoff' => $cutoff,
										'groupID' => $group->id,
										'whcutoff' => $whCutoff
									]);
		}

		$this->info('Deleted old signatures');
	}
}
