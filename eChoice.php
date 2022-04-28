<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
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
	$nextQuery = true;
	//$nextQuery = $_SESSION['nQ'];

	/*$albumCheck = "SELECT COUNT(albumID) FROM albums";
	$aStmt = $mysql_db->prepare($albumCheck);
	$aStmt->execute();
	$aStmt->store_result();
	if ($aStmt->num_rows == 0) {
		$nextQuery = false;
	}*/
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_REQUEST['prevPage'])) {
			$_SESSION['pageNum'] = $_SESSION['pageNum'] - 1;
		}
		if (isset($_REQUEST['nextPage'])) {
			$_SESSION['pageNum'] = $_SESSION['pageNum'] + 1;
		}
	}
	
	$pN = $_SESSION['pageNum'];
	$num = 1 + (($pN-1)*5);
	
	function createAlbumQuery($conn, $aID) {
		$sql = 'SELECT albumName, albumArtist, albumArt, avgScore FROM albums WHERE albumID = ?';
		if ($stmt = $conn->prepare($sql)) {
				$stmt->bind_param('i', $aID);
				if ($stmt->execute()) {
					$stmt->store_result();
					if ($stmt->affected_rows > 0) {//used to be num_rows == 1
						$_SESSION['nQ'] = true;
						$stmt->bind_result($albumName, $albumArtist, $albumArt, $avgScore);
						$stmt->fetch();
						echo '<button class="albumQuery hItem" type="submit" name="albButton" value="'.$aID.'" style="margin: 25px 150px;">
							<img src="albArt/'.$albumArt.'">
							<h1>'.$albumName.'</h1>
							<p>Artist: '.$albumArtist.'<br>MM Score: '.$avgScore.'</p>
						</button>';
					} else {
						$_SESSION['nQ'] = false;
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
			while ($nextQuery && $num <= ($pN*5)) {
				createAlbumQuery($mysql_db, $num);
				$nextQuery = $_SESSION['nQ'];
				$num = $num + 1;
			}
			$mysql_db->close();
			//echo '<h1>'.$nextQuery.'</h1>';
			if ($pN > 1) {
				echo '<button name="prevPage" class="hubButtons hItem" type="submit">Previous Page</button>';
			}
			echo '<input type="text" name="typePN" size="1" style="margin: 20px;" value="'.$pN.'">';
			if ($nextQuery) {
				echo '<button name="nextPage" class="hubButtons hItem" type="submit">Next Page</button>';
			}
		?>
	</form>
</body>
</html>