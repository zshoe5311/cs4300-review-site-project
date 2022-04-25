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
		
		$input_err = $input = "";
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {	
			$input = trim($_POST['fID']);
			if (!isset($input) || !((string)(int)$input == $input) || (string)(int)$input <= 0) {
					$input_err = "Please enter a positive integer ID number from above.";
			} else {
				$input = (int)trim($_POST['fID']);
				$sql = 'DELETE FROM `albums` WHERE albumID = ?';
				
				if ($stmt = $mysql_db->prepare($sql)) {
					$stmt->bind_param('i', $input);
					$stmt->execute();
					$stmt->store_result();
					if ($stmt->affected_rows > 0) {
						$myfile = fopen("aData.txt", "r") or die("Unable to open aData!");
						$delContent = fgets($myfile);
						while (trim($delContent) != $input) {
							for ($i = 0; $i < 6; $i++) {
								$delContent = fgets($myfile);
							}
						}
						for ($i = 0; $i < 5; $i++) {
							$delContent = $delContent./*"\n".*/fgets($myfile);
						}
						//$delContent = $delContent."\n";
						
						$contents = file_get_contents("aData.txt");
						$contents = str_replace($delContent, '', $contents);
						file_put_contents("aData.txt", $contents);
						$input_err = "Album Deleted Successfully!";
						fclose($myfile);
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
		$result = $mysql_db->query("SELECT albumName, albumArtist, albumID FROM albums ORDER BY `albums`.`albumID` ASC");
		
		echo "<div class=\"genSection hItem\"> <table border='1'>
		<tr>
		<th>albumName</th>
		<th>albumArtist</th>
		<th>albID</th>
		</tr>";

		while($row = $result->fetch_assoc())
		{
			echo "<tr>";
			echo "<td>" . $row['albumName'] . "</td>";
			echo "<td>" . $row['albumArtist'] . "</td>";
			echo "<td>" . $row['albumID'] . "</td>";
			echo "</tr>";
		}
		echo "</table></div>";
		
		$mysql_db->close();
	?>
	<div class="genSection hItem">	
		<h1>Type the ID of the album you want to delete:</h1>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			ID: <input type="text" name="fID">
			<input type="submit">
		</form>
	</div>
	<div class="genSection hItem">
		<p><?php echo $input_err; ?></p>
	</div>
</body>
</html>