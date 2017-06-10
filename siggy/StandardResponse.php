<?php

namespace Siggy;

class StandardResponse
{
	public const StatusOk = 'ok';
	public const StatusError = 'error';
	public const StatusErrors = 'errors';

	public static function error(string $msg): array
	{
		$resp = [
			'status' => self::StatusError,
			'error' => $msg
		];

		return $resp;
	}

	public static function errors(array $errs): array
	{
		$resp = [
			'status' => self::StatusErrors,
			'errors' => $errs
		];

		return $resp;
	}

	public static function ok($data = null): array
	{
		$resp = [
			'status' => self::StatusOk
		];

		if($data != null)
		{
			$resp['result'] = $data;
		}
		
		return $resp;
	}
}