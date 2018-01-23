<?php

namespace Siggy;

class DScanParser {

	private static $pattern = '/(\d+)\t(.*)\t(.+)\t(-|[\d,\.]+ [a-z]{1,2})/i';

	/**
	* $dscanItem['typeId']
	*			['name']
	*			['typeName']
	*			['distance']
	*
	* @return array see above
	*/
	public static function parse(string $paste): ?array {
		$lines = preg_split('/\n|\r\n?/', $paste);
		
		$list = [];

		foreach($lines as $line) {
			if(empty($line)) {
				continue;
			}

			$matches = [];
			preg_match(self::$pattern, $line, $matches);
			
			//correct # of matches
			if(count($matches) == 5) {
				$listItem = [];
				list(,$listItem['typeId'], $listItem['name'], $listItem['typeName'], $listItem['distance']) = $matches;
				
				$list[] = $listItem;
			} else {
				return null;
			}
		}

		return $list;
	}
}