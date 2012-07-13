<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Systems extends Controller {

	public function action_index()
	{
		$this->request->response = 'hello, world!';
	}
	public function action_view($name)
	{
	
		$result = DB::select('a.solarSystemName')
			->from(array('mapsolarsystems','a'), array('mapsolarsystems', 'b'), array('mapsolarsystemjumps','j') )
		->where('a.solarsystemID','=',DB::expr('j.fromSolarSystemID'))
		->and_where('b.solarsystemID','=',DB::expr('j.toSolarSystemID'))
		->and_where('b.solarSystemName','=',$name)
		->execute()->as_array();
		
		


		
foreach ($result as $row)
{
   print_r($row);
}

/*
// I is the infinite distance.
define('I',1000);
 
// Size of the matrix
$matrixWidth = 20;
 
// $points is an array in the following format: (router1,router2,distance-between-them)
$points = array(
	array(0,1,4),
	array(0,2,I),
	array(1,2,5),
 	array(1,3,5),
	array(2,3,5),
	array(3,4,5),
	array(4,5,5),
	array(4,5,5),
	array(2,10,30),
	array(2,11,40),
	array(5,19,20),
	array(10,11,20),
	array(12,13,20),
);
 
$ourMap = array();
 
 
// Read in the points and push them into the map
 
foreach ($points as $point) {
	$x = $point[0];
	$y = $point[1];
	$c = $point[2];
	$ourMap[$x][$y] = $c;
	$ourMap[$y][$x] = $c;
}
 
// ensure that the distance from a node to itself is always zero
// Purists may want to edit this bit out.
 
for ($i=0; $i < $matrixWidth; $i++) {
    $ourMap[$i][$i] = 0;
}
 
 
// initialize the algorithm class
$dijkstra = new Dijkstra($ourMap, I,$matrixWidth);
 
// $dijkstra->findShortestPath(0,13); to find only path from field 0 to field 13...
$dijkstra->findShortestPath(0); 
 
// Display the results
 
echo '<pre>';
echo "the map looks like:\n\n";
echo $dijkstra -> printMap($ourMap);
echo "\n\nthe shortest paths from point 0:\n";
echo $dijkstra -> getResults();
echo '</pre>';
 

*/

		$this->request->response = 'hello, world 2222!'.$name;
	}

} // End Welcome
