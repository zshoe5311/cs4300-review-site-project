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
	
	if (isset($_GET['uNamePage'])) {
		$_SESSION['uNamePage'] = $_GET['uNamePage'];
	}
	$albumName = $albumArtist = $albumArt = '';
	//$sql = 'SELECT albumArt FROM albums WHERE albumID = ?';
	$authorUser = $_SESSION['uNamePage'];
	/*if ($stmt = $mysql_db->prepare($sql)) {
			$stmt->bind_param('i', $aID);
			if ($stmt->execute()) {
				$stmt->store_result();
				if ($stmt->num_rows == 1) {
					$stmt->bind_result($albumName, $albumArtist, $albumArt, $avgScore);
					$stmt->fetch();
				}
			}
	}*/
	$rQuery = "SELECT `reviewID`, `albumID`, `reviewDescript`, `reviewScore`, `authorUsername`, `postingDate` FROM `reviews` WHERE authorUsername = '".$authorUser."'";
	$rStmt = $mysql_db->prepare($rQuery);
	$rStmt->execute();
	$rStmt->store_result();
	$rStmt->bind_result($rID, $aID, $rDesc, $rScore, $rAuthor, $rPostDate);
	
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
  <title><?php echo $authorUser ?>'s Account Page - MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<!--<div class="albumDescript hItem">
		<img class="albumArt" src="albArt/<?php echo $albumArt; ?>">
		<div class="aDText">
			<h1><?php //echo $albumName; ?></h1>
			<h2>Artist: <?php //echo $albumArtist; ?>&emsp;&emsp;<font size="+3">MM Score: <?php //echo $avgScore; ?>/10</font></h2>
			<p><?php 
				/*$dFile = fopen($albumDescription, "r") or die("Unable to load description!");
				echo fread($dFile, filesize($albumDescription));
				fclose($dFile);*/
				?></p>
		</div>
	</div>--> 
	<?php
		$statQ = "SELECT `reviewScore`, `albumArtist` FROM `reviews` INNER JOIN albums ON reviews.albumID = albums.albumID WHERE authorUsername = '".$authorUser."' ORDER BY albums.albumArtist DESC";
		$statM = $mysql_db->prepare($statQ);
		$statM->execute();
		$statM->store_result();
		$statM->bind_result($rSC, $alA);
		$lastArtist = '';
		$numRevs = $albs5 = $diffArts = 0;
		while ($statM->fetch()) {
			if ($lastArtist != $alA) {
				$diffArts = $diffArts + 1;
				$lastArtist = $alA;
			}
			if ($rSC > 5) {
				$albs5 = $albs5 + 1;
			}
			$numRevs = $numRevs + 1;
		}
	?>
	<div class="genSection hItem">
		<?php
			if ($authorUser == $_SESSION['username']) {
				echo '<h1>WELCOME to your account page, '.$authorUser.'!</h1>
				<h1 style="float: left; margin-left: 20px;">So far you have:</h1>
				<h1 style="float: left; margin-left: 0px; margin-top: 80px;">Written '.$numRevs.' different reviews...</h1>
				<h1 style="float: right; margin-right: 50px; margin-top: 80px;">Reviewed albums from '.$diffArts.' different Artists...</h1>
				<h1 style="margin-left: 0px; margin-top: 210px;">And given '.$albs5.' albums a score above a 5!</h1>
				<p style="font-size: 28px;"><br>Below you will find all of the reviews you have written. You may choose to edit or delete them as you like.</p>
				</div>';
				if ($_SESSION['isAdmin'] == 1) {
					echo '<div class="genSection hItem">	
					<h1>You are also an admin, '.$authorUser.'! Below are some admin actions you can take:</h1>
					<a class="hubButtons" href="addAlbum.php"> Add Album</a>
					<a class="hubButtons" href="deleteAlbum.php"> Delete Album</a>
					</div>';
				}
				echo '<div class="homeLetter hItem" style="margin:20px 500px;"> 
					<h1>Your Reviews</h1>
				</div>';
			} else {
				echo '<h1>Welcome to '.$authorUser.'\'s account page!<h1 style="float: left; margin-left: 20px;">So far they have:</h1>
				<h1 style="float: left; margin-left: 0px; margin-top: 80px;">Written '.$numRevs.' different reviews...</h1>
				<h1 style="float: right; margin-right: 50px; margin-top: 80px;">Reviewed albums from '.$diffArts.' different Artists...</h1>
				<h1 style="margin-left: 0px; margin-top: 210px;">And given '.$albs5.' albums a score above a 5!</h1>
				<p style="font-size: 28px;"><br>Below you will find all of the reviews they have written.</p>
				</div>
				<div class="homeLetter hItem" style="margin:20px 500px;"> 
					<h1>'.$authorUser.'\'s Reviews</h1>
				</div>';
			}
		?>
	</div>
	<?php	
		while ($rStmt->fetch()) {
			$rFile = fopen($rDesc, "r") or die("Unable to load review text!");
			$albumName = $albumArtist = '';
			
			$sql = 'SELECT albumName, albumArtist FROM albums WHERE albumID = ?';
			$authorUser = $_SESSION['username'];
			if ($stmt = $mysql_db->prepare($sql)) {
					$stmt->bind_param('i', $aID);
					if ($stmt->execute()) {
						$stmt->store_result();
						if ($stmt->num_rows == 1) {
							$stmt->bind_result($albumName, $albumArtist);
							$stmt->fetch();
						}
					}
			}
			echo '<h2 style="float: left; margin-left: 140px;">Review of '.$albumName.' by '.$albumArtist.':</h2>';
			if (filesize($rDesc) > 0) {
				echo '<div class="reviewPost hItem" style="margin-bottom: 20px;">
					<div class="postText">	
						<h2 id="postUser"><a href="accountPage.php?uNamePage='.$rAuthor.'"><font size="+2">'.$rAuthor.'</font></a>&emsp;&emsp;Score: '.$rScore.'/10&emsp;&emsp;'.$rPostDate.'</h2>
						<p>'.fread($rFile, filesize($rDesc)).'</p>
				</div>';
			} else {
				echo '<div class="reviewPost hItem" style="margin-bottom: 20px;">
					<div class="postText">	
						<h2 id="postUser"><a href="accountPage.php?uNamePage='.$rAuthor.'"><font size="+2">'.$rAuthor.'</font></a>&emsp;&emsp;Score: '.$rScore.'/10&emsp;&emsp;'.$rPostDate.'</h2>
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