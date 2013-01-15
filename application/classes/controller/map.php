<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Map extends Controller {

	public function action_index()
	{
		$this->request->response = 'hello, world! Maps';
/*
  $stats = DB::select(DB::expr('min(x) as minX,max(x) as maxX, max(y) as maxY, min(y) as minY, max(z) as maxZ, min(z) as minZ'))
		->from('mapsolarsystems')
	/*	->where('regionID','',DB::expr('NOT IN(10000004, 10000017,10000019,11000001,11000002,11000003,11000004,11000005,11000006,11000007,11000008,
		11000009,11000010,11000011,11000012,11000013,11000014,11000015,11000016,11000017,11000018,11000019,11000020,11000021,11000022,11000023,11000024,11000025
		,11000026,11000027,11000028,11000029,11000030)'))
		->where('regionID','',DB::expr('IN(10000001)'))
		->execute()->as_array();		
		$stats = $stats[0]; */
		


    //	width
    $mapWidth = 928*4;
	
    //	height
    $mapHeight = 1024*4;
	
    //	vertical offset
    $mapWidthOffset = 208;
	
    //	horizontal offset
    $mapHeightOffset = 0;
			
		$scale = 4.8445284569785E17/(($mapHeight - 20) / 2.0);		
		

	$result = DB::select('*')
		->from('mapsolarsystemjumps')
		->where('fromRegionID','',DB::expr('NOT IN(10000004, 10000017,10000019,11000001,11000002,11000003,11000004,11000005,11000006,11000007,11000008,
		11000009,11000010,11000011,11000012,11000013,11000014,11000015,11000016,11000017,11000018,11000019,11000020,11000021,11000022,11000023,11000024,11000025
		,11000026,11000027,11000028,11000029,11000030)'))
		->execute()->as_array();		
		
    $RAWJUMPS = array();
    foreach($result as $row)
    {
      $regional = false;
      if( $row['fromRegionID'] != $row['toRegionID'] )
      {
        $regional = true;
      }
      
      $RAWJUMPS[] = array($row['fromSolarSystemID'],$row['toSolarSystemID'], $regional);
    }				
    
    //lets prune duplicates because ccp
    /*foreach( $RAWJUMPS as $id => $jump )
    {
        foreach( $RAWJUMPS as $cjumps )
        {
          foreach( $cjumps as $sjump )
          {
            if( $sjump[ 0 ] ==  $id )
            {
              $sjump  = NULL;
              unset( $sjump );
            }
          }
        }
     }*/
     //print count($RAWJUMPS);
     /*
     foreach( $RAWJUMPS as $k=> $jump )
     {
       foreach( $RAWJUMPS as $k2 => $sjump )
       {
          if(  $sjump[1] == $jump[0]  )
          {
            unset( $RAWJUMPS[ $k2 ] );
          }
       }
     }*/
    
		
  $result = DB::select('*')
		->from('mapsolarsystems')
		->where('regionID','',DB::expr('NOT IN(10000004, 10000017,10000019,11000001,11000002,11000003,11000004,11000005,11000006,11000007,11000008,
		11000009,11000010,11000011,11000012,11000013,11000014,11000015,11000016,11000017,11000018,11000019,11000020,11000021,11000022,11000023,11000024,11000025
		,11000026,11000027,11000028,11000029,11000030)'))
		->execute()->as_array();
		
		$systems = array();
		foreach($result as $row)
		{
		  $x = (int) floor((($row['x'] / $scale) + $mapWidth / 2 + $mapWidthOffset)+0.5);
		  $y = (int) floor((($row['z'] / $scale) + $mapHeight / 2 + $mapHeightOffset)+0.5);
		  $systems[ $row['solarSystemID'] ] = array('id' => $row['solarSystemID'],'name' => $row['solarSystemName'], 'x' => $x, 'y' => $y, 'sec' => $row['security']);
		}
		

    
		$jumps = array();
		foreach( $RAWJUMPS as $j )
		{
		  $jumps[] = array('fromX' => $systems[ $j[0] ]['x'] , 'fromY' => $systems[ $j[0] ]['y'], 'toX' => $systems[ $j[1] ]['x'], 'toY' => $systems[ $j[1] ]['y'],'regional' => $j[2]);
		}		
		
		
		
		$view      = View::factory('map')
                 ->bind('systems', $systems)
                 ->bind('jumps', $jumps);
                 
		$this->response->body($view->render());
	}
	public function action_view($name)
	{
	
	}
} 
