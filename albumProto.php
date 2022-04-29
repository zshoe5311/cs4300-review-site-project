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
	$aID = $_SESSION['albNum'];
	if ($stmt = $mysql_db->prepare($sql)) {
			$stmt->bind_param('i', $aID);
			if ($stmt->execute()) {
				$stmt->store_result();
				if ($stmt->num_rows == 1) {
					$stmt->bind_result($albumName, $albumArtist, $albumDescription, $albumArt, $avgScore);
					$stmt->fetch();
				}
			}
	}
	
	/*$scoreQ = "SELECT AVG(reviewScore) FROM `reviews` WHERE albumID = ".$aID;
	$scSt = $mysql_db->prepare($scoreQ);
	$scSt->execute();
	$scSt->bind_result($avgScore);
	$scSt->store_result();
	$scSt->fetch();
	$avgScore = number_format($avgScore, 1);*/
	
	$rQuery = "SELECT `reviewID`, `reviewDescript`, `reviewScore`, `authorUsername`, `postingDate` FROM `reviews` WHERE albumID = ".$aID;
	$rStmt = $mysql_db->prepare($rQuery);
	$rStmt->execute();
	$rStmt->store_result();
	$rStmt->bind_result($rID, $rDesc, $rScore, $rAuthor, $rPostDate);
	
	if (isset($_REQUEST['cRB'])) {
		$_SESSION['rD'] = "";
		$mysql_db->close();
		header('location: createReview.php');
	}
	
	if (isset($_REQUEST['revROD'])) {
		$dRID = $_REQUEST['revROD'];
		$dQuery = "DELETE FROM `reviews` WHERE reviewID = ".$dRID;
		$dSTMT = $mysql_db->prepare($dQuery);
		$dSTMT->execute();
		$dSTMT->store_result();
		if ($dSTMT->affected_rows > 0) {
			$myfile = fopen("rData.txt", "r") or die("Unable to open rData!");
			$delContent = fgets($myfile);
			while (trim($delContent) != $dRID) {
				for ($i = 0; $i < 6; $i++) {
					$delContent = fgets($myfile);
				}
			}
			for ($i = 0; $i < 5; $i++) {
				$delContent = $delContent.fgets($myfile);
			}
			
			$contents = file_get_contents("rData.txt");
			$contents = str_replace($delContent, '', $contents);
			file_put_contents("rData.txt", $contents);
			fclose($myfile);
			unlink('reviews/'.$dRID.'.txt');
			
			$scoreQ = "SELECT AVG(reviewScore) FROM `reviews` WHERE albumID = ".$aID;
			$scSt = $mysql_db->prepare($scoreQ);
			$scSt->execute();
			$scSt->bind_result($avgScore);
			$scSt->store_result();
			$scSt->fetch();
			$avgScore = number_format($avgScore, 1);
			
			$aQ = "UPDATE `albums` SET `avgScore`= ".$avgScore." WHERE `albumID` = ".$aID;
			$aStmt = $mysql_db->prepare($aQ);
			$aStmt->execute();
		}
		$mysql_db->close();
		header('location: albumProto.php');
	}
	//$mysql_db->close();
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
			<h2>Artist: <?php echo $albumArtist; ?>&emsp;&emsp;<font size="+3">MM Score: <?php echo $avgScore; ?>/10</font></h2>
			<p><?php 
				$dFile = fopen($albumDescription, "r") or die("Unable to load description!");
				echo fread($dFile, filesize($albumDescription));
				fclose($dFile);
				?></p>
		</div>
	</div>
	<div class="homeLetter hItem"> <!--style="margin-top: 50px; margin-right: 70px;"-->
		<h1>User Reviews</h1>
	</div>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<button name="cRB" class="hubButtons hItem" type="submit"><font size="+2">Create Review</font></button>
	</form>
	<?php	
		while ($rStmt->fetch()) {
			$rFile = fopen($rDesc, "r") or die("Unable to load review text!");
			if (filesize($rDesc) > 0) {
				echo '<div class="reviewPost hItem">
					<div class="postText">	
						<h2 id="postUser"><a href="home.php"><font size="+2">'.$rAuthor.'</font></a>&emsp;&emsp;Score: '.$rScore.'/10&emsp;&emsp;'.$rPostDate.'</h2>
						<p>'.fread($rFile, filesize($rDesc)).'</p>
				</div>';
			} else {
				echo '<div class="reviewPost hItem">
					<div class="postText">	
						<h2 id="postUser"><a href="home.php"><font size="+2">'.$rAuthor.'</font></a>&emsp;&emsp;Score: '.$rScore.'/10&emsp;&emsp;'.$rPostDate.'</h2>
				</div>';
			}
			if (trim($_SESSION['username']) == trim($rAuthor) || $_SESSION['isAdmin'] == 1) {	
				echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">
						<button name="revROD" value="'.$rID.'" class="hubButtons hItem" type="submit" style="float: right; margin-right: 50px;">Delete this review?</button>
					</form>';
			}
			echo '</div>';
			fclose($rFile);
		}
		$mysql_db->close();
	?>
</body>
</html>
