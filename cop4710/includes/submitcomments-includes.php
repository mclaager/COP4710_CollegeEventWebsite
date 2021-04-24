<?php

//header('Content-Type: application/json');

include_once "database-includes.php";
include_once "functions-includes.php";

echo submitCommentHelper($conn);//$_POST["method"]($conn);

function submitdCommentHelper($conn) {
	echo json_encode("Tddest");
}

function submitCommentHelper($conn) {
	if (isset($_POST["eventid"])) {
		$eventid = $_POST["eventid"];
	}
	if (isset($_POST["comment_field"])) {
		$desc = $_POST["comment_field"];
	}
	
	//echo json_encode("Tddest");
	
	session_start();
	
	$userid = $_SESSION["usersId"];

	$data = '';
	// takes the user out of the rso
	if (makeComment($conn, $eventid, $userid, $desc) !== FALSE) {
		$data = "Comment added";
	}
	else {
		$data = "Unable to add comment.";
	}
	
	echo json_encode($data);
}