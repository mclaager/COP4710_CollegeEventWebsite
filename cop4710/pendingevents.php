<?php

	include_once "header.php";

?>

<link rel="stylesheet" href="css/eventstyle.css">

<script src="vendor/jquery/jquery.min.js"></script>
<script src="js/PendingEventsAjax.js"></script>

<section class="events">
	<h2> All Pending Events for Your University </h2>
	<ul class="tilesWrapEvents">
		<?php
			if (isset($_SESSION["usersId"]) AND isSuperAdmin($conn, $_SESSION["usersId"]) !== FALSE) {
				$usersid = $_SESSION["usersId"];
				$rsodata = getEventRequests($conn, $usersid);
				while($row = mysqli_fetch_assoc($rsodata)) {
					echo "<li>";
					$eventid = $row["eventapprovalEid"];
					$eventinfo = getEventInfo($conn, $eventid);
					$eventname = $eventinfo["eventsName"];
					$eventdesc = $eventinfo["eventsDesc"];
					$eventphone = $eventinfo["eventsPhone"];
					$eventdatetime = $eventinfo["eventsDateTime"];
					$eventuniid = $eventinfo["eventsUid"];
					$eventlid = $eventinfo["eventsLid"];
					$eventlocinfo = getLocationInfo($conn, $eventlid);
					$eventlat = $eventlocinfo["locationsLat"];
					$eventlong = $eventlocinfo["locationsLong"];
					$uniname = getUniversityName($conn, $eventuniid);
					$eventdatetime = strtotime($eventdatetime);
					$eventtype = getEventType($conn, $eventid);
					// Shows RSO if applicable
					if ($eventinfo["eventsRid"] !== NULL) {
						$eventrid = $eventinfo["eventsRid"];
						$eventrsoname = getRSOName($conn, $eventrid);
						echo "<h2>" . $eventrsoname . "<br>" . $uniname . "</h2>";
					}
					else {
						echo "<h2>" . $uniname . "</h2>";
					}
					echo "<h4><b>" . $eventname . "<br>" . date('m-d-Y', $eventdatetime) . 
							"<br>" . date('ha', $eventdatetime) ."</b></h4>";
					//echo "<h4><b>TEST</b></h4>";
					echo "<p>" . $eventdesc . "<br><br>Latitude: " . $eventlat . "<br>Longitude: " . $eventlong .
						"<br><br>Phone #: ". $eventphone . "<br><br>Visibility: " . strtoupper($eventtype) . "</p>";
					echo "<button type='button' onclick='acceptEvent(" . $eventid . ")'>ACCEPT</button>";
					echo "<br>";
					echo "<button type='button' onclick='denyEvent(" . $eventid . ")'>DENY</button>";
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