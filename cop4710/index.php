<?php

	include_once "header.php";

?>

<html>
<head><title>COP 4710 Database Project</title></head>
<body>

<?php
	if (isset($_SESSION["usersId"])) {
		echo "<h2>Welcome, " . $_SESSION['usersName'] . "!</h2>";
	}
?>

<!--
<form action="process.php" method="post">
	<input name="name" type="text">
	<input type="submit">
</form>
-->
<h4>Please use the links above for navigation.</h4>

</body>
</html>