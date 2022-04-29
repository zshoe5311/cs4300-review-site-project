<?php
	session_start();
	
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
		header("location: index.php");
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
		if (isset($_POST['revDescript'])) {
				$_SESSION['rD'] = $_POST['revDescript'];
		} else {
			$_SESSION['inputErr'] = false;
		}
		$rDescript = $_SESSION["rD"];
		$input_err = "";
		if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['hSearch'])) {
			$rScore = trim($_POST['revScore']);
			if(empty(trim($_POST['revScore']))) {
				$input_err = "Please enter your review score out of 10. No zeroes, negative numbers, or decimals, please.";
				$_SESSION['inputErr'] = true;
			} else if (!((string)(int)$rScore == $rScore) || (string)(int)$rScore <= 0 || (string)(int)$rScore > 10) {
					$input_err = "Please enter your review score out of 10. No zeroes, negative numbers, non-numbers, or decimals, please.";
					$_SESSION['inputErr'] = true;
			}
			if (empty($input_err)) {
				$_SESSION['inputErr'] = false;
				$rID = 1;
				$filename = "reviews/".$rID.".txt";
				while (file_exists($filename)) {
					$rID = $rID + 1;
					$filename = "reviews/".$rID.".txt";
				}
				$myfile = fopen($filename, "w");
				fwrite($myfile, $rDescript);
				fclose($myfile);
				$rDescript = $filename;
				
				$aID = $_SESSION['albNum'];
				$rScore = trim($_POST['revScore']);
				$author = $_SESSION['username'];
				date_default_timezone_set('America/New_York');
				$createTime = date('Y-m-d H:i:s');
				
				$addQuery = "INSERT INTO `reviews`(`reviewID`, `albumID`, `reviewDescript`, `reviewScore`, `authorUsername`, `postingDate`) VALUES (?,?,?,?,?,?)";
				if ($stmt = $mysql_db->prepare($addQuery)) {
					$stmt->bind_param("iisiss", $rID, $aID, $rDescript, $rScore, $author, $createTime);
					if ($stmt->execute()) {
						$dFile = fopen("rData.txt", "a") or die("Unable to open rData!");
						fwrite($dFile, $rID."\n");
						fwrite($dFile, $aID."\n");
						fwrite($dFile, $rDescript."\n");
						fwrite($dFile, $rScore."\n");
						fwrite($dFile, $author."\n");
						fwrite($dFile, $createTime."\n");
						fclose($dFile);
						
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
						
						header('location: albumProto.php');
					}
				}
			}				
		}
		//make sure that when an error is encountered for the score, the progress in the review text blob is saved
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
		your thoughts freely on this album. Also yes, we will fix the css on this page <i>eventually</i>.</p>
	</div>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<div class="createRText hItem">
			<textarea rows="10" cols="100" name="revDescript" placeholder="Enter review here..."><?php echo $rDescript ?></textarea>
		</div>
		<div class="hItem scoreInput">
			<h2>Enter your score out of 10 below! (No negatives)</h2>
			<input type="text" name="revScore">
			<input type="submit">
		</div>
		<?php
			if ($_SESSION['inputErr']) {
				echo '<div class="genSection hItem">
					<p>'.$input_err.'</p>
				</div>';
			}
		?>
	</form>
</body>
</html>