<?php

	include_once "header.php";

?>

<link rel="stylesheet" href="css/rsostyle.css">

<script src="vendor/jquery/jquery.min.js"></script>
<script src="js/MyRSOsAjax.js"></script>

<section class="events">
	<h2> Your RSOs </h2>
	<ul class="tilesWrap">
		<?php
			if (isset($_SESSION["usersId"])) {
				$rsodata = getUserRSOs($conn, $_SESSION["usersId"]);
				while($row = mysqli_fetch_assoc($rsodata)) {
					echo "<li>";
					$rsoid = $row["rsouserRid"];
					$rsoinfo = getRSOInfo($conn, $rsoid);
					$rsoname = $rsoinfo["rsosName"];
					$rsodesc = $rsoinfo["rsosDesc"];
					$rsouniid = getRSOUniversity($conn, $rsoid);
					$uniname = getUniversityName($conn, $rsouniid);
					echo "<h2>" . $uniname . "</h2>";
					echo "<h4><b>" . $rsoname . "</b></h4>";
					echo "<p>" . $rsodesc . "</p>";
					echo "<button type='button' onclick='attemptLeaveRSO(" . $rsoid . ")'>Leave RSO</button>";
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