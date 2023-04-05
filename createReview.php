<?php
	session_start();
	
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){ //if the user is not already logged in, this code redirects the user to the login page, and sets them to be redirected to the createReview page upon logging in
		$_SESSION['destinationPage'] = "createReview";
		header("location: login.php");//real
	} else {
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "reviewsitedata";
		
		require_once "config/config.php";//config regenerates missing parts of database if needed
		$con->close();
		$mysql_db->close(); //the above 3 lines are to make sure the database is here and complete
		
		$mysql_db = new mysqli($servername, $username, $password, $dbname);//Creates connection
		// Check connection
		if ($mysql_db->connect_error) {
		  die("Connection failed: " . $mysql_db->connect_error);
		}
		if (isset($_POST['revDescript'])) {//this if statement checks to see if there was already any text in the text area user input on the createReview page, and if there was, saves that text in the 'rD' Session variable
				$_SESSION['rD'] = $_POST['revDescript'];
		} else { 
			$_SESSION['inputErr'] = false;
		}
		$rDescript = '';
		if (isset($_SESSION['rD'])) {//restores the text that was in the createReview text area before the submission of the form/refresh of the page
			$rDescript = $_SESSION['rD'];
		}
		$input_err = "";
		if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['hSearch'])) {//the below code attempts to submit the form to create the review on the createReview page
			$rScore = trim($_POST['revScore']);
			if(empty(trim($_POST['revScore']))) { //these if and else if statements validate the input of the review score on the createReview page form. It does not allow blank submissions, or zeroes, negative numbers, or decimals
				$input_err = "Please enter your review score out of 10. No zeroes, negative numbers, or decimals, please.";
				$_SESSION['inputErr'] = true;
			} else if (!((string)(int)$rScore == $rScore) || (string)(int)$rScore <= 0 || (string)(int)$rScore > 10) {
					$input_err = "Please enter your review score out of 10. No zeroes, negative numbers, non-numbers, or decimals, please.";
					$_SESSION['inputErr'] = true;
			}
			if (empty($input_err)) { //if there is no input validation error, the below code will add the newly created review to the database
				$_SESSION['inputErr'] = false;
				$rID = 1;
				$filename = "reviews/".$rID.".txt";
				while (file_exists($filename)) { //looks for untaken review ID number for this new review, and subsequently makes it the filename for the review description file (i.e. 'reviewID.txt')
					$rID = $rID + 1;
					$filename = "reviews/".$rID.".txt";
				}
				$myfile = fopen($filename, "w"); //The below file code creates the review description file, and puts the review's text contents in said file
				fwrite($myfile, $rDescript);
				fclose($myfile);
				$rDescript = $filename;
				
				$aID = $_SESSION['albNum']; //Creates variables for the different attributes of the new review
				$rScore = trim($_POST['revScore']);
				$author = $_SESSION['username'];
				date_default_timezone_set('America/New_York');
				$createTime = date('Y-m-d H:i:s');
				
				$addQuery = "INSERT INTO `reviews`(`reviewID`, `albumID`, `reviewDescript`, `reviewScore`, `authorUsername`, `postingDate`) VALUES (?,?,?,?,?,?)"; //This query will add the review to the database, with its attributes
				if ($stmt = $mysql_db->prepare($addQuery)) {
					$stmt->bind_param("iisiss", $rID, $aID, $rDescript, $rScore, $author, $createTime); 
					if ($stmt->execute()) { //once the addQuery is executed and the new review is added to the database, the code immediately below adds the new review entry to the rData file, for database
						$dFile = fopen("rData.txt", "a") or die("Unable to open rData!"); //regenerative purposes
						fwrite($dFile, $rID."\n");
						fwrite($dFile, $aID."\n");
						fwrite($dFile, $rDescript."\n");
						fwrite($dFile, $rScore."\n");
						fwrite($dFile, $author."\n");
						fwrite($dFile, $createTime."\n");
						fclose($dFile);
						
						$scoreQ = "SELECT AVG(reviewScore) FROM `reviews` WHERE albumID = ".$aID; //this code blob updates the average score of the reviewed album with the score of the new review in the database
						$scSt = $mysql_db->prepare($scoreQ);
						$scSt->execute();
						$scSt->bind_result($avgScore);
						$scSt->store_result();
						$scSt->fetch();
						$avgScore = number_format($avgScore, 1);
						$aQ = "UPDATE `albums` SET `avgScore`= ".$avgScore." WHERE `albumID` = ".$aID;
						$aStmt = $mysql_db->prepare($aQ);
						$aStmt->execute();
						
						$aDFile = fopen("aData.txt", "r") or die("Unable to open aData!"); //this code blob updates the average score of the reviewed album in the aData text file for database regenerative purposes
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
						header('location: albumProto.php');
					}
				}
			}				
		}
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
	<?php include 'hdr.php'; 
		//This php code blurb is used to get the name of the album being reviewed, to display on the create review page
		$nq = "SELECT albumName FROM `albums` WHERE albumID = ".$_SESSION['albNum'];
		$nst = $mysql_db->prepare($nq);
		$nst->execute();
		$nst->bind_result($aName);
		$nst->store_result();
		$nst->fetch();
	?>
	<div class="homeLetter hItem slide-in-fwd-center">	
		<h1>You are writing a review for <?php echo $aName ?>!</h1>
		<p>Please note the word limit, and remember to give it a score out of 10, but otherwise express
		your thoughts freely on this album. Also note that the written portion is actually optional, only a score is required for each review.</p>
	</div>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>"> <!--This is the HTML form for the createReview page and process-->
		<div class="createRText hItem" style="margin: 40px 310px 40px 100px;">
			<textarea rows="10" cols="100" name="revDescript" placeholder="Enter review here..."><?php echo $rDescript ?></textarea>
		</div>
		<div class="hItem scoreInput slide-in-fwd-center">
			<h2>Enter your score out of 10 below! (No negatives)</h2>
			<input type="text" name="revScore">
			<input type="submit">
		</div>
		<?php 
			if ($_SESSION['inputErr']) {// this php code displays an error blob if there is a input validation error during submission of the createReview form
				echo '<div class="genSection hItem slide-in-fwd-center" style="width: 50%; margin: 20px 200px; background-color: rgba(255,0,0,.7)">
					<p>'.$input_err.'</p>
				</div>';
			}
		?>
	</form>
</body>
</html>