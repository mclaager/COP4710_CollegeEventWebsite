<?php

header('Content-Type: application/json');

include_once "database-includes.php";
include_once "functions-includes.php";

echo listCommentsHelper($conn);//$_POST["method"]($conn);

function listCommentsHelper($conn) {
	if (isset($_POST["eventid"])) {
		$eventid = json_decode($_POST["eventid"]);
	}
	session_start();
	
	$userid = $_SESSION["usersId"];

	// gets the comments for the users event
	$comments = getAvailableComments($conn, $eventid, $userid);
	
	$output = '';
	
	if ($comments !== FALSE) {
		while($row = mysqli_fetch_assoc($comments)) {
			$commenterid = $row['commentsUid'];
			$commentername = getUsersName($conn, $commenterid);
			$output .= '
			<div class="panel panel-default" style="margin-left:0px">
				<div class="panel-heading">By <b>'.$commentername.'</b> on <i>'.$row["commentsTime"].'</i></div>
				<div class="panel-body">'.$row["commentsDesc"].'<br><br></div>
			</div>
			';
		}
	}
	
	echo $output;
}