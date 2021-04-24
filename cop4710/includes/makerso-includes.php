<?php

if (isset($_POST["submitmakerso"])) {
	
	$rsoname = $_POST["rsoname"];
	$rsodesc = $_POST["rsodesc"];
	
	require_once "database-includes.php";
	require_once "functions-includes.php";
	
	if (isEmptyMakeRSO($rsoname, $rsodesc) !== false) {
		header("location: ../makerso.php?error=empty_input");
		exit();
	}
	
	session_start();
	
	$usersid = $_SESSION["usersId"];
	makeRSO($conn, $rsoname, $rsodesc, $usersid);
	$rsoid = rsoExists($conn, $rsoname);
	$rsoid = $rsoid['rsosId'];
	joinRSO($conn, $rsoid, $usersid);
	
	header("location: ../makerso.php?error=none");
}
else {
	header("location: ../makerso.php");
	exit();
}