<?php

header('Content-Type: application/json');

include_once "database-includes.php";
include_once "functions-includes.php";

echo $_POST["method"]($conn);

function joinRSOHelper($conn) {
	if (isset($_POST["rid"])) {
		$rsoid = json_decode($_POST["rid"]);
	}
	
	session_start();
	
	$userid = $_SESSION["usersId"];

	// Joins the user into the RSO
	joinRSO($conn, $rsoid, $userid);
	
	echo json_encode(array('status' => 'ok'));
}