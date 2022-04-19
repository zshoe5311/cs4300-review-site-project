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

	$albumName = $albumArtist = $albumArt = '';
	$avgScore = 0;
	$num = 1;
	$nextQuery = true;

	$albumCheck = "SELECT COUNT(albumID) FROM albums";
	$aStmt = $mysql_db->prepare($albumCheck);
	$aStmt->execute();
	$aStmt->store_result();
	if ($aStmt->num_rows == 0) {
		$nextQuery = false;
	}
	
	
	function createAlbumQuery($conn, $aID) {
		$sql = 'SELECT albumName, albumArtist, albumArt, avgScore FROM albums WHERE albumID = ?';
		if ($stmt = $conn->prepare($sql)) {
				$stmt->bind_param('i', $aID);
				if ($stmt->execute()) {
					$stmt->store_result();
					if ($stmt->num_rows == 1) {
						$stmt->bind_result($albumName, $albumArtist, $albumArt, $avgScore);
						$stmt->fetch();
						echo '<button class="albumQuery hItem" type="submit" name="albButton" value="'.$aID.'">
							<img src="'.$albumArt.'">
							<h1>'.$albumName.'</h1>
							<p>Artist: '.$albumArtist.'<br>MM Score: '.$avgScore.'</p>
						</button>';
					} else {
						$nextQuery = false;
					}
				}
		}
	}
	
	if (isset($_REQUEST['albButton'])) {
		$_SESSION['albNum'] = $_REQUEST['albButton'];
		$mysql_db->close();
		header('location: albumProto.php');
	}
	
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="newStyle.css">
	<title>Editor's Choice -MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<div class="homeLetter hItem">
		<h1>List Elements:</h1>
	</div>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<?php
			while ($nextQuery && $num <= 5) {
				createAlbumQuery($mysql_db, $num);
				$num = $num + 1;
			}
		?>
	</form>
</body>
</html>