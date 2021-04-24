<?php

header('Content-Type: application/json');

include_once "database-includes.php";
include_once "functions-includes.php";

echo $_POST["method"]($conn);

function leaveRSOHelper($conn) {
	if (isset($_POST["rid"])) {
		$rsoid = json_decode($_POST["rid"]);
	}
	
	session_start();
	
	$userid = $_SESSION["usersId"];

	// takes the user out of the rso
	leaveRSO($conn, $rsoid, $userid);
	
	echo json_encode(array('status' => 'ok'));
}