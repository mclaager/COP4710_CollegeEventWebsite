<?php
	session_start();
	include_once "includes/database-includes.php";
	include_once "includes/functions-includes.php";
?>
<link rel="stylesheet" href="css/headerstyle.css">

<head>
	<h1> College Event Website </h1>

	<link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
	<link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
	<!-- Font special for pages-->
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">

	<!-- Vendor CSS-->
	<link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
	<link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

	<!-- Main CSS-->
	<link href="css/main.css" rel="stylesheet" media="all">

</head>

<div class="topnav">
	<ul>
		<a class = "active" href = "index.php">Home</a>
		<?php
			// Header for logged in person
			if (isset($_SESSION["usersId"])) {
				$usersId = $_SESSION["usersId"];
				// Options for all users
				echo "<a href = 'my_events.php'>My Events</a>";
				echo "<a href = 'my_rsos.php'>My RSOs</a>";
				echo "<a href = 'events.php'>Events</a>";
				echo "<a href = 'rsos.php'>RSOs</a>";
				
				// Admin
				if (isAdmin($conn, $usersId) !== FALSE) {
					echo "<a href = 'createevent.php'>Create An Event</a>";
				}
				// Super Admin
				if (isSuperAdmin($conn, $usersId) !== FALSE) {
					echo "<a href = 'pendingevents.php'>Pending Events</a>";
				}
				// Normal User
				if (isAdmin($conn, $usersId) === FALSE && isSuperAdmin($conn, $usersId) === FALSE) {
					;
				}
				
				// More options for all users
				echo "<a href = 'requestevent.php'>Request An Event</a>";
				echo "<a href = 'makerso.php'>Make An RSO</a>";
				echo "<a href = 'includes/logout-includes.php'>Log Out</a>";
			}
			// Header for visiters
			else {
				echo "<a href = 'signup.php'>Sign Up</a>";
				echo "<a href = 'login.php'>Log In</a>";
			}
		?>
	</ul>
</div>