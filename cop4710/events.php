<?php

	include_once "header.php"

?>

<link rel="stylesheet" href="css/eventstyle.css">

<script src="vendor/jquery/jquery.min.js"></script>
<script src="js/EventsAjax.js"></script>

<h2>All Available Events</h2>

<section class="events">
	<?php
		if (isset($_SESSION["usersId"])) {
			$useruni = getUsersUniversity($conn, $_SESSION["usersId"]);
			$uniname = getUniversityName($conn, $useruni);
		}
		else {
				header("location: index.php");
		}
	?>
	<ul class="tilesWrapEvents">
		<?php
			if (isset($_SESSION["usersId"])) {
				$userid = $_SESSION["usersId"];
				$eventdata = getAvailableDisplayEvents($conn, $userid);
				while($row = mysqli_fetch_assoc($eventdata)) {
					echo "<li>";
					$eventid = $row["eventsId"];
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
					
					if (isUserInEvent($conn, $eventid, $userid) === TRUE) {
						echo "<button type='button'>Event Already Joined</button>";
					}
					else {
						echo "<button type='button' onclick='attemptJoinEvent(" . $eventid . ")'>Join Event</button>";
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