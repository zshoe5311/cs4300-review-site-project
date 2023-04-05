<?php
	session_start();
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "reviewsitedata";
	
	require_once "config/config.php";//runs config to regenerate any missing parts of the database
	$con->close();
	$mysql_db->close(); //the above 3 lines are to make sure the database is here and complete
	
	$mysql_db = new mysqli($servername, $username, $password, $dbname); //create connection
	// Check connection
	if ($mysql_db->connect_error) {
	  die("Connection failed: " . $mysql_db->connect_error);
	}
	
	$albumName = $albumArtist = $albumDescription = $albumArt = '';
	$sql = 'SELECT albumName, albumArtist, albumDescription, albumArt, avgScore FROM albums WHERE albumID = ?';
	$aID = $_SESSION['albNum'];
	
	if ($stmt = $mysql_db->prepare($sql)) { //Gathers and stores the page's album's data in variables to be used later
			$stmt->bind_param('i', $aID);
			if ($stmt->execute()) {
				$stmt->store_result();
				if ($stmt->num_rows == 1) {
					$stmt->bind_result($albumName, $albumArtist, $albumDescription, $albumArt, $avgScore);
					$stmt->fetch();
				}
			}
	}
	
	$rQuery = "SELECT `reviewID`, `reviewDescript`, `reviewScore`, `authorUsername`, `postingDate` FROM `reviews` WHERE albumID = ".$aID; //provides query and binds results to specific variables to be used to generate
	$rStmt = $mysql_db->prepare($rQuery); //the album's reviews below the album description sectionS
	$rStmt->execute();
	$rStmt->store_result();
	$rStmt->bind_result($rID, $rDesc, $rScore, $rAuthor, $rPostDate);
	
	if (isset($_REQUEST['cRB'])) { //if the create review button is clicked, it takes the user to the create review page for this album, with slight differences based on if they are already logged in or not
		if ($_SESSION['loggedin'] == true) {
			$_SESSION['rD'] = "";
			$mysql_db->close();
			header('location: createReview.php');
		} else {
			$_SESSION['destinationPage'] = "createReview";
			header('location: login.php');//real
		}
	}
	
	if (isset($_REQUEST['revROD'])) {//code below is executed if the delete review button is pressed on any of the reviews
		$dRID = $_REQUEST['revROD']; //the code before the if statement deletes the review from the database
		$dQuery = "DELETE FROM `reviews` WHERE reviewID = ".$dRID;
		$dSTMT = $mysql_db->prepare($dQuery);
		$dSTMT->execute();
		$dSTMT->store_result();
		if ($dSTMT->affected_rows > 0) {
			$myfile = fopen("rData.txt", "r") or die("Unable to open rData!");//up until the unlink function, the below code deletes the review's entry in the rData.txt file and deletes the review description's file
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
			
			$scoreQ = "SELECT AVG(reviewScore) FROM `reviews` WHERE albumID = ".$aID; //the rest of the code within the parent if statement from here updates the album's average score after the review has been deleted,
			$scSt = $mysql_db->prepare($scoreQ); // by updating it in the database and in the aData.txt file
			$scSt->execute();
			$scSt->bind_result($avgScore);
			$scSt->store_result();
			$scSt->fetch();
			$avgScore = number_format($avgScore, 1);
			
			$aQ = "UPDATE `albums` SET `avgScore`= ".$avgScore." WHERE `albumID` = ".$aID;
			$aStmt = $mysql_db->prepare($aQ);
			$aStmt->execute();
			
			$aDFile = fopen("aData.txt", "r") or die("Unable to open aData!"); 
			$delContent = fgets($aDFile);
			while (trim($delContent) != $aID) {
				for ($i = 0; $i < 6; $i++) {
					$delContent = fgets($aDFile);
				}

			}
			for ($i = 0; $i < 3; $i++) {
				$delContent = $delContent.fgets($aDFile);
			}
			$newContent = $delContent.$avgScore."\n";
			$delContent = $delContent.fgets($aDFile);
			$contents = file_get_contents("aData.txt");
			$contents = str_replace($delContent, $newContent, $contents);
			file_put_contents("aData.txt", $contents);
			fclose($aDFile);
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
	<div class="albumDescript hItem flip-in-hor-top"> <!--the code within this div creates the Album description with the album name, artist, art, score, and the actual description text itself-->
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
		while ($rStmt->fetch()) { //this while loop generates and displays the review list for the album on this page
			$rFile = fopen($rDesc, "r") or die("Unable to load review text!");
			if (filesize($rDesc) > 0) { 
				echo '<div class="reviewPost hItem">
					<div class="postText">	
						<h2 id="postUser"><a href="accountPage.php?uNamePage='.$rAuthor.'"><font size="+2">'.$rAuthor.'</font></a>&emsp;&emsp;Score: '.$rScore.'/10&emsp;&emsp;'.$rPostDate.'</h2>
						<p>'.fread($rFile, filesize($rDesc)).'</p>
				</div>';
			} else {
				echo '<div class="reviewPost hItem">
					<div class="postText">	
						<h2 id="postUser"><a href="accountPage.php?uNamePage='.$rAuthor.'"><font size="+2">'.$rAuthor.'</font></a>&emsp;&emsp;Score: '.$rScore.'/10&emsp;&emsp;'.$rPostDate.'</h2>
				</div>';
			}
			if ($_SESSION['loggedin'] == true) {
				if (trim($_SESSION['username']) == trim($rAuthor) || $_SESSION['isAdmin'] == 1) {	
					echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">
							<button name="revROD" value="'.$rID.'" class="hubButtons hItem" type="submit" style="float: right; margin-right: 50px;">Delete this review?</button>
						</form>';
				}
			}
			echo '</div>';
			fclose($rFile);
		}
		$mysql_db->close();
	?>
</body>
</html>
