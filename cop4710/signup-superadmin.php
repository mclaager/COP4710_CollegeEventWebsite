<?php

	include_once "header.php";

?>


<?php

// Checks if the user 
if (isset($_POST["submit"])) {
	
	$uname = $_POST["uname"];
	$pwd = $_POST["pwd"];
	$pwdRepeat = $_POST["pwdrepeat"];
	$uniname = $_POST["uniname"];
	
	require_once "includes/database-includes.php";
	require_once "includes/functions-includes.php";
	
	// Makes sure all fields were filled in
	if (isEmptySignup($uname, $pwd, $pwdRepeat, $uniname) !== false) {
		header("location: signup.php?error=empty_input");
		exit();
	}
	
	if (isInvalidUsername($uname) !== false) {
		header("location: signup.php?error=invalid_username");
		exit();
	}
	
	if (isPasswordMismatch($pwd, $pwdRepeat) !== false) {
		header("location: signup.php?error=pwd_mismatch");
		exit();
	}
	
	if (userExists($conn, $uname) !== false) {
		header("location: signup.php?error=nonunique_username");
		exit();
	}
	
	if (universityExists($conn, $uniname) !== false) {
		createUser($conn, $uname, $pwd, $uniname, TRUE);
		exit();
	}
	
}
else {
	header("location: signup.php");
	exit();
}

// Saves the info in SESSION to allow for later user creation
foreach ($_POST as $key => $value) {
	$_SESSION['post'][$key] = $value;
}
?>

<h2> New University Information </h2>
<h3> Complete sign up for new university to become super-admin for the university. </h3>

<!-- This only happens once there is a new university to be created -->
<div class="card card-1">
	<div class="card-body">
		<form action="includes/signup-superadmin-includes.php" method="post">
			<h4><b>UNIVERSITY LOCATION</b></h4>
			<div class="row row-space">
				<div class="col-2">
					<div class="input-group">
						<input class="input--style-1" type="text" name="lat" placeholder="LATITUDE">
					</div>
				</div>
				<div class="col-2">
					<div class="input-group">
						<input class="input--style-1" type="text" name="long" placeholder="LONGITUDE">
					</div>
				</div>
			</div>
			<h4><b>UNIVERSITY INFO</b></h4>
			<div class="input-group">
				<input class="input--style-1" type="text" name="desc" placeholder="UNIVERSITY DESCRIPTION">
			</div>
			<div class="p-t-20">
				<button class="btn btn--radius btn--green" type="submit" name="submitsa">Sign Up</button>
			</div>
		</form>
	</div>
</div>

<?php

	include_once "footer.php";

?>