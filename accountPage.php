<?php
	session_start();
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "reviewsitedata";
	
	require_once "config/config.php"; //runs config file to make sure all the database is generated
	$con->close();
	$mysql_db->close(); //the above 3 lines are to make sure the database is here and complete
	
	$mysql_db = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($mysql_db->connect_error) {
	  die("Connection failed: " . $mysql_db->connect_error);
	}
	
	if (isset($_GET['uNamePage'])) {//creates a session variable with the user associated with the accountPage, if the page was arrived at with a certain user in mind
		$_SESSION['uNamePage'] = $_GET['uNamePage'];
	} else {
		$_SESSION['uNamePage'] = 'guest';
	}
	$albumName = $albumArtist = $albumArt = '';
	$authorUser = $_SESSION['uNamePage'];
	
	$rQuery = "SELECT `reviewID`, `albumID`, `reviewDescript`, `reviewScore`, `authorUsername`, `postingDate` FROM `reviews` WHERE authorUsername = '".$authorUser."'"; //this code blob binds the results of the query
	$rStmt = $mysql_db->prepare($rQuery); //returning all reviews made by the page's specified user to different variables, which will be used in a loop later on
	$rStmt->execute();
	$rStmt->store_result();
	$rStmt->bind_result($rID, $aID, $rDesc, $rScore, $rAuthor, $rPostDate);
	
	if (isset($_REQUEST['revROD'])) { //the following code deletes a review if its review button is clicked
		$dRID = trim($_REQUEST['revROD']);
		$raidST = $mysql_db->prepare("SELECT albumID FROM `reviews` WHERE reviewID = ".$dRID); //up until the if statement, the following code assigns the review-to-be-deleted's album's id to $rAID, and deletes the specified
		$raidST->execute();//review from the database
		$raidST->store_result();
		$raidST->bind_result($rAID);
		$dQuery = "DELETE FROM `reviews` WHERE reviewID = ".$dRID;
		$dSTMT = $mysql_db->prepare($dQuery);
		$dSTMT->execute();
		$dSTMT->store_result();
		if ($dSTMT->affected_rows > 0) {
			$dSTMT->close();
			$myfile = fopen("rData.txt", "r") or die("Unable to open rData!"); //up until the unlink function, the below code deletes the review's entry in the rData.txt file and deletes the review description's file
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
			
			$raidST->fetch();
			$scoreQ = "SELECT AVG(reviewScore) FROM `reviews` WHERE albumID = ".$rAID; //the rest of the code within the parent if statement from here updates the album's average score after the review has been deleted,
			$scSt = $mysql_db->prepare($scoreQ); // by updating it in the database and in the aData.txt file
			$scSt->execute();
			$scSt->bind_result($avgScore);
			$scSt->store_result();
			$scSt->fetch();
			$avgScore = number_format($avgScore, 1);
			
			$aQ = "UPDATE `albums` SET `avgScore`= ".$avgScore." WHERE `albumID` = ".$rAID;
			$aStmt = $mysql_db->prepare($aQ);
			$aStmt->execute();
			
			$aDFile = fopen("aData.txt", "r") or die("Unable to open aData!"); 
			$delContent = fgets($aDFile);
			while (trim($delContent) != $rAID) {
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
			$_SESSION['albNum'] = $rAID;
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
	
	<?php
		//the below code executes an inner joined query between the reviews and albums tables in the database, in order to display some fun stats about the user on their account page about their reviews
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
			//the code below creates the display of the top half of the account page, showing the stats, with slight differences between if it is your account page or not, and action buttons available if you're an admin
			if ($_SESSION['loggedin'] == true && $authorUser == $_SESSION['username']) {
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
		//the code below generates the name of the album each review was made for, and the review posts themselves, along with the option to delete them if you are the accountPageUser or an admin
		while ($rStmt->fetch()) {
			$rFile = fopen($rDesc, "r") or die("Unable to load review text!");
			$albumName = $albumArtist = '';
			
			$sql = 'SELECT albumName, albumArtist FROM albums WHERE albumID = ?';
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
			if ($_SESSION['loggedin'] == true && (trim($_SESSION['username']) == trim($rAuthor) || $_SESSION['isAdmin'] == 1)) {	
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