<?php

class SystemPathFinder extends AStar
{
	public $jumps = array();
	
	public function __construct()
	{
		$this->jumps = array();
			
		$cache = Cache::instance( CACHE_METHOD );
		$cacheName = 'systemPathFinderJumps';
		
		if( !($this->jumps = $cache->get( $cacheName, FALSE ) ) )
		{
			$data = DB::select()->from('mapsolarsystemjumps')->order_by('fromSolarSystemID', 'ASC')->execute()->as_array();
			
			$this->jumps = array();
			foreach($data as $j)
			{
				$this->jumps[ $j['fromSolarSystemID'] ][ $j['toSolarSystemID'] ] = 1;
			}
			
			$cache->set($cacheName, $this->jumps);		 
		}
	}
	
	function neighbors($i)
	{
		if( isset( $this->jumps[ $i ] ) )
		{
			return $this->jumps[ $i ];
		}
		return array();
	}

	function heuristic($i, $j)
	{
		return 1;
	}
}