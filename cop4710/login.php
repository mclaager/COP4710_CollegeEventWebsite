<?php

	include_once "header.php"

?>

<h2> Login </h2>

<div class="card card-1">
	<div class="card-body">
		<?php
			if(isset($_GET["error"])) {
				if ($_GET["error"] == "empty_input") {
					echo "<p style='color:#500'>You must fill in all fields.<br><br></p>";
				}
				if ($_GET["error"] == "incorrect_login") {
					echo "<p style='color:#500'>Username or password is incorrect.<br><br></p>";
				}
			}
		?>
		<form action="includes/login-includes.php" method="post">
			<div class="input-group">
				<input class="input--style-1" type="text" name="uname" placeholder="Username">
			</div>
			<div class="input-group">
				<input class="input--style-1" type="password" name="pwd" placeholder="Password">
			</div>
			<div class="p-t-20">
				<button class="btn btn--radius btn--green" type="submit" name="submit">Login</button>
			</div>
		</form>
	</div>
</div>

<?php

	include_once "footer.php"

?>