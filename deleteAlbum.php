<?php
	session_start();
	
	if ($_SESSION['isAdmin'] == 0) { //if the user is not an admin, this code redirects them to the home page
		header('location: index.php');//real
	} else {	
		require_once "config/config.php";//runs through the config code in case the database needs to be regenerated
		$con->close();
		$mysql_db->close(); //the above 3 lines are to make sure the database is here and complete
		
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "reviewsitedata";
		
		$mysql_db = new mysqli($servername, $username, $password, $dbname);//Creates the database connection
		// Check connection
		if ($mysql_db->connect_error) {
		  die("Connection failed: " . $mysql_db->connect_error);
		}
		
		$input_err = $input = "";
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {	
			$input = trim($_POST['fID']);
			if (!isset($input) || !((string)(int)$input == $input) || (string)(int)$input <= 0) { //this if statement detects if there is an input validation error
					$input_err = "Please enter a positive integer ID number from above.";
			} else {
				$input = (int)trim($_POST['fID']);
				
				$sql = 'DELETE FROM `albums` WHERE albumID = ?';
				if ($stmt = $mysql_db->prepare($sql)) {//the code below until the next if statement deletes the album from the database
					$stmt->bind_param('i', $input);
					$stmt->execute();
					$stmt->store_result();
					if ($stmt->affected_rows > 0) {
						$myfile = fopen("aData.txt", "r") or die("Unable to open aData!");// the while and the for loop below locate and consolidate the deleted albums entry in aData.txt, preparing for it to be removed,
						$delContent = fgets($myfile);// so it will no long regenerate with the rest of the database
						while (trim($delContent) != $input) {
							for ($i = 0; $i < 6; $i++) {
								$delContent = fgets($myfile);
							}
						}
						for ($i = 0; $i < 5; $i++) {
							if ($i == 1) {
								$albDescript = fgets($myfile); //grabs the name of the album description file, so it can be deleted
								$delContent = $delContent.$albDescript;
							} else {
								$delContent = $delContent./*"\n".*/fgets($myfile);
							}
						} 
						
						$contents = file_get_contents("aData.txt");// this code blob removes the deleted album's entry from the aData.txt file, and deletes the file containig the album's description
						$contents = str_replace($delContent, '', $contents);
						file_put_contents("aData.txt", $contents);
						fclose($myfile);
						unlink(trim($albDescript));
						
						$rQuery = "SELECT reviewID FROM reviews WHERE albumID = ".$input; //this code blob finds all of the reviews attached to the deleted album and puts them in the queries array
						$rst = $mysql_db->prepare($rQuery);
						$rst->execute(); 
						$rst->store_result();
						$rst->bind_result($rID);
						$rQI = 0;
						while ($rst->fetch()) {
							$queries[$rQI] = $rID;
							$rQI = $rQI + 1;
							//echo '<p>!</p>';
						}
						
						for ($j = 0; $j < $rQI; $j++) {	//this for-loop deletes every review from the deleted album from the database, removes their entries from rData.txt, and deletes the files containing their review text
							$dRID = $queries[$j];
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
								
								$input_succ = "Album Deleted Successfully!";
							}
						}
						
					} else {
						$input_err = "Please enter a positive integer ID number from above.";
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
	<title>Delete Album - MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<?php
		/* all of the code in this php segment creates the table of all the albums currently in the database, displaying their name, artist, and ID, so the user can see which albums they can delete*/
		$result = $mysql_db->query("SELECT albumName, albumArtist, albumID FROM albums ORDER BY `albums`.`albumID` ASC");
		
		echo '<div class="genSection hItem" style="width: 30%"> <div style="margin-left: 15%;"> <table border="1">
		<tr>
		<th>albumName</th>
		<th>albumArtist</th>
		<th>albID</th>
		</tr>';

		while($row = $result->fetch_assoc())
		{
			echo "<tr>";
			echo "<td>" . $row['albumName'] . "</td>";
			echo "<td>" . $row['albumArtist'] . "</td>";
			echo "<td>" . $row['albumID'] . "</td>";
			echo "</tr>";
		}
		echo "</table></div></div>";
		
		$mysql_db->close();
	?>
	<div class="genSection hItem">	
		<h1>Type the ID of the album you want to delete:</h1>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>"> <!--form for the deletion of an album from the chart-->
			ID: <input type="text" name="fID">
			<input type="submit">
		</form>
	</div>
	<?php
		if (!empty($input_err)) { //these if and else if statements display either error or deletion success messages
			echo	'<div class="genSection hItem" style="background-color: rgba(255,0,0,.7)">
				<p>'.$input_err.'</p>
			</div>';
		} else if (!empty($input_succ)) {
			echo	'<div class="genSection hItem" style="background-color: rgba(128,0,255,.7);">
				<p>'.$input_succ.'</p>
			</div>';
		}
	?>
</body>
</html>