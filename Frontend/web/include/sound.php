<?php
include_once('config.php');

$client = $_POST['client'];

$errors = [];
$data = [];

if (empty($_POST['sound'])) {
	$errors['sound'] = 'Er ging iets mis.';
}

if (!empty($errors)) {
	$data['success'] = false;
	$data['errors'] = $errors;
} else {

	// send POST request

	$reqData = [];
	$reqData['text'] = $_POST['sound'];
	$reqData['type'] = 'soundboard';
	$reqData['client'] = $client;

	$options = array(
		'http' => array(
			'method'  => 'POST',
			'content' => json_encode($reqData),
			'header' =>  "Content-Type: application/json\r\n" .
				"Accept: application/json\r\n" .
				"Api-key: " . API_KEY . "\r\n"
		)
	);

	$context  = stream_context_create($options);
	$result = file_get_contents(API_URL, false, $context);
	$response = json_decode($result);

	$data['success'] = true;
	$data['result'] = 'Het geluid is met succes verzonden!';
}


echo json_encode($data);
