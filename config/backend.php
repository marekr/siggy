<?php

return [
	'payment_corp_id' => env('BACKEND_PAYMENT_CORP_ID'),
	'payment_division' => env('BACKEND_PAYMENT_DIVISION',1),
	'esi' => [
		'client_id' => env('BACKEND_EVE_ESI_KEY'),
		'secret_key' => env('BACKEND_EVE_ESI_SECRET')
	]
];
