<?php 

require_once APPPATH.'classes/FrontController.php';
require_once APPPATH.'classes/access.php';

class Controller_Dscan extends FrontController
{
	/*
		Key value array
	*/
	public $template = 'template/public';
	
	protected $dscan_item_cache = array();
	protected $output_array = array();
	
	public function action_add()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1\
			

		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			
		
		$blob = $_REQUEST['blob'];
		
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
				$itemData = DB::query(Database::SELECT, 'SELECT typeID FROM invtypes 
										WHERE typeName LIKE :name')
									->param(':name', $entry[1])
									->execute()->current();		
									
				if( isset($itemData['typeID']) )
				{
					$typeID = $this->dscan_item_cache[ $entry[1] ] = $itemData['typeID'];
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
				'system_id' => intval($_POST['system_id']),
				'group_id' => Auth::$session->groupID,
				'dscan_title' => htmlentities($_POST['dscan_title']),
				'dscan_id' => $id,
				'dscan_added_by' => Auth::$session->charName
			);
			
			$dscanID = DB::insert('dscan', array_keys($data) )
						->values(array_values($data))->execute();
			//print_r($this->output_array);
			foreach( $this->output_array as $rec )
			{
				$insert = array('dscan_id' => $id,
								 'type_id' => $rec['type_id'],
								'record_name' => htmlentities($rec['name']),
								'item_distance' => $rec['item_distance'] );
				$posID = DB::insert('dscan_records', array_keys($insert) )
						->values(array_values($insert))->execute();
			}
		}
		
		
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
	
	public function action_view()
	{
		$this->template->title = "siggy: dscan";
		$this->template->selectedTab = 'home';
		$this->template->loggedIn = Auth::loggedIn();
		$this->template->user = Auth::$user->data;
		$this->template->layoutMode = 'blank';
		
		$view = View::factory('dscan/view');
		
		$id = $this->request->param('id');
		
		$dscan = DB::query(Database::SELECT, "SELECT d.dscan_id, d.dscan_title, d.dscan_date,dscan_added_by,ss.name as system_name
										FROM dscan d
										LEFT JOIN solarsystems ss ON (ss.id=d.system_id)
										WHERE d.dscan_id=:dscan_id AND d.group_id=:group_id")
								->param(':group_id', Auth::$session->groupID)
								->param(':dscan_id', $id)
								->execute()->current();
								
		if( !isset($dscan['dscan_id']) )
		{
			HTTP::redirect('/');
		}
		$this->template->title = "siggy: dscan " . $dscan['dscan_title'];
		
		$recs = DB::query(Database::SELECT, "SELECT r.record_name, i.typeName,g.groupID, g.groupName, r.item_distance
										FROM dscan_records r
										LEFT JOIN invtypes i ON(i.typeID = r.type_id)
										LEFT JOIN invgroups g ON(g.groupID = i.groupID)
										WHERE r.dscan_id=:dscan_id
										ORDER BY g.groupName ASC,i.typeName ASC")
								->param(':group_id', Auth::$session->groupID)
								->param(':dscan_id', $id)
								->execute()->as_array();
								
		$dscan_data = array();
		$ongrid_data = array();
		foreach($recs as $record)
		{
			$dscan_data[ $record['groupID'] ]['group_name'] = $record['groupName'];
			$dscan_data[ $record['groupID'] ]['records'][] = array('record_name' => $record['record_name'], 
																	'type_name' => $record['typeName']);
																	
			$matches = array();
			
			if(preg_match("/^([-+]?[0-9]*\.?[0-9]+) (m|km)$/", $record['item_distance'], $matches))
			{
				if($matches[2] == 'm' || ($matches[2] == 'km' && $matches[1] <= 600))
				{
					$ongrid_data[ $record['groupID'] ]['group_name'] = $record['groupName'];
					$ongrid_data[ $record['groupID'] ]['records'][] = array('record_name' => $record['record_name'], 
																		'type_name' => $record['typeName'],
																		'distance' => $record['item_distance']);
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
		
		$view->dscan = $dscan;
		$view->all = $dscan_data;
		$view->ongrid = $ongrid_data;
		$this->template->content = $view;	
	}
	
	public function action_remove()
	{
		
		$id = $_POST['dscan_id'];
		$dscan = DB::query(Database::SELECT, "SELECT dscan_id, dscan_title, dscan_date
										FROM dscan
										WHERE dscan_id=:dscan_id AND group_id=:group_id")
								->param(':group_id', Auth::$session->groupID)
								->param(':dscan_id', $id)
								->execute()->current();
								
		if( !isset($dscan['dscan_id']) )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid dscan'));
			exit();
		}
		
		
		DB::delete('dscan')->where('dscan_id', '=', $id)->execute();
		DB::delete('dscan_records')->where('dscan_id', '=', $id)->execute();
		
		
	}
}