<?php
	session_start();
	
	if ($_SESSION['isAdmin'] == 0) {
		header('location: home.php');
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
		
		$input_err = "";
		if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
			if(empty(trim($_POST['fAlbName']))){ //can be done more efficient, look at posted vars empty bookmark
				$input_err = 'Please enter missing data.';
			} else{
				$albName = trim($_POST['fAlbName']);
			}
			if(empty(trim($_POST['fAlbArtist']))){
				$input_err = 'Please enter missing data.';
			} else{
				$albArtist = trim($_POST['fAlbArtist']);
			}
			if(empty(trim($_POST['fAlbDescript']))){
				$input_err = 'Please enter missing data.';
			} else{
				$albDescript = trim($_POST['fAlbDescript']);
			}
			if(empty(trim($_POST['fAlbArt']))){
				$input_err = 'Please enter missing data.';
			} else{
				$albArt = trim($_POST['fAlbArt']);
			}
			/*if(empty(trim($_POST['fAvgScore']))){
				$input_err = 'Please enter data.';
			} else{
				$avgScore = trim($_POST['fAvgScore']);
			}
			*/
			
			if (empty($input_err)) {
				$fileName = "albDescriptions/".str_replace(' ','',$albName).".txt";
				$myfile = fopen($fileName, "w");
				fwrite($myfile, $albDescript);
				fclose($myfile);
				$albDescript = $fileName;
				
				$addQuery = "INSERT INTO `albums`(`albumName`, `albumArtist`, `albumDescription`, `albumArt`, `avgScore`, `albumID`) VALUES (?,?,?,?,0,?)";
				$loopQuery = "SELECT COUNT(albumID) FROM albums WHERE albumID = ?";
				$albID = 0;
				$sum = 1;
				
				while ($sum != 0) {
					$albID = $albID + 1;
					if ($stmt = $mysql_db->prepare($loopQuery)) {
						$stmt->bind_param('i', $albID);
						if ($stmt->execute()) {
							$stmt->store_result();
							if ($stmt->num_rows == 1) {
								$stmt->bind_result($sum);
								$stmt->fetch();
							}
						}
					}
				}
				
				if ($stmt = $mysql_db->prepare($addQuery)) {
					$stmt->bind_param("ssssi", $albName, $albArtist, $albDescript, $albArt, $albID);
					if ($stmt->execute()) {
							$_SESSION['albNum'] = $albID;
							$myfile = fopen("aData.txt", "a") or die("Unable to open aData!");
							fwrite($myfile, $albID."\n");
							fwrite($myfile, $albArtist."\n");
							fwrite($myfile, $albDescript."\n");
							fwrite($myfile, $albArt."\n");
							fwrite($myfile, "0\n");
							fwrite($myfile, $albName."\n");
							fclose($myfile);
							header('location: albumProto.php');
					}
				}
			}
		}
		
		$mysql_db->close();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="newStyle.css">
	<title>Add Album - MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<div class="genSection hItem">	
		<h1>Add album:</h1>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			albumName: <input type="text" name="fAlbName">
			albumArtist: <input type="text" name="fAlbArtist">
			albumDescription: <input type="text" name="fAlbDescript">
			albumArt: <input type="text" name="fAlbArt">
			<!--avgScore: <input type="text" name="fAvgScore">-->
			<input type="submit">
		</form>
	</div>
	<div class="genSection hItem">
		<p><?php echo $input_err; ?></p>
	</div>
</body>
</html>