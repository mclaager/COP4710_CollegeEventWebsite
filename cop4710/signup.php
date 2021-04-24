<?php

	include_once "header.php"

?>

<h2> Sign Up </h2>

<div class="card card-1">
	<div class="card-body">
		<?php
			// Error handling
			if(isset($_GET["error"])) {
				if ($_GET["error"] == "empty_input") {
					echo "<p style='color:#500'>You must fill in all fields.<br><br></p>";
				}
				if ($_GET["error"] == "invalid_username") {
					echo "<p style='color:#500'>Usernames must be alphanumeric.<br><br></p>";
				}
				if ($_GET["error"] == "pwd_mismatch") {
					echo "<p style='color:#500'>Passwords do not match.<br><br></p>";
				}
				if ($_GET["error"] == "nonunique_username") {
					echo "<p style='color:#500'>Username already exists.<br><br></p>";
				}
				if ($_GET["error"] == "bad_stmt") {
					echo "<p style='color:#500'>Database error. Please try again.<br><br></p>";
				}
				
				if ($_GET["error"] == "none") {
					echo "<p style='color:#500'>Sign up successful.<br><br></p>";
				}
			}
		?>
		<form action="signup-superadmin.php" method="post">
			<div class="input-group">
				<input class="input--style-1" type="text" name="uname" placeholder="USERNAME">
			</div>
			<div class="input-group">
				<input class="input--style-1" type="password" name="pwd" placeholder="PASSWORD">
			</div>
			<div class="input-group">
				<input class="input--style-1" type="password" name="pwdrepeat" placeholder="REPEAT PASSWORD">
			</div>
			<div class="input-group">
				<input class="input--style-1" type="text" name="uniname" placeholder="UNIVERSITY NAME">
			</div>
			<div class="p-t-20">
				<button class="btn btn--radius btn--green" type="submit" name="submit">Sign Up</button>
			</div>
		</form>
	</div>
</div>