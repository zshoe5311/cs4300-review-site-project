<?php
	session_start();
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "reviewsitedata";
	
	$mysql_db = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($mysql_db->connect_error) {
	  die("Connection failed: " . $mysql_db->connect_error);
	}
	
	$albumName = $albumArtist = $albumDescription = $albumArt = '';
	$sql = 'SELECT albumName, albumArtist, albumDescription, albumArt, avgScore FROM albums WHERE albumID = ?';
	
	if ($stmt = $mysql_db->prepare($sql)) {
			$aID = $_SESSION['albNum'];
			$stmt->bind_param('i', $aID);
			if ($stmt->execute()) {
				$stmt->store_result();
				if ($stmt->num_rows == 1) {
					$stmt->bind_result($albumName, $albumArtist, $albumDescription, $albumArt, $avgScore);
					$stmt->fetch();
				}
			}
	}
	$mysql_db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="newStyle.css">
  <title>Album Name - MM</title>
</head>
<body>
    <?php include 'hdr.php'; ?>
	<div class="albumDescript hItem">
		<img class="albumArt" src="albArt/<?php echo $albumArt; ?>">
		<div class="aDText">
			<h1><?php echo $albumName; ?></h1>
			<h2>Artist: <?php echo $albumArtist; ?>&emsp;&emsp;<font size="+3">MM Score: <?php echo $avgScore; ?></font></h2>
			<p><?php echo $albumDescription; ?></p>
		</div>
	</div>
	<div class="homeLetter hItem" style="margin-top: 50px; margin-right: 70px;">
		<h1>User Reviews</h1>
	</div>
	<a class="hubButtons hItem" href="createReview.php">Create Review</a>
	<div class="reviewPost hItem">
		<div class="postText">	
			<h2 id="postUser"><a href="home.php"><font size="+2">username</font></a>&emsp;&emsp;Score: can i get uhhh/10</h2>
			<p>eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,
			eatin' a burger wit no honey mustard,</p>
		</div>
	</div>
</body>
</html>
