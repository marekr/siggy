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
use Siggy\DScanParser;


class DScanController extends Controller {

	protected $dscan_item_cache = array();
	protected $output_array = array();

	public function add(Request $request)
	{
		$postData = json_decode($request->getContent(), true);
		$blob = $postData['blob'];

		$dscanItems = DScanParser::parse( $blob );

		if( $dscanItems == null ) {
			return response()->json(StandardResponse::error('DScan parsing failed'));
		}

		$dscan = null;
		if( count( $this->output_array ) > 0 )
		{
			$data = [
				'system_id' => intval($postData['system_id']),
				'group_id' => SiggySession::getGroup()->id,
				'title' => htmlentities($postData['title']),
				'added_by' => SiggySession::getCharacterName()
			];

			$dscan = DScan::create($data);

			$id = $dscan->id;

			foreach( $this->output_array as $rec )
			{
				$insert = [
								'dscan_id' => $id,
								'type_id' => $dscanItems['typeId'],
								'record_name' => htmlentities($dscanItems['name']),
								'item_distance' => $dscanItems['distance'] 
							];

				$record = DScanRecord::create($insert);
			}
		}

		if($dscan != null) {
			
			return response()->json(StandardResponse::ok($dscan));
		}

		return response()->json(StandardResponse::error('DScan parsing failed'));
	}

	public function view(string $id, Request $request)
	{
		$dscan = DScan::findByGroup(SiggySession::getGroup()->id, $id);

		if( $dscan == null )
		{
			return response()->json(StandardResponse::error('DScan not found'), 404);
		}

		$recs = $dscan->records;
		$records = [];
		foreach($recs as $record)
		{
			$records[ $record->type->groupID ]['name'] = $record->type->group->groupName;
			$records[ $record->type->groupID ]['id'] = $record->type->groupID;

			$matches = [];

			$record->on_grid = false;
			if(preg_match("/^([0-9,]+) (m|km)$/", $record->item_distance, $matches))
			{
				$distance = (int)$matches[1];
				$unit = $matches[2];
				if($unit == 'm' || ($unit == 'km' && $distance <= 600))
				{
					$record->on_grid = true;
				}
			}

			$records[ $record->type->groupID ]['is_ship'] = false;
			//ship or capsule
			if($record->type->group->categoryID == 6 || $record->type->groupID == 29) 
			{
				$records[ $record->type->groupID ]['is_ship'] = true;
			}

			$records[ $record->type->groupID ]['is_structure'] = false;
			$structures = [15, //station
							365, //towers
							1657, //citadels
							1404, //engineering complex
							1012, //ihub
			];

			if(in_array($record->type->groupID, $structures)) 
			{
				$records[ $record->type->groupID ]['is_structure'] = true;
			}

			$record->type_name = $record->type->typeName;

			$records[ $record->type->groupID ]['records'][] = $record;
		}
		
		return response()->json([
									'title' => $dscan->title,
									'groups' => $records
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
