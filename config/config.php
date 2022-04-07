<?php
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "login_system";

	// Create connection
	$mysql_db = new mysqli($servername, $username, $password);
	// Check connection
	if ($mysql_db->connect_error) {
	  die("Connection failed: " . $mysql_db->connect_error);
	}

	// Create database
	$sql = "CREATE DATABASE IF NOT EXISTS login_system";
	if ($mysql_db->query($sql) === TRUE) {
	  echo "!";
	} else {
	  echo "Error creating database: " . $mysql_db->error;
	}
/*
	$tsql = "CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
	)";
	if ($conn->query($tsql) === TRUE) {
	  echo "!";
	} else {
	  echo "Error creating database: " . $conn->error;
	}
	*/


	$con = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($con->connect_error) {
	  die("Connection failed: " . $con->connect_error);
	}
	
	$tsql = "CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
	)";
	if ($con->query($tsql) === TRUE) {
	  echo "!";
	} else {
	  echo "Error creating database: " . $con->error;
	}


	
	/*
	// Database credentials
	define('DB_SERVER', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '');
	define('DB_NAME', 'login_system');

	// Attempt to connect to MySQL database
	$mysql_db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

	if (!$mysql_db) {//was: !$mysql_db
		die("Error: Unable to connect " . $mysql_db->connect_error);
	}
	
	//Create Database
	$sql = "CREATE DATABASE login_system";
	if ($mysql_db->query($sql) === TRUE) {
	  echo "Database created successfully";
	} else {
	  echo "Error creating database: " . $mysql_db->error;
	}

	$mysql_db->close();*/
	?>