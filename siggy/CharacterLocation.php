<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class CharacterLocation
{
	public static function findWithinCutoff(int $id, int $cutOffSeconds = 15)
	{
		$string = Redis::get('siggy:location:character#'.$id);
		if($string != null)
		{
			$data = json_decode($string);
			$data->updated_at = Carbon::parse($data->updated_at);

			if($data->updated_at >= Carbon::now()->subSeconds($cutOffSeconds))
			{
				return $data;
			}
		}

		return null;
	}
}