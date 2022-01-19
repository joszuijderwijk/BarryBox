<?php
include_once ('config.php');

	$client = $_POST['client'];
	
	$url = API_URL . "?client=" . $client;

	$options = array(
	  'http' => array(
		'method'  => 'GET',
		'header'=>  "Content-Type: application/json\r\n" .
					"Accept: application/json\r\n" .
					"Api-key: " . API_KEY . "\r\n"
		)
	);

	$context  = stream_context_create( $options );
	$result = file_get_contents( $url, false, $context );
	$response = json_decode( $result, true);

	echo json_encode($response);
?>