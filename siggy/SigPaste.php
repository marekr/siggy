<?php

namespace Siggy;

class SigPaste {
	
	public static function parseIngameSigExport( string $string ): array
	{
		$resultingSigs = [];

		$lines = explode("\n", $string);
		foreach( $lines as $line )
		{
			$rawdata = explode("\t", $line);
			if( count($rawdata) < 2 )
			{
				continue;
			}

			$sigData = ['type' => 'none', 'sig' => '', 'siteID' => 0];

			$matches = [];

			/*eliminate junk items, :CCP: sometimes
			inject mix spaces/tabs that cause the tab split to not be clean */
			$data = [];
			foreach($rawdata as $item)
			{
				$item = trim($item);
				if( empty($item) )
					continue;
				$data[] = $item;
			}

			foreach($data as $k => $item)
			{
				$item = trim($item);
				if( empty($item) )
					continue;

				preg_match("/^([a-zA-Z]{3})-([0-9]{3})$/", $item, $matches );
				if( count($matches) == 3 )	//SIG-NUM, SIG, NUM
				{
					$sigData['sig'] = $matches[1];
					continue;
				}

				$regex = "/^(".__('Wormhole')."|".__('Data Site')."|".__('Gas Site')."|".__('Relic Site')."|".__('Ore Site')."|".__('Combat Site').")$/";

				preg_match($regex, $item, $matches );
				if( count($matches) == 2 )
				{
					switch( $matches[1] )
					{
						case __('Wormhole'):
							$sigData['type'] = 'wh';
							$sigData['siteID'] = 0;
							break;
						case __('Data Site'):
							$sigData['type'] = 'data';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
						case __('Gas Site'):
							$sigData['type'] = 'gas';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
						case __('Relic Site'):
							$sigData['type'] = 'relic';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
						case __('Ore Site'):
							$sigData['type'] = 'ore';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
						case __('Combat Site'):
							$sigData['type'] = 'anomaly';
							$sigData['siteID'] = self::siteIDLookupByName( $data[$k+1], $sigData['type'] );
							break;
					}
					continue;
				}
			}

			if( $sigData['sig'] != '' )
			{
				$resultingSigs[] = $sigData;
			}
		}
		return $resultingSigs;
	}

	public static function siteIDLookupByName( string $name, string $type ): int
	{
		$sites = DB::select("SELECT * FROM sites WHERE type = ?", [$type]);

		foreach( $sites as $site )
		{
			if( __($site->name) == $name )
			{
				return $site->id;
			}
		}

		return 0;
	}
}