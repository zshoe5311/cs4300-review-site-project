<?php
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "reviewsitedata";

	date_default_timezone_set('America/New_York');

	// Create connection to server
	$mysql_db = new mysqli($servername, $username, $password);
	// Check connection
	if ($mysql_db->connect_error) {
	  die("Connection failed: " . $mysql_db->connect_error);
	}

	// Create database if it doesn't exist
	$sql = "CREATE DATABASE IF NOT EXISTS reviewsitedata";
	if ($mysql_db->query($sql) === TRUE) {
	  //echo "!";
	} else {
	  echo "Error creating database: " . $mysql_db->error;
	}

	$con = new mysqli($servername, $username, $password, $dbname); // Creates connection to database
	// Check connection
	if ($con->connect_error) {
	  die("Connection failed: " . $con->connect_error);
	}
	
	//loading user data to database
	$usql = "CREATE TABLE IF NOT EXISTS `users` (
	`id` int(11) NOT NULL,
	`username` varchar(50) NOT NULL,
	`password` varchar(255) NOT NULL,
	`created_at` datetime DEFAULT current_timestamp(),
	`isAdmin` tinyint(1) NOT NULL,
	`isBanned` tinyint(1) NOT NULL
	)";
	
	if ($con->query($usql) === TRUE) { // creates user table if it does not exist
	  //echo "!";
	} else {
	  echo "Error creating users table: " . $con->error;
	}
	
	$bsql = "SELECT COUNT(*) AS cnt FROM users";
	$stmt = $con->query($bsql);
	$result = $stmt->fetch_assoc();
	if ($result['cnt'] < 1 && !empty(file_get_contents("uData.txt"))) { //if there are 0 entries in the user data table and the uData text file is not empty, the below code adds all the entries stored in the uData text file
		$myfile = fopen("uData.txt", "r") or die("Unable to open uData!"); // to the table in the database
		$uID = $uName = $uPW = $uCreatedAt = ''; 
		$uAdmin = $uBanned = 0;
		while(!feof($myfile)) { //this is the loop that adds every entry in the uData text file to the table in the database
			$uID = trim(fgets($myfile));
			$uName = trim(fgets($myfile));
			$uPW = trim(fgets($myfile));
			$uCreatedAt = trim(fgets($myfile));
			$uAdmin = trim(fgets($myfile));
			$uBanned = trim(fgets($myfile));
			$isql = "INSERT INTO `users`(`id`, `username`, `password`, `created_at`, `isAdmin`, `isBanned`) 
			VALUES (?,?,?,?,?,?)";
			if ($stmt = $con->prepare($isql)) {
				$stmt->bind_param('isssii', $uID, $uName, $uPW, $uCreatedAt, $uAdmin, $uBanned);
				if ($stmt->execute()) {
					//echo "!";
				}		
			}
		}
		fclose($myfile);
	}
	
	//loading album data to database
	$asql = "CREATE TABLE IF NOT EXISTS `albums` (
	  `albumName` varchar(255) NOT NULL,
	  `albumArtist` varchar(255) NOT NULL,
	  `albumDescription` text NOT NULL,
	  `albumArt` varchar(255) NOT NULL,
	  `avgScore` float NOT NULL,
	  `albumID` int(11) NOT NULL
	)";
	
	if ($con->query($asql) === TRUE) { // creates album table if it does not exist
	  //echo "!";
	} else {
	  echo "Error creating albums table: " . $con->error;
	}
	
	$bsql = "SELECT COUNT(*) AS cnt FROM albums";
	$stmt = $con->query($bsql);
	$result = $stmt->fetch_assoc();
	$myfile = fopen("aData.txt", "r") or die("Unable to open aData!");
	if ($result['cnt'] < 1 && !empty(file_get_contents("aData.txt"))) { //if there are 0 entries in the album data table and the aData text file is not empty, the below code adds all the entries stored in the aData text file
		$aDesc = $aName = $aArtist = $alArt = $aID = ''; // to the table in the database
		$aScore = 0;
		while(!feof($myfile)) { //this is the loop that adds every entry in the aData text file to the table in the database
			$aID = trim(fgets($myfile));
			$aArtist = trim(fgets($myfile));
			$aDesc = trim(fgets($myfile));
			$alArt = trim(fgets($myfile));
			$aScore = trim(fgets($myfile));
			$aName = trim(fgets($myfile));
			$isql = "INSERT INTO `albums`(`albumName`, `albumArtist`, `albumDescription`, `albumArt`, `avgScore`, `albumID`) 
			VALUES (?,?,?,?,?,?)";
			if ($stmt = $con->prepare($isql)) {
				$stmt->bind_param('ssssdi', $aName, $aArtist, $aDesc, $alArt, $aScore, $aID);
				if ($stmt->execute()) {
					//echo "!";
				}		
			}
		}
	}
	fclose($myfile);
	
	//loading review data to database
	$rsql = "CREATE TABLE IF NOT EXISTS `reviews` (
	  `reviewID` int(11) NOT NULL,
	  `albumID` int(11) NOT NULL,
	  `reviewDescript` text NOT NULL,
	  `reviewScore` int(11) NOT NULL,
	  `authorUsername` varchar(255) NOT NULL,
	  `postingDate` datetime NOT NULL
	)";
	
	if ($con->query($rsql) === TRUE) { // creates review table if it does not exist
	  //echo "!";
	} else {
	  echo "Error creating reviews table: " . $con->error;
	}
	
	$bsql = "SELECT COUNT(*) AS cnt FROM reviews";
	$stmt = $con->query($bsql);
	$result = $stmt->fetch_assoc();
	$myfile = fopen("rData.txt", "r") or die("Unable to open rData!");
	if ($result['cnt'] < 1 && !empty(file_get_contents("rData.txt"))) { //if there are 0 entries in the review data table and the rData text file is not empty, the below code adds all the entries stored in the rData text file
		$rID = $aID = $rDesc = $postDate = $author = ''; // to the table in the database
		$rScore = 0;
		while(!feof($myfile)) { //this is the loop that adds every entry in the rData text file to the table in the database
			$rID = trim(fgets($myfile));
			$aID = trim(fgets($myfile));
			$rDesc = trim(fgets($myfile));
			$rScore = trim(fgets($myfile));
			$author = trim(fgets($myfile));
			$postDate = trim(fgets($myfile));
			$isql = "INSERT INTO `reviews`(`reviewID`, `albumID`, `reviewDescript`, `reviewScore`, `authorUsername`, `postingDate`) VALUES (?,?,?,?,?,?)";
			if ($stmt = $con->prepare($isql)) {
				$stmt->bind_param('iisiss', $rID, $aID, $rDesc, $rScore, $author, $postDate);
				if ($stmt->execute()) {
					//echo "!";
				}		
			}
		}
	}
	fclose($myfile);
	
	?>