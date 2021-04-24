<?php

$serverName = "localhost";
$dbUserName = "root";
$dbPassword = "";
$dbName = "college_events";

$conn = mysqli_connect($serverName, $dbUserName, $dbPassword, $dbName);

if (!$conn) {
	die("Connection failed. " . mysqli_connect_error());
}