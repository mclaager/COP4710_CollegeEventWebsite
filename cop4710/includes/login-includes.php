<?php

if (isset($_POST["submit"])) {
	
	$uname = $_POST["uname"];
	$pwd = $_POST["pwd"];
	
	require_once "database-includes.php";
	require_once "functions-includes.php";
	
	if (isEmptyLogin($uname, $pwd) !== false) {
		header("location: ../login.php?error=empty_input");
		exit();
	}
	
	loginUser($conn, $uname, $pwd);
}
else {
	header("location: ../login.php");
	exit();
}