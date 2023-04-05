<?php
	session_start();
	
	if ($_SESSION['isAdmin'] == 0) { //this if statement redirects the user to the home page if they are not an admin
		header('location: index.php');//real
	} else {
		require_once "config/config.php";//runs config code to regenerate any missing parts of the database
		$con->close();
		$mysql_db->close(); //the above 3 lines are to make sure the database is here and complete
		
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "reviewsitedata";
		
		$mysql_db = new mysqli($servername, $username, $password, $dbname);//creates connection
		// Check connection
		if ($mysql_db->connect_error) {
		  die("Connection failed: " . $mysql_db->connect_error);
		}
		
		$input_err = "";//error message variable
		if ($_SERVER['REQUEST_METHOD'] === 'POST') { //if the addAlbum form is posted, the below code is executed to add an album to the database
			if(empty(trim($_POST['fAlbName']))){ //this if-else chain check if any of the form inputs are empty
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
			
			
			if (empty($input_err)) {//if none of the post input slots are empty, the below code adds the album to the database
				$fileName = "albDescriptions/".str_replace([' ', ':', '\\', '/', '*', '?', '\"', '<', '>', '|'],'',$albName).".txt"; //creates the file name of the file where the new album's description will be stored
				$myfile = fopen($fileName, "w");//this clump of code creates the description file, and adds the description to the file from the form's input
				fwrite($myfile, $albDescript);
				fclose($myfile);
				$albDescript = $fileName;
				
				$addQuery = "INSERT INTO `albums`(`albumName`, `albumArtist`, `albumDescription`, `albumArt`, `avgScore`, `albumID`) VALUES (?,?,?,?,0,?)"; //the add query is for actually adding the new album to 
				$loopQuery = "SELECT COUNT(albumID) FROM albums WHERE albumID = ?"; //the database, while the loop query will be used to find a unique numerical album ID for the new album 
				$albID = 0;
				$sum = 1;
				
				while ($sum != 0) { //this while loop finds the positive integer number not already taken as an album ID, and makes it the new album's ID number
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
				
				if ($stmt = $mysql_db->prepare($addQuery)) {//the code below executes the addQuery, adding the album to the database, and then saves the needed attributes of the album in the aData file, for 
					$stmt->bind_param("ssssi", $albName, $albArtist, $albDescript, $albArt, $albID);// database regenerating purposes
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
	<?php include 'hdr.php'; ?> <!-- includes hub bar -->
	<div class="genSection hItem" style="width: 60%;">	
		<h1>Add album:</h1>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>"> <!-- creates the HTML for the addAlbum form -->
			albumName: <input type="text" name="fAlbName">
			albumArtist: <input type="text" name="fAlbArtist">
			<div style="margin: 40px 0px 40px 0px;">
			<p>albumDescription:</p> <textarea rows="10" cols="100" name="fAlbDescript" style="resize: none;"></textarea>
			</div>
			albumArt: <input type="text" name="fAlbArt">
			<p>(Image must already be put in the 'albArt' folder, then the name of the image file + the file extension typed for the image to display properly)</p>
			<input type="submit">
		</form>
	</div>
	<?php 
	if (!empty($input_err)) { //displays an error sign if there is an error (i.e. there is any missing input)
		echo	'<div class="genSection hItem" style="width: 60%; background-color: rgba(255,0,0,.7)">
			<p>'.$input_err.'</p>
		</div>';
	} 
	?>
</body>
</html>