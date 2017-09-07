<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use \miscUtils;
use App\Facades\Auth;
use App\Facades\SiggySession;


class DScanController extends Controller {

	protected $dscan_item_cache = array();
	protected $output_array = array();

	public function add(Request $request)
	{
		$postData = json_decode($request->getContent(), true);
		$blob = $postData['blob'];

		$test = $this->parse_csv( $blob, "\t" );

		/*
			Index 0 - custom name
			Index 1 - real name
			Index 2 - distance
		*/
		foreach($test as $entry)
		{
			$typeID = 0;

			/* skip entries with no names */
			if( !isset( $entry[1] ) )
			{
				continue;
			}

			/* check the cache first, else query */
			if( !isset($this->dscan_item_cache[ $entry[1] ] ) )
			{
				$itemData = DB::selectOne('SELECT typeID FROM eve_inv_types
										WHERE typeName LIKE ?', [$entry[1]]);

				if( isset($itemData->typeID) )
				{
					$typeID = $this->dscan_item_cache[ $entry[1] ] = $itemData->typeID;
				}
			}
			else
			{
				$typeID = $this->dscan_item_cache[ $entry[1] ];
			}

			if( $typeID != 0  )
			{
				$this->output_array[] = array( 'type_id' => $typeID, 'name' => $entry[0], 'item_distance' => $entry[2] );
			}

		}


		if( count( $this->output_array ) > 0 )
		{
			$id = miscUtils::generateString(14);

			$data = array(
				'dscan_date' => time(),
				'system_id' => intval($postData['system_id']),
				'group_id' => SiggySession::getGroup()->id,
				'dscan_title' => htmlentities($postData['dscan_title']),
				'dscan_id' => $id,
				'dscan_added_by' => SiggySession::getCharacterName()
			);

			$dscanID = DB::table('dscan')->insert($data);
			//print_r($this->output_array);
			foreach( $this->output_array as $rec )
			{
				$insert = array('dscan_id' => $id,
								 'type_id' => $rec['type_id'],
								'record_name' => htmlentities($rec['name']),
								'item_distance' => $rec['item_distance'] );
				$posID = DB::table('dscan_records')->insert($insert);
			}
		}

		return response()->json([true]);
	}

	function parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true)
	{
		$enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
		$enc = preg_replace_callback(
			'/"(.*?)"/s',
			function ($field) {
				return urlencode(utf8_encode($field[1]));
			},
			$enc
		);
		$lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);
		return array_map(
			function ($line) use ($delimiter, $trim_fields) {
				$fields = $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);
				return array_map(
					function ($field) {
						return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
					},
					$fields
				);
			},
			$lines
		);
	}

	public function view($id, Request $request)
	{
		$dscan = DB::selectOne("SELECT d.dscan_id, d.dscan_title, d.dscan_date,dscan_added_by,ss.name as system_name
										FROM dscan d
										LEFT JOIN solarsystems ss ON (ss.id=d.system_id)
										WHERE d.dscan_id=:dscan_id AND d.group_id=:group_id",[
											'group_id' => SiggySession::getGroup()->id,
											'dscan_id' => $id
										]);

		if( $dscan == null )
		{
			return redirect('/');
		}

		$recs = DB::select("SELECT r.record_name, i.typeName,g.groupID, g.groupName, r.item_distance
										FROM dscan_records r
										LEFT JOIN eve_inv_types i ON(i.typeID = r.type_id)
										LEFT JOIN eve_inv_groups g ON(g.groupID = i.groupID)
										WHERE r.dscan_id=:dscan_id
										ORDER BY g.groupName ASC,i.typeName ASC",[
											'dscan_id' => $id
										]);

		$dscan_data = array();
		$ongrid_data = array();
		foreach($recs as $record)
		{
			$dscan_data[ $record->groupID ]['group_name'] = $record->groupName;
			$dscan_data[ $record->groupID ]['records'][] = array('record_name' => $record->record_name,
																	'type_name' => $record->typeName);

			$matches = array();

			if(preg_match("/^([-+]?[0-9]*\.?[0-9]+) (m|km)$/", $record->item_distance, $matches))
			{
				if($matches[2] == 'm' || ($matches[2] == 'km' && $matches[1] <= 600))
				{
					$ongrid_data[ $record->groupID ]['group_name'] = $record->groupName;
					$ongrid_data[ $record->groupID ]['records'][] = array('record_name' => $record->record_name,
																		'type_name' => $record->typeName,
																		'distance' => $record->item_distance);
				}
			}
		}

		foreach($dscan_data as &$group)
		{
			$group['record_count'] = count($group['records']);
		}


		foreach($ongrid_data as &$group)
		{
			$group['record_count'] = count($group['records']);
		}
		
		return view('dscan.view', [
												'dscan' => $dscan,
												'all' => $dscan_data,
												'ongrid' => $ongrid_data
											]);
	}

	public function remove()
	{
		$id = $_POST['dscan_id'];
		$dscan = DB::selectOne("SELECT dscan_id, dscan_title, dscan_date
										FROM dscan
										WHERE dscan_id=:dscan_id AND group_id=:group_id",[
											'group_id' => SiggySession::getGroup()->id,
											'dscan_id' => $id
										]);

		if( $dscan == null )
		{
			return response()->json(['error' => 1, 'error_message' => 'Invalid dscan']);
		}

		DB::table('dscan')->where('dscan_id', '=', $id)->delete();
		DB::table('dscan_records')->where('dscan_id', '=', $id)->delete();
		
		return response()->json([true]);
	}
}
