<?php

	include_once "header.php";

?>

<link rel="stylesheet" href="css/eventstyle.css">

<script src="vendor/jquery/jquery.min.js"></script>
<script src="js/MyEventsAjax.js"></script>

<style>
	.modal {
		display: none;
        position: fixed;
        z-index: 8;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.8);
	}
	.modal-content {
		margin: 50px auto;
        border: 1px solid #999;
		background-color: #eee;
        width: 60%;
	}
	span {
        color: #666;
        display: block;
        padding: 0 0 5px;
    }
	input,
    textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        outline: none;
    }
	.modal-content h2 {
		color: #262a2b;
		text-align: center;
	}
	.new-comment button {
        width: 100%;
        padding: 10px;
        border: none;
        background: #444;
        font-size: 16px;
        font-weight: 400;
        color: #fff;
    }
	.display_comments button {
        width: 25%;
        padding: 5px;
        border: none;
        background: #888;
        font-size: 14px;
        font-weight: 400;
        color: #fff;
    }
    button:hover {
        background: #333;
    }
</style>

<h2>Your Events</h2>

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
				$eventdata = getAvailableUserEvents($conn, $userid);
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
					
					echo "<button type='button' onclick='gotoComments(" . $eventid . ")'>Comments</button>";
					echo "<button type='button' onclick='attemptLeaveEvent(" . $eventid . ")'>Leave Event</button>";
					echo "</li>";
				}
			}
			else {
				header("location: index.php");
			}
		?>
	</ul>
</section>

<!-- Comments -->
<div id="modal" class="modal">
	<div class="modal-content">
		<div class="new-comment">
			<a class="close">&times;</a>
			<form method="POST" id="new_comment_form">
				<h2>Create New Comment</h2>
				<div>
					<textarea id="comment_field" name="comment_field" rows="2" placeholder="Comment goes here."></textarea>
					<input type="hidden" id="eventid" name="eventid" value="-1">
				</div>
				<button type="submit" id='new_comment_btn' onclick="">Submit</button>
			</form>
			<span id="comment_status"></span>
			<br />
			<div class="display_comments" id="display_comments"></div>
		</div>
		
		<div class="comments">
		</div>
	</div>
</div>


<script>
	function alertThenReload(msg) {
		alert(msg);
		window.location.reload(false);
	}

	function gotoComments (eventid) {
		document.getElementById("modal").style.display = "block";
		document.getElementById("eventid").value = eventid;
		listComments();
	}
		
	function listComments() {
		var eventid = document.getElementById("eventid").value;
		
		$.ajax({
			url: "includes/listcomments-includes.php",
			method: "POST",
			dataType: "text",
			data: {method: "listCommentsHelper", eventid: eventid},
			success: function(data) {
				$("#display_comments").html(data);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) { 
				alert("Status: " + textStatus);
				alertThenReload("Error: " + errorThrown); 
			}  
		});
	}

	$("#new_comment_form").on("submit", function(event){
		event.preventDefault();
		var form_data = $(this).serializeArray();

		form_data.push({name: "method", value: "submitCommentHelper"});
		$.ajax({
			url: "includes/submitcomments-includes.php",
			dataType: "JSON",
			method: "POST",
			data: $.param(form_data),
			success: function(response) {
				console.log(response);
				if (response != null) {
					document.getElementById("comment_field").value = "";
					$("#comment_status").html(response);
					listComments(eventid);
				}
				else {
					alertThenReload("Error - Could not create comment.");
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) { 
				alert("Status: " + textStatus);
				alertThenReload("Error: " + errorThrown); 
			}  
		})
	});

	window.onclick = function(event) {
		if (event.target.className === "modal") {
			event.target.style.display = "none";
			$("#comment_status").html("");
		}
	}
</script>
<?php

	include_once "footer.php";

?>