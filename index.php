<?php
	session_start();
	
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {//Sets 'loggedin' session variable to false if it has not been set yet
		$_SESSION['loggedin'] = false;
	}
	
	require_once "config/config.php"; //runs config to create database
	$con->close();
	$mysql_db->close(); //the above 3 lines are to make sure the database is here and complete
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="newStyle.css">
	<title>Home - MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?> <!-- includes hub bar-->
	<div class="homeLetter hItem slide-in-fwd-center" style="margin: 0px 550px 40px;">	<!--the below code is used to create the text blobs on the home page-->
		<h1>Welcome to Music Madness!</h1>
		<p style="font-size: 20px;">We are a small music review page looking to not only share our opinions on music, but to create a community of discussion and enjoyment for the music we like across all genres. </p>
	</div>
	<div class="genSection hItem slide-in-fwd-center" style="width: 70%">	
		<div id="homeText" style="text-align: left; padding: 10px;">
			<h2>We aim to grow a community that is as passionate for music as we are:</h2>
			<p style="font-size: 20px;">Our website started out as just a way for us to share our own opinions with each other on the music that we like. But the bigger it has grown, and the more
			albums that have been added, we felt it was time to share the love, and continue growing our website to not only discover new music from others, but share our own favorite music with them.  </p>
		</div>
	</div>
</body>
</html>