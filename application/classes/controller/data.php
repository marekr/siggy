<?php 

require_once APPPATH.'classes/FrontController.php';

class Controller_Data extends FrontController
{
	public function action_sig_types()
	{
		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			header('content-type: application/json');
			ob_start( 'ob_gzhandler' );
		}
		
		$output = array();
		
        $wormholeTypes = DB::query(Database::SELECT, "SELECT * FROM statics")
								->execute()
								->as_array();
		
		$types = array();
		foreach($wormholeTypes as &$row)
		{
			$row['dest_class'] = (int)$row['dest_class'];
			$row['id'] = (int)$row['id'];
			$row['mass'] = (float)$row['mass'];
			$row['jump_mass'] = (int)$row['jump_mass'];
			$row['regen'] = (int)$row['regen'];
			$row['sig_size'] = (float)$row['sig_size'];
			$row['lifetime'] = (float)$row['lifetime'];
			
			$type[ $row['id'] ] = $row;
		}
		
		$output['wormhole_types'] = $type;		
								
        $whStaticMap = DB::query(Database::SELECT, "SELECT * FROM wormhole_class_map
                                                ORDER BY position ASC")
								->execute()
								->as_array();
		
		$outWormholes = array();
		foreach($whStaticMap as $entry)
		{
			$outWormholes[ $entry['system_class'] ][] = array('static_id' => (int)$entry['static_id'], 'position' => (int)$entry['position']);
		}

		$output['wormholes'] = $outWormholes;
		
		
		$siteTypes = DB::query(Database::SELECT, "SELECT * FROM sites")
								->execute()
								->as_array();
		
		foreach($siteTypes as $site)
		{
			$output['sites'][$site['id']] = array('id' => (int)$site['id'], 'name' => $site['name'], 'type' => $site['type']);
		}
		
		
        $extra = DB::query(Database::SELECT, "SELECT s.id, scm.system_class, s.name, s.type FROM site_class_map scm
													LEFT JOIN sites s ON(s.id=scm.site_id)")
								->execute()
								->as_array();
								
		foreach($extra as $site)
		{
			$output['maps'][ $site['type'] ][ $site['system_class'] ][] = $site['id'];
		}
		
		print (json_encode($output));
		die();
	}
}