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
	$lQuery = "";
	$queries = array();
	$lQI = 0;
	if ($_SESSION['listType'] == 2) {
		$queries = array(1,3,5,7);
		$lQI = 4;
	} else {
		if ($_SESSION['listType'] == 1) {
			$lQuery = $_SESSION['sQuery'];
		} else if ($_SESSION['listType'] == 3) {
			$lQuery = "SELECT albumID FROM albums ORDER BY avgScore DESC LIMIT 10";
		}
		$lst = $mysql_db->prepare($lQuery);
		$lst->execute(); 
		$lst->store_result();
		$lst->bind_result($qID);
		$lQI = 0;
		while ($lst->fetch()) {
			$queries[$lQI] = $qID;
			$lQI = $lQI + 1;
		}
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_REQUEST['prevPage'])) {
			$_SESSION['pageNum'] = $_SESSION['pageNum'] - 1;
		} else if (isset($_REQUEST['nextPage'])) {
			$_SESSION['pageNum'] = $_SESSION['pageNum'] + 1;
		} 
	}
	
	$pN = $_SESSION['pageNum'];
	$num = ($pN-1)*5;
	
	function createAlbumQuery($conn, $aID, $arr) {
		$sql = 'SELECT albumName, albumArtist, albumArt, avgScore FROM albums WHERE albumID = ?';
		if ($stmt = $conn->prepare($sql)) {
				$stmt->bind_param('i', $arr[$aID]);
				if ($stmt->execute()) {
					$stmt->store_result();
					if ($stmt->affected_rows > 0) {//used to be num_rows == 1
						$_SESSION['nQ'] = true;
						$stmt->bind_result($albumName, $albumArtist, $albumArt, $avgScore);
						$stmt->fetch();
						if ($arr[$aID] > 0) { //temporary fix to not display fake albums
							echo '<button class="albumQuery hItem" type="submit" name="albButton" value="'.$arr[$aID].'" style="margin: 25px 150px;">
								<img src="albArt/'.$albumArt.'">
								<h1>'.$albumName.'</h1>
								<p>Artist: '.$albumArtist.'<br>MM Score: '.$avgScore.'</p>
							</button>';
						} else {
							echo '<p>teehee</p>';
						}
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
		<?php
			if ($_SESSION['listType'] == 1) {
				echo '<h1>Search results for: "'.$_SESSION['sText'].'"</h1>';
			} else if ($_SESSION['listType'] == 3){
				echo '<h1>Top 10 Albums</h1>';
			} else if ($_SESSION['listType'] == 2) {
				echo '<h1>Editor\'s Choice</h1>';
			}
		?>
	</div>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<?php
			if ($_SESSION['listType'] == 3) {
				$pLim = 10;
			} else {
				$pLim = $pN*5;
			}				
			while ($nextQuery && $num < $pLim) {
				createAlbumQuery($mysql_db, $num, $queries);
				$nextQuery = $_SESSION['nQ'];
				$num = $num + 1;
				if ($num > $pLim) {
					$nextQuery = false;
				}
			}
			if ($num <= 1) {
				echo '<div class="genSection hItem" style="margin: 0px 420px;">
					<h2>No results found. Please try a different search query.</h2>
				</div>';
			}
			$mysql_db->close();
			//echo '<h1>'.$nextQuery.'</h1>';
			//if (!($_SESSION['listType'] == 3)) {
				if ($pN > 1) {
					echo '<button name="prevPage" class="hubButtons hItem" type="submit">Previous Page</button>';
				}
				echo '<input type="text" name="typePN" size="1" style="margin: 20px;" value="'.$pN.'">';
				if ($nextQuery) {
					echo '<button name="nextPage" class="hubButtons hItem" type="submit">Next Page</button>';
				}
			//}
		?>
	</form>
</body>
</html>