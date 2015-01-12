<?php

final class MapUtils
{

	static function generatePossibleSystemLocations($x, $y)
	{
		$originBB = self::coordsToBB($x,$y);

		$cX = $originBB['left'];
		$cY = $originBB['top'];

		$ret = array();

		for($level = 1; $level <= 3; $level++)
		{
			$positions = 8*$level;
			$rotation = 2 * M_PI / $positions;

			for($position = 0; $position < $positions; ++$position)
			{
				$spot_rotation = $position * $rotation;
				$newx = $cX + 125*$level*cos($spot_rotation);
				$newy = $cY + 85*$level*sin($spot_rotation);

				//limited horizontal span
				if( $newy < 780 && $newy > 0 && $newx > 0 )
				{
					$ret[] = array('x' => $newx, 'y' => $newy);
				}
			}
		}

		return $ret;
	}

	static function doBoxesIntersect($a, $b)
	{
		$x1 = $a['left'];
		$x2 = $a['left'] + $a['width'];
		$y1 = $a['bottom'];
		$y2 = $a['bottom'] + $a['height'];

		$a1 = $b['left'];

		$a2 = $b['left'] + $b['width'];
		$b1 =  $b['bottom'];
		$b2 =  $b['bottom'] +  $b['height'];

		return  ( ($x1 <= $a1 && $a1 <= $x2) && ($y1 <= $b1 && $b1 <= $y2) ) ||
				( ($x1 <= $a2 && $a2 <= $x2) && ($y1 <= $b1 && $b1 <= $y2) ) ||
				( ($x1 <= $a1 && $a1 <= $x2) && ($y1 <= $b2 && $b2 <= $y2) ) ||
				( ($x1 <= $a2 && $a1 <= $x2) && ($y1 <= $b2 && $b2 <= $y2) ) ||
				( ($a1 <= $x1 && $x1 <= $a2) && ($b1 <= $y1 && $y1 <= $b2) ) ||
				( ($a1 <= $x2 && $x2 <= $a2) && ($b1 <= $y1 && $y1 <= $b2) ) ||
				( ($a1 <= $x1 && $x1 <= $a2) && ($b1 <= $y2 && $y2 <= $b2) ) ||
				( ($a1 <= $x2 && $x1 <= $a2) && ($b1 <= $y2 && $y2 <= $b2) );
	}

	static function coordsToBB($x,$y)
	{
		return array( 'left' => $x,
					  'top' => $y,
					  'width' => 78,
					  'height' => 38,
					  'right' => $x+78,
					  'bottom' => $y+38 );
	}

	static function whHashByID($to, $from)
	{
		if( $to < $from )
		{
			return md5( intval($to) . intval($from) );
		}
		else
		{
			return md5( intval($from) . intval($to) );
		}
	}

	static function findSystemByEVEName($name)
	{
		$systemID = 0;

		$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
															->param(':name', $name )
															->execute()
															->get('id', 0);

		return $systemID;
	}

	static function findSystemByName($name, $group_id, $chain_map_id = 0)
	{
		$systemID = 0;
		if( empty($name) )
		{
			return 0;
		}

		$name = strtolower($name);
		$systemID = DB::query(Database::SELECT, "SELECT systemID,displayName FROM activesystems WHERE groupID=:groupID AND chainmap_id=:chainmap AND displayName LIKE :name")
													->param(':name', $name )
													->param(':groupID', $group_id)
													->param(':chainmap', $chain_map_id)
													->execute()
													->get('systemID', 0);

		if( $systemID == 0 )
		{
			$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
																->param(':name', $name )
																->execute()
																->get('id', 0);

		}

		return $systemID;
	}
}
