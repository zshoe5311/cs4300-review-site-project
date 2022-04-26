<?php
	session_start();
	
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
		header("location: index.php");
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="newStyle.css">
	<title>Create Review - MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<div class="homeLetter hItem">	
		<h1>You are writing a review for ALBUMNAME!</h1>
		<p>Please note the word limit, and remember to give it a score out of 10, but otherwise express
		your thoughts freely on this album</p>
	</div>
	<div class="createRText hItem">
		<textarea rows="10" cols="100">Enter Review Here...</textarea>
	</div>
	<div class="hItem scoreInput">
		<h2>Enter your score out of 10 below! (No negatives)</h2>
		<input type="text">
		<a class="hubButtons hItem" href="albumProto.php">Submit Review</a>
	</div>
</body>
</html>