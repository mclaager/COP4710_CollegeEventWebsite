<?php

	include_once "header.php"

?>

<link rel="stylesheet" href="css/rsostyle.css">

<script src="vendor/jquery/jquery.min.js"></script>
<script src="js/RSOsAjax.js"></script>

<section class="events">
	<?php
		if (isset($_SESSION["usersId"])) {
			$useruni = getUsersUniversity($conn, $_SESSION["usersId"]);
			$uniname = getUniversityName($conn, $useruni);
			echo "<h2>All RSOs for " . $uniname . "</h2>";
		}
		else {
				header("location: index.php");
		}
	?>
	<ul class="tilesWrap">
		<?php
			if (isset($_SESSION["usersId"])) {
				$userid = $_SESSION["usersId"];
				$useruni = getUsersUniversity($conn, $userid);
				$rsodata = getUniversityRSOs($conn, $useruni);
				while($row = mysqli_fetch_assoc($rsodata)) {
					echo "<li>";
					$rsoid = $row["rsouniversityRid"];
					$rsoinfo = getRSOInfo($conn, $rsoid);
					$rsoname = $rsoinfo["rsosName"];
					$rsodesc = $rsoinfo["rsosDesc"];
					$rsouniid = getRSOUniversity($conn, $rsoid);
					$uniname = getUniversityName($conn, $rsouniid);
					echo "<h2>" . $uniname . "</h2>";
					echo "<h4><b>" . $rsoname . "</b></h4>";
					echo "<p>" . $rsodesc . "</p>";
					
					if (isUserInRSO($conn, $rsoid, $userid) === TRUE) {
						echo "<button type='button'>RSO Already Joined</button>";
					}
					else {
						echo "<button type='button' onclick='attemptJoinRSO(" . $rsoid . ")'>Join RSO</button>";
					}
					echo "</li>";
				}
			}
			else {
				header("location: index.php");
			}
		?>
	</ul>
</section>

<?php

	include_once "footer.php";

?>