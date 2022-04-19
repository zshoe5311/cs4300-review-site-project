<?php
	session_start();
	
	if ($_SESSION['isAdmin'] == 0) {
		header('location: home.php');
	} else {		
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "reviewsitedata";
		
		$mysql_db = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($mysql_db->connect_error) {
		  die("Connection failed: " . $mysql_db->connect_error);
		}
		
		$input_err = "";
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="newStyle.css">
	<title>Delete Album - MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<?php
		$result = $mysql_db->query("SELECT * FROM albums");
		
		echo "<div class=\"genSection hItem\"> <table border='1'>
		<tr>
		<th>albumName</th>
		<th>albumArtist</th>
		<th>albDescript</th>
		<th>albArt</th>
		<th>avgScore</th>
		<th>albID</th>
		</tr>";

		while($row = $result->fetch_assoc())
		{
			echo "<tr>";
			echo "<td>" . $row['albumName'] . "</td>";
			echo "<td>" . $row['albumArtist'] . "</td>";
			echo "<td>" . $row['albumDescription'] . "</td>";
			echo "<td>" . $row['albumArt'] . "</td>";
			echo "<td>" . $row['avgScore'] . "</td>";
			echo "<td>" . $row['albumID'] . "</td>";
			echo "</tr>";
		}
		echo "</table></div>";
		
		$mysql_db->close();
	?>
	<div class="genSection hItem">	
		<h1>Choose your action:</h1>
		<a class="hubButtons" href="addAlbum.php"> Add Album</a>
	</div>
</body>
</html>