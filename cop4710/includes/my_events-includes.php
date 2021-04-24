<?php

header('Content-Type: application/json');

include_once "database-includes.php";
include_once "functions-includes.php";

echo $_POST["method"]($conn);

function leaveEventHelper($conn) {
	if (isset($_POST["eid"])) {
		$eventid = json_decode($_POST["eid"]);
	}
	
	session_start();
	
	$userid = $_SESSION["usersId"];

	// takes the user out of the rso
	leaveEvent($conn, $eventid, $userid);
	
	echo json_encode(array('status' => 'ok'));
}