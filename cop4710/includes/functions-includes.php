<?php

function isInvalidUsername($uname) {
	// Checks for alphanumeric username
	if (!preg_match("/^[a-zA-Z0-9]*$/", $uname)) {
		return true;
	}
	else {
		return false;
	}
}

function isPasswordMismatch($pwd, $pwdRepeat) {
	// Checks for password and repeat password being the same
	if ($pwd !== $pwdRepeat) {
		return true;
	}
	else {
		return false;
	}
}

function userExists($conn, $uname) {
	$sql = "SELECT * FROM users WHERE usersName = ?;";
	// Creates a filter to prevent sql injections
	$stmt = mysqli_stmt_init($conn);
	// Checks for DB errors
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "s", $uname);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	// User exists, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	// User does not exist
	else {
		$result = false;
		return $result;
	}
	
}

function universityExists($conn, $uniname) {
	$sql = "SELECT * FROM universities WHERE universitiesName = ?;";
	// Creates a filter to prevent sql injections
	$stmt = mysqli_stmt_init($conn);
	// Checks for DB errors
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "s", $uniname);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	// User exists, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	// User does not exist
	else {
		$result = false;
		return $result;
	}
	
}

function createUser($conn, $uname, $pwd, $uniname, $rtnflag) {
	// CREATE USER
	// -----------
	$sql = "INSERT INTO users (usersName, usersPwd) VALUES (?, ?);";
	// Creates a filter to prevent sql injections
	$stmt = mysqli_stmt_init($conn);
	// Checks for DB errors
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	$hashPwd = password_hash($pwd, PASSWORD_DEFAULT);
	mysqli_stmt_bind_param($stmt, "ss", $uname, $hashPwd);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	// GETS NEW USER ID
	// ----------------
	$sql = "SELECT usersId FROM users WHERE usersName = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "s", $uname);
	mysqli_stmt_execute($stmt);
	// Saves the result of query (userid)
	$res = mysqli_stmt_get_result($stmt);
	while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
		$userid = $row[0];
	}
	mysqli_stmt_close($stmt);
	
	// GETS UNIVERSITY ID
	// ------------------
	$sql = "SELECT universitiesId FROM universities WHERE universitiesName = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "s", $uniname);
	mysqli_stmt_execute($stmt);
	// Saves the result of query (uniid)
	$res = mysqli_stmt_get_result($stmt);
	while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
		$uniid = $row[0];
	}
	mysqli_stmt_close($stmt);
	
	
	// LINKS UNIVERSITY TO USER
	// ------------------------
	$sql = "INSERT INTO universityuser (universityuserUserid, universityuserUniid) VALUES (?, ?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ii", $userid, $uniid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	if ($rtnflag == True) {
		header("location: signup.php?error=none");
	}
}

function locationExists($conn, $locLat, $locLong) {
	$sql = "SELECT * FROM locations WHERE locationsLat = ? AND locationsLong = ?;";
	// Creates a filter to prevent sql injections
	$stmt = mysqli_stmt_init($conn);
	// Checks for DB errors
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ss", $locLat, $locLong);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	// User exists, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	// User does not exist
	else {
		$result = false;
		return $result;
	}
}

function createlocation($conn, $lat, $long) {
	// CREATE LOCATION
	// ---------------
	$sql = "INSERT INTO locations (locationsLong, locationsLat) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ss", $long, $lat);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

function getLocationInfo($conn, $locid) {
	$sql = "SELECT * FROM locations WHERE locationsId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getLocationInfo";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $locid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	// return event data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	// data doesnt exist
	return FALSE;
}

function createSuperAdmin($conn, $cred_uname, $cred_pwd, $cred_uniname, $lat, $long, $desc) {
	// TESTS IF LOCATION EXISTS
	// ------------
	$locresult = locationExists($conn, $lat, $long);
	
	// Location does not exist, create new location and get id
	if ($locresult === FALSE) {
		createLocation($conn, $lat, $long);
	}
	
	// Runs query again with gauruntee that location exists
	$locresult = locationExists($conn, $lat, $long);
	$locationsid = $locresult['locationsId'];
	
	// CREATE UNIVERSITY
	// -----------------
	$sql = "INSERT INTO universities (universitiesName, universitiesDesc) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ss", $cred_uniname, $desc);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	// GETS NEW UNIVERSITY ID
	// ----------------------
	$sql = "SELECT universitiesId FROM universities WHERE universitiesName = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "s", $cred_uniname);
	mysqli_stmt_execute($stmt);
	// Gets new university id
	$res = mysqli_stmt_get_result($stmt);
	while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
		$uniid = $row[0];
	}
	mysqli_stmt_close($stmt);
	
	// CREATE UNIVERSITY LOCATION PAIR
	// -------------------------------
	$sql = "INSERT INTO universitylocation (universitylocationUid, universitylocationLid) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ii", $uniid, $locationsid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	// CREATES NEW USER
	// ----------------
	createUser($conn, $cred_uname, $cred_pwd, $cred_uniname, False);
	
	// GETS NEW USER ID
	// ----------------
	$sql = "SELECT usersId FROM users WHERE usersName = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "s", $cred_uname);
	mysqli_stmt_execute($stmt);
	// Gets new users id
	$res = mysqli_stmt_get_result($stmt);
	while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
		$usersid = $row[0];
	}
	mysqli_stmt_close($stmt);
	
	// CREATES NEW SUPERADMIN USER
	// ---------------------------
	$sql = "INSERT INTO superadmins (superadminsId) VALUES (?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../signup.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "i", $usersid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	header("location: ../signup.php?error=none");
}

function getSuperAdminId($conn, $uniid) {
	$sql = "SELECT * FROM superadmins WHERE superadminsId IN 
		(SELECT universityuserUserid FROM universityuser WHERE universityuserUniid = ?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getSuperAdminId";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $uniid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	
	// found superadmin, return id
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row['superadminsId'];
	}
	
	return FALSE;
}

function isSuperAdmin($conn, $usersId) {
	$sql = "SELECT * FROM superadmins WHERE superadminsId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: index.php");
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "s", $usersId);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	
	// User is super admin, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	
	return FALSE;
}

function isAdmin($conn, $usersId) {
	$sql = "SELECT * FROM admins WHERE adminsId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: index.php");
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "s", $usersId);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	
	// User is admin, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	return FALSE;
}

function loginUser($conn, $uname, $pwd) {
	$userExists = userExists($conn, $uname);
	
	if ($userExists === false) {
		header("location: ../login.php?error=incorrect_login");
		exit();
	}
	
	$hashedRealPwd = $userExists["usersPwd"];
	$checkPwd = password_verify($pwd, $hashedRealPwd);
	
	if ($checkPwd === false) {
		header("location: ../login.php?error=incorrect_login");
		exit();
	}
	// Creates a new session for the user
	else if ($checkPwd === true) {
		session_start();
		$_SESSION["usersId"] = $userExists["usersId"];
		$_SESSION["usersName"] = $userExists["usersName"];
		header("location: ../index.php");
		exit();
	}
	
}

function getUsersUniversity($conn, $usersId) {
	$sql = "SELECT * FROM universityuser WHERE universityuserUserid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getUsersUniversity";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $usersId);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row['universityuserUniid'];
	}
	return FALSE;
}

function getUsersName($conn, $usersId) {
	$sql = "SELECT * FROM users WHERE usersId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getUsersName";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $usersId);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row['usersName'];
	}
	return FALSE;
}

function getUniversityName($conn, $uniid) {
	$sql = "SELECT * FROM universities WHERE universitiesId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getUniversityName";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $uniid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	
	// uni exists, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row['universitiesName'];
	}
	// uni does not exist
	return FALSE;
}

function rsoExists($conn, $rsoname) {
	$sql = "SELECT * FROM rsos WHERE rsosName = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT rsoExists";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "s", $rsoname);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	
	// rso exists, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	// rso does not exist
	return FALSE;
}

function makeRSO($conn, $rsoname, $rsodesc, $usersid) {
	// Creates RSO
	$sql = "INSERT INTO rsos (rsosName, rsosDesc, rsosOwnerId) VALUES (?,?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../makerso.php?error=bad_stmt");
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ssi", $rsoname, $rsodesc, $usersid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	$ownersuniid = getUsersUniversity($conn, $usersid);
	
	// Adds connection between RSO and university
	$sql = "INSERT INTO rsouniversity (rsouniversityUid, rsouniversityRid) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../makerso.php?error=bad_stmt");
		exit();
	}
	
	$rsoid = rsoExists($conn, $rsoname);
	$rsoid = $rsoid['rsosId'];
	
	mysqli_stmt_bind_param($stmt, "ii", $ownersuniid, $rsoid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

function joinRSO($conn, $rsosId, $usersId) {
	$sql = "INSERT INTO rsouser (rsouserRid, rsouserUid) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT joinRSO";
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ii", $rsosId, $usersId);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

function leaveRSO($conn, $rsoid, $userid) {
	$sql = "DELETE FROM rsouser WHERE rsouserRid = ? AND rsouserUid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT leaveRSO";
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ii", $rsoid, $userid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	// THIS PART WOULDNT BE NECESSARY IF IT WASNT FOR MYSQL BEING STUPID WITH CASCADING DELETE TRIGGERS
	// gets number of students in rso
	$sql = "SELECT COUNT(*) AS total FROM rsouser WHERE rsouserRid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT leaveRSO";
		exit();
	}
	mysqli_stmt_bind_param($stmt, "i", $rsoid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	$row = mysqli_fetch_assoc($resultData);
	$usercount = $row['total'];
	
	// Checks if there are less than 5 students in the rso
	if ($usercount < 5) {
		$sql = "UPDATE rsos SET rsosStatus = 'inactive' WHERE rsosId = ?;";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt,$sql)) {
			echo "BAD STMT leaveRSO";
			exit();
		}
		mysqli_stmt_bind_param($stmt, "i", $rsoid);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
}

function getRSOInfo($conn, $rsosId) {
	$sql = "SELECT * FROM rsos WHERE rsosId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getRSOInfo";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $rsosId);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	// uni exists, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	// uni does not exist
	return FALSE;
}

function getRSOName($conn, $rsosId) {
	$sql = "SELECT * FROM rsos WHERE rsosId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getRSOName";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $rsosId);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	// uni exists, returning data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row['rsosName'];
	}
	// uni does not exist
	return FALSE;
}

function getUserRSOs($conn, $usersId) {
	$sql = "SELECT * FROM rsouser WHERE rsouserUid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getUserRSOs";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $usersId);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	// return result
	return $resultData;
}

function isUserInRSO($conn, $rsoid, $userid) {
	$sql = "SELECT * FROM rsouser WHERE rsouserRid = ? AND rsouserUid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT isUserInRSO";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ii", $rsoid, $userid);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	mysqli_stmt_close($stmt);
		
	// returns true if user is in rso
	if (mysqli_fetch_assoc($resultData)) {
		return TRUE;
	}
	return FALSE;
}

function getUniversityRSOs($conn, $uniid) {
	$sql = "SELECT * FROM rsouniversity WHERE rsouniversityUid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getUniversityRSOs";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $uniid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	// return result
	return $resultData;
}

function getRSOUniversity($conn, $rsosId) {
	$sql = "SELECT * FROM rsouniversity WHERE rsouniversityRid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getRSOUniversity";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $rsosId);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	
	// data exists
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row["rsouniversityUid"];
	}
	// data does not exist
	return FALSE;
}

function eventExists($conn, $eventdatetime, $eventlat, $eventlong) {
	$row = locationExists($conn, $eventlat, $eventlong);
	if ($row !== FALSE) {
		$locid = $row['locationsId'];
		$sql = "SELECT * FROM events WHERE eventsDateTime = ? AND eventsLid = ?;";
		// Creates a filter to prevent sql injections
		$stmt = mysqli_stmt_init($conn);
		// Checks for DB errors
		if (!mysqli_stmt_prepare($stmt,$sql)) {
			header("location: ../signup.php?error=bad_stmt");
			exit();
		}
		
		mysqli_stmt_bind_param($stmt, "si", $eventdatetime, $locid);
		mysqli_stmt_execute($stmt);
		
		$resultData = mysqli_stmt_get_result($stmt);
		
		// User exists, returning data
		if ($row = mysqli_fetch_assoc($resultData)) {
			return $row;
		}
		else {
			return FALSE;
		}
	}
	else {
		return FALSE;
	}
}

function createEvent($conn, $eventname, $eventdesc, $eventphone,
		$eventdatetime, $eventlat, $eventlong, $eventtype, $uniid) {
	if (locationExists($conn, $eventlat, $eventlong) === FALSE) {
		createLocation($conn, $eventlat, $eventlong);
	}
	
	$row = locationExists($conn, $eventlat, $eventlong);
	$locid = $row['locationsId'];
	
	
	$sql = "INSERT INTO events (eventsName, eventsDesc, eventsPhone, eventsDateTime, eventsLid, eventsUid)
			VALUES (?,?,?,?,?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT createEvent";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ssssii", $eventname, $eventdesc, $eventphone, $eventdatetime, $locid, $uniid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	$eventdata = eventExists($conn, $eventdatetime, $eventlat, $eventlong);
	$eventid = $eventdata['eventsId'];
	
	if ($eventtype === "private") {
		$sql = "INSERT INTO privateevents (privateeventsId) VALUES (?);";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt,$sql)) {
			echo "BAD STMT createEvent";
			exit();
		}
		mysqli_stmt_bind_param($stmt, "i", $eventid);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
	else {
		$sql = "INSERT INTO publicevents (publiceventsId) VALUES (?);";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt,$sql)) {
			echo "BAD STMT createEvent";
			exit();
		}
		mysqli_stmt_bind_param($stmt, "i", $eventid);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
}

function joinEvent($conn, $eventid, $userid) {
	$sql = "INSERT INTO eventuser (eventuserEid, eventuserUid) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT joinEvent";
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ii", $eventid, $userid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

function leaveEvent($conn, $eventid, $userid) {
	$sql = "DELETE FROM eventuser WHERE eventuserEid = ? AND eventuserUid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT leaveEvent";
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ii", $eventid, $userid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

function isUserInEvent($conn, $eventid, $userid) {
	$sql = "SELECT * FROM eventuser WHERE eventuserEid = ? AND eventuserUid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT isUserInEvent";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ii", $eventid, $userid);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	mysqli_stmt_close($stmt);
		
	// returns true if user is in rso
	if (mysqli_fetch_assoc($resultData)) {
		return TRUE;
	}
	return FALSE;
}

function isPublicEvent($conn, $eventid) {
	$sql = "SELECT * FROM publicevents WHERE publiceventsId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT isPublicEvent";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $eventid);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	mysqli_stmt_close($stmt);
		
	// returns true if user is in rso
	if (mysqli_fetch_assoc($resultData)) {
		return TRUE;
	}
	return FALSE;
}

function isPrivateEvent($conn, $eventid) {
	$sql = "SELECT * FROM privateevents WHERE privateeventsId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT isPrivateEvent";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $eventid);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	mysqli_stmt_close($stmt);
		
	// returns true if user is in rso
	if (mysqli_fetch_assoc($resultData)) {
		return TRUE;
	}
	return FALSE;
}

function isRSOEvent($conn, $eventid) {
	$sql = "SELECT * FROM rsoevents WHERE rsoeventsId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT isRSOEvent";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $eventid);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	mysqli_stmt_close($stmt);
		
	// returns true if user is in rso
	if (mysqli_fetch_assoc($resultData)) {
		return TRUE;
	}
	return FALSE;
}

function getEventType($conn, $eventid) {
	if (isPublicEvent($conn,$eventid)) {
		return "public";
	}
	else if (isPrivateEvent($conn,$eventid)) {
		return "private";
	}
	else if (isRSOEvent($conn,$eventid)) {
		return "rso";
	}
	else {
		return "unknown";
	}
}

function getEventInfo($conn, $eventid) {
	$sql = "SELECT * FROM events WHERE eventsId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getEventInfo";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $eventid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	// return event data
	if ($row = mysqli_fetch_assoc($resultData)) {
		return $row;
	}
	// data doesnt exist
	return FALSE;
}

function createRSOEvent($conn, $eventname, $eventdesc, $eventphone,
		$eventdatetime, $eventlat, $eventlong, $eventtype, $userid, $rsoid, $uniid){
	if (locationExists($conn, $eventlat, $eventlong) === FALSE) {
		createLocation($conn, $eventlat, $eventlong);
	}
	
	$row = locationExists($conn, $eventlat, $eventlong);
	$locid = $row['locationsId'];
	
	
	$sql = "INSERT INTO events (eventsName, eventsDesc, eventsPhone, eventsDateTime, eventsLid, eventsRid, eventsUid)
			VALUES (?,?,?,?,?,?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT createRSOEvent";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ssssiii", $eventname, $eventdesc, $eventphone, $eventdatetime, $locid, $rsoid, $uniid);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	$eventdata = eventExists($conn, $eventdatetime, $eventlat, $eventlong);
	$eventid = $eventdata['eventsId'];
	
	if ($eventtype === "private") {
		$sql = "INSERT INTO privateevents (privateeventsId) VALUES (?);";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt,$sql)) {
			echo "BAD STMT createRSOEvent";
			exit();
		}
		mysqli_stmt_bind_param($stmt, "i", $eventid);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
	else if ($eventtype === "public"){
		$sql = "INSERT INTO publicevents (publiceventsId) VALUES (?);";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt,$sql)) {
			echo "BAD STMT createRSOEvent";
			exit();
		}
		mysqli_stmt_bind_param($stmt, "i", $eventid);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
	else {
		$sql = "INSERT INTO rsoevents (rsoeventsId) VALUES (?);";
		$stmt = mysqli_stmt_init($conn);
		if (!mysqli_stmt_prepare($stmt,$sql)) {
			echo "BAD STMT createRSOEvent";
			exit();
		}
		mysqli_stmt_bind_param($stmt, "i", $eventid);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
	
	joinEvent($conn, $eventid, $userid);
}

function requestEvent($conn, $eventname, $eventdesc, $eventphone,
		$eventdatetime, $eventlat, $eventlong, $eventtype, $usersid) {
	$uniid = getUsersUniversity($conn, $usersid);
	// Creates new event
	createEvent($conn, $eventname, $eventdesc, $eventphone,
		$eventdatetime, $eventlat, $eventlong, $eventtype, $uniid);
	$row = eventExists($conn, $eventdatetime, $eventlat, $eventlong);
	$eventid = $row['eventsId'];
	
	// Add event to request public events table
	$uniid = getUsersUniversity($conn, $usersid);
	$said = getSuperAdminId($conn, $uniid);
	
	$sql = "INSERT INTO eventapproval (eventapprovalEid, eventapprovalSid) VALUES (?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		header("location: ../requestevent.php?error=bad_stmt");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "ii", $eventid, $said);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	
	joinEvent($conn, $eventid, $usersid);
}

function getEventRequests($conn, $said) {
	$sql = "SELECT * FROM eventapproval WHERE eventapprovalSid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getEventRequests";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $said);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	// return result
	return $resultData;
}

function getActiveRSOs($conn, $aid) {
	$sql = "SELECT * FROM rsos WHERE rsosOwnerId = ? AND rsosStatus = 'active';";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getActiveRSOs";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $aid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	return $resultData;
}

function validateCreateEvent($conn, $rsoid, $aid) {
	$sql = "SELECT * FROM rsos WHERE rsosId = ? AND rsosOwnerId = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT validateCreateEvent";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ii", $rsoid, $aid);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	mysqli_stmt_close($stmt);
		
	// checks if admin is attempting to create an event for a different rso
	if (mysqli_fetch_assoc($resultData)) {
		return TRUE;
	}
	return FALSE;
}

function getAvailableDisplayEvents($conn, $userid) {
	$uniid = getUsersUniversity($conn, $userid);
	// MYSQL ALSO DOESNT SUPPORT EXCEPT AAAA
	$sql = "(SELECT * FROM events WHERE (eventsId IN (SELECT publiceventsId FROM publicevents))
			AND (eventsId NOT IN (SELECT eventapprovalEid FROM eventapproval)))
		UNION
		(SELECT * FROM events WHERE (eventsId IN (SELECT privateeventsId FROM privateevents)) AND eventsUid = ?
			AND (eventsId NOT IN (SELECT eventapprovalEid FROM eventapproval)))
		UNION
		(SELECT * FROM events WHERE (eventsRid IN (SELECT rsouserRid FROM rsouser WHERE rsouserUid = ?)));";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getAvailableDisplayEvents";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ii", $uniid, $userid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	return $resultData;
}

function getAvailableUserEvents($conn, $userid) {	
	$sql = "SELECT * FROM events WHERE (eventsId IN (SELECT eventuserEid FROM eventuser WHERE eventuserUid = ? ))
		AND (eventsId NOT IN (SELECT eventapprovalEid FROM eventapproval));";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getAvailableUserEvents";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $userid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	return $resultData;
}

function verifyCommenter($conn, $eventid, $userid) {
	$sql = "SELECT * FROM eventuser WHERE eventuserEid = ? AND eventuserUid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT verifyCommenter";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "ii", $eventid, $userid);
	mysqli_stmt_execute($stmt);
	
	$resultData = mysqli_stmt_get_result($stmt);
	
	mysqli_stmt_close($stmt);
		
	// checks if admin is attempting to create an event for a different rso
	if (mysqli_fetch_assoc($resultData)) {
		return TRUE;
	}
	return FALSE;
}

function makeComment($conn, $eventid, $userid, $desc) {
	if (verifyCommenter($conn, $eventid, $userid) === FALSE) {
		return FALSE;
	}
	
	$sql = "INSERT INTO comments (commentsEid, commentsUid, commentsDesc) VALUES (?,?,?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT makeComment";
		return FALSE;
	}
	mysqli_stmt_bind_param($stmt, "iis", $eventid, $userid, $desc);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	return TRUE;
}

function getAvailableComments($conn, $eventid, $userid) {
	if (verifyCommenter($conn, $eventid, $userid) === FALSE) {
		return FALSE;
	}
	
	$sql = "SELECT * FROM comments WHERE commentsEid = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT getAvailableComments";
		exit();
	}
	
	mysqli_stmt_bind_param($stmt, "i", $eventid);
	mysqli_stmt_execute($stmt);
	$resultData = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	
	// return result
	return $resultData;
}

function editComment($conn, $eventid, $userid, $originaltime, $desc) {
	if (verifyCommenter($conn, $eventid, $userid) === FALSE) {
		return FALSE;
	}
	
	$sql = "UPDATE comments SET desc = ? WHERE commentsEid = ? AND commentsUid = ? AND commentsTime = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt,$sql)) {
		echo "BAD STMT editComment";
		return FALSE;
	}
	mysqli_stmt_bind_param($stmt, "siis", $desc, $eventid, $userid, $originaltime);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	return TRUE;
}

// ------------------------
// CHECK IF FORMS ARE EMPTY
// ------------------------

function isEmptySignup($uname, $pwd, $pwdRepeat, $uniname) {
	if (empty($uname) || empty($pwd) || empty($pwdRepeat) || empty($uniname)) {
		return true;
	}
	else {
		return false;
	}
}

function isEmptyLogin($uname, $pwd) {
	if (empty($uname) || empty($pwd)) {
		return true;
	}
	else {
		return false;
	}
}

function isEmptySignupSA($lat, $long, $desc) {
	if (empty($lat) || empty($long) || empty($desc)) {
		return true;
	}
	else {
		return false;
	}
}

function isEmptyMakeRSO($rsoname, $rsodesc) {
	if (empty($rsoname) || empty($rsodesc)) {
		return true;
	}
	else {
		return false;
	}
}

function isEmptyRequestEvent($eventname, $eventdesc, $eventphone, $eventdate, $eventtime, $eventtime2,
			$eventlat, $eventlong, $eventtype) {
	if (empty($eventname) || empty($eventdesc) || empty($eventphone) || empty($eventdate) || empty($eventtime)
			 || empty($eventtime2) || empty($eventlat) || empty($eventlong) || empty($eventtype)) {
		return true;
	}
	else {
		return false;
	}
}

function isEmptyCreateEvent($rsoid, $eventname, $eventdesc, $eventphone, $eventdate, $eventtime, $eventtime2,
			$eventlat, $eventlong, $eventtype) {
	if (empty($rsoid) || empty($eventname) || empty($eventdesc) || empty($eventphone) || empty($eventdate) || empty($eventtime)
			 || empty($eventtime2) || empty($eventlat) || empty($eventlong) || empty($eventtype)) {
		return true;
	}
	else {
		return false;
	}
}
