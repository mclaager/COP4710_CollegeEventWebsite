<?php

	include_once "header.php";

?>

<h2> Make An RSO </h2>

<div class="card card-1">
	<div class="card-body">
		<?php
			if(isset($_GET["error"])) {
				if ($_GET["error"] == "empty_input") {
					echo "<p style='color:#500'>You must fill in all fields.<br><br></p>";
				}
				if ($_GET["error"] == "bad_stmt") {
					echo "<p style='color:#500'>Database error. Please try again.<br><br></p>";
				}
				if ($_GET["error"] == "none") {
					echo "<p style='color:#500'>RSO successfully made. Admin privleges granted.<br>";
					echo "Once <b>4 more users</b> join the RSO, you will be able to create events for it.<br><br></p>";
				}
			}
		?>
		<form action="includes/makerso-includes.php" method="post">
			<div class="input-group">
				<input class="input--style-1" type="text" name="rsoname" placeholder="RSO NAME" required>
			</div>
			<div class="input-group">
				<input class="input--style-1" type="text" name="rsodesc" placeholder="RSO DESCRIPTION" required>
			</div>
			<div class="p-t-20">
				<button class="btn btn--radius btn--green" type="submit" name="submitmakerso">Submit</button>
			</div>
		</form>
	</div>
</div>

<?php

	include_once "footer.php";

?>