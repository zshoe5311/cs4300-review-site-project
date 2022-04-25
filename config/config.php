<?php
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "reviewsitedata";

	// Create connection
	$mysql_db = new mysqli($servername, $username, $password);
	// Check connection
	if ($mysql_db->connect_error) {
	  die("Connection failed: " . $mysql_db->connect_error);
	}

	// Create database
	$sql = "CREATE DATABASE IF NOT EXISTS reviewsitedata";
	if ($mysql_db->query($sql) === TRUE) {
	  echo "!";
	} else {
	  echo "Error creating database: " . $mysql_db->error;
	}

	$con = new mysqli($servername, $username, $password, $dbname);
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
	
	if ($con->query($usql) === TRUE) {
	  echo "!";
	} else {
	  echo "Error creating users table: " . $con->error;
	}
	
	$bsql = "SELECT COUNT(*) AS cnt FROM users";
	$stmt = $con->query($bsql);
	$result = $stmt->fetch_assoc();
	if ($result['cnt'] < 1) {
		$myfile = fopen("uData.txt", "r") or die("Unable to open uData!");
		$uID = $uName = $uPW = $uCreatedAt = '';
		$uAdmin = $uBanned = 0;
		while(!feof($myfile)) {
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
					echo "!";
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
	
	if ($con->query($asql) === TRUE) {
	  echo "!";
	} else {
	  echo "Error creating albums table: " . $con->error;
	}
	
	$bsql = "SELECT COUNT(*) AS cnt FROM albums";
	$stmt = $con->query($bsql);
	$result = $stmt->fetch_assoc();
	$myfile = fopen("aData.txt", "r") or die("Unable to open aData!");
	if ($result['cnt'] < 1 && !empty(file_get_contents("aData.txt"))) {
		$aDesc = $aName = $aArtist = $alArt = '';
		$aScore = $aID = 0;
		while(!feof($myfile)) {
			$aID = trim(fgets($myfile));
			$aArtist = trim(fgets($myfile));
			$aDesc = trim(fgets($myfile));
			$alArt = trim(fgets($myfile));
			$aScore = trim(fgets($myfile));
			$aName = trim(fgets($myfile));
			$isql = "INSERT INTO `albums`(`albumName`, `albumArtist`, `albumDescription`, `albumArt`, `avgScore`, `albumID`) 
			VALUES (?,?,?,?,?,?)";
			if ($stmt = $con->prepare($isql)) {
				$stmt->bind_param('ssssii', $aName, $aArtist, $aDesc, $alArt, $aScore, $aID);
				if ($stmt->execute()) {
					echo "!";
				}		
			}
		}
	}
	fclose($myfile);
	
	?>