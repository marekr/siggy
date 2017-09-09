<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use \miscUtils;
use App\Facades\Auth;
use App\Facades\SiggySession;
use Siggy\DScan;
use Siggy\DScanRecord;
use Siggy\StandardResponse;


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
			if( !isset( $entry[2] ) )
			{
				continue;
			}

			/* check the cache first, else query */
			if( !isset($this->dscan_item_cache[ $entry[2] ] ) )
			{
				$itemData = DB::selectOne('SELECT typeID FROM eve_inv_types
										WHERE typeName LIKE ?', [$entry[2]]);

				if( isset($itemData->typeID) )
				{
					$typeID = $this->dscan_item_cache[ $entry[2] ] = $itemData->typeID;
				}
			}
			else
			{
				$typeID = $this->dscan_item_cache[ $entry[2] ];
			}

			if( $typeID != 0  )
			{
				$this->output_array[] = array( 'type_id' => $typeID, 'name' => $entry[1], 'item_distance' => $entry[3] );
			}

		}

		$dscan = null;
		if( count( $this->output_array ) > 0 )
		{
			$data = array(
				'system_id' => intval($postData['system_id']),
				'group_id' => SiggySession::getGroup()->id,
				'title' => htmlentities($postData['title']),
				'added_by' => SiggySession::getCharacterName()
			);

			$dscan = DScan::create($data);

			$id = $dscan->id;

			foreach( $this->output_array as $rec )
			{
				$insert = [
								'dscan_id' => $id,
								 'type_id' => $rec['type_id'],
								'record_name' => htmlentities($rec['name']),
								'item_distance' => $rec['item_distance'] 
							];

				$record = DScanRecord::create($insert);
			}
		}

		if($dscan != null) {
			
			return response()->json(StandardResponse::ok($dscan));
		}

		return response()->json(StandardResponse::error('DScan parsing failed'));
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
		$dscan = DScan::findByGroup(SiggySession::getGroup()->id, $id);

		if( $dscan == null )
		{
			return response()->json(StandardResponse::error('DScan not found'), 404);
		}

		$recs = $dscan->records;
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
		
		return response()->json([
									'dscan' => $dscan,
									'all' => $dscan_data,
									'ongrid' => $ongrid_data
								]);
	}

	public function remove(Request $request)
	{
		$id = $request->input('id');
		$dscan = DScan::findByGroup(SiggySession::getGroup()->id, $id);

		if( $dscan == null )
		{
			return response()->json(StandardResponse::error('DScan not found'));
		}

		DB::table('dscan_records')->where('dscan_id', '=', $id)->delete();
		$dscan->delete();
		
		return response()->json(StandardResponse::ok());
	}
}
