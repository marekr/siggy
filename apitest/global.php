<?php

if( $_SERVER['SERVER_NAME'] != 'localhost' )
{
	exit("no access");
}

function request( $verb, $url )
{
	global $apiID, $apiSecret;
	$params     = array(
		'host'          => 'localhost',
		'content-type'  => 'application/json',
		'user-agent'    => 'apitest',
		'connection'    => 'keep-alive',
	);
	
	$timestamp = time();
	$stringToSign = $verb . "\n".
					$timestamp;
	
	$hash = base64_encode(hash_hmac('sha256', $stringToSign, $apiSecret, true));
	$authorization = $apiID.":".$timestamp.":".$hash;
	
	$params['Authorization'] = 'siggy-HMAC-SHA256 Credential='.$authorization;

	$ch     = curl_init();

	$curl_headers = array();
	foreach( $params as $p => $k )
	{
		$curl_headers[] = $p . ": " . $k;
	}

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TCP_NODELAY, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
	
	// debug opts
	{
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$verbose = fopen('php://temp', 'rw+');
		curl_setopt($ch, CURLOPT_STDERR, $verbose);
		$result = curl_exec($ch); // raw result
		rewind($verbose);
		$verboseLog = stream_get_contents($verbose);
		echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
	}

	var_dump( $result );
}

function url()
{
  return sprintf(
		"%s://%s%s",
		isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
		$_SERVER['SERVER_NAME'],
		str_replace("apitest","",dirname($_SERVER['PHP_SELF']))
  );
}