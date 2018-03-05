<?php

return [
	'payment_corp_id' => env('BACKEND_PAYMENT_CORP_ID'),
	'payment_division' => env('BACKEND_PAYMENT_DIVISION',1),
	'failure_email' => env('BACKEND_FAILURE_EMAIL'),
	'esi' => [
		'user_id' => env('BACKEND_ESI_USER_ID',0),
		'client_id' => env('BACKEND_EVE_ESI_CLIENT_ID'),
		'secret_key' => env('BACKEND_EVE_ESI_SECRET_KEY')
	]
];
