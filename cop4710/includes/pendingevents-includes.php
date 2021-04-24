<?php

header('Content-Type: application/json');

include_once "database-includes.php";

echo $_POST["method"]($conn);


function verifySuperAdmin($conn, $eventid, $userid) {
	// Checks if user is able to delete this entry from database
	$sql = "SELECT * FROM eventapproval WHERE eventapprovalEid = ? AND eventapprovalSid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo json_encode(array('status' => 'err', 'statusText' => 'Database error in verifySuperAdmin.'));
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ii", $eventid, $userid);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	mysqli_stmt_close($stmt);
		
	// User is not (correct) super admin and is trying to accept an event
	if (mysqli_fetch_assoc($resultData)) {
		return TRUE;
	}
	return FALSE;
}

function acceptEvent($conn) {
	if (isset($_POST["eid"])) {
		$eventid = json_decode($_POST["eid"]);
	}
	
	session_start();
	
	$userid = $_SESSION["usersId"];

	// User is not (correct) super admin and is trying to accept an event
	if (verifySuperAdmin($conn, $eventid, $userid) === FALSE) {
		echo json_encode(array('status' => 'err', 'statusText' => 'You do not have the access to accept this event.'));
		exit();
	}
	
	// Removes approval entries from database
	$sql = "DELETE FROM eventapproval WHERE eventapprovalEid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo json_encode(array('status' => 'err', 'statusText' => 'Database error in verifySuperAdmin.'));
		exit();
	}
	mysqli_stmt_bind_param($stmt, "i", $eventid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	echo json_encode(array('status' => 'ok'));
}

function denyEvent($conn) {
	if (isset($_POST["eid"])) {
		$eventid = json_decode($_POST["eid"]);
	}
	
	session_start();
	
	$userid = $_SESSION["usersId"];
	
	// User is not (correct) super admin and is trying to deny an event
	if (verifySuperAdmin($conn, $eventid, $userid) === FALSE) {
		echo json_encode(array('status' => 'err', 'statusText' => 'You do not have the access to deny this event.'));
		exit();
	}
	
	// Removes event from database
	$sql = "DELETE FROM events WHERE eventsId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo json_encode(array('status' => 'err', 'statusText' => 'Database error in denyEvent.'));
		exit();
	}
	mysqli_stmt_bind_param($stmt, "i", $eventid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	echo json_encode(array('status' => 'ok'));
}