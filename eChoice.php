<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
<?php
	session_start();
			
	require_once "config/config.php"; //runs the config file in case the database needs to be regenerated
	$con->close();
	$mysql_db->close(); //the above 3 lines are to make sure the database is here and complete
			
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "reviewsitedata";
	
	$mysql_db = new mysqli($servername, $username, $password, $dbname);//Creates database connection
	// Check connection
	if ($mysql_db->connect_error) {
	  die("Connection failed: " . $mysql_db->connect_error);
	}

	$albumName = $albumArtist = $albumArt = '';
	$avgScore = 0;
	$nextQuery = true;
	$lQuery = "";
	$queries = array();
	$lQI = 0;
	if (isset($_SESSION['listType'])) {	
		if ($_SESSION['listType'] == 2) { //the below if statement sets the list type to 2 (if the list to be displayed is the editor's choice list), and sets the queries array to contain the proper album IDs
			$queries = array(1,3,5,7);
			$lQI = 4;
		} else { //the below else statement handles all other list types, by taking the custom query and using it to execute sql functions, and puts each album ID fitting in that query in the queries array
			if ($_SESSION['listType'] == 1 && isset($_SESSION['sQuery'])) {
				$lQuery = $_SESSION['sQuery'];
			} else if ($_SESSION['listType'] == 3) {
				$lQuery = "SELECT albumID FROM albums ORDER BY avgScore DESC LIMIT 10";
			}
			$lst = $mysql_db->prepare($lQuery);
			$lst->execute(); 
			$lst->store_result();
			$lst->bind_result($qID);
			$lQI = 0;
			while ($lst->fetch()) {
				$queries[$lQI] = $qID;
				$lQI = $lQI + 1;
			}
		}
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') { //detects if either the previous page or next page button was pressed, and takes you to either the previous or next page of album queries
		if (isset($_REQUEST['prevPage'])) {
			$_SESSION['pageNum'] = $_SESSION['pageNum'] - 1;
		} else if (isset($_REQUEST['nextPage'])) {
			$_SESSION['pageNum'] = $_SESSION['pageNum'] + 1;
		} 
	}
	
	$pN = $_SESSION['pageNum']; //establishes a page number variable ($pN) based on the current state of the page number session variable
	$num = ($pN-1)*5; //sets the starting number, representative of the first album query to be displayed in the list on the current page
	
	function createAlbumQuery($conn, $aID, $arr) {//function that generates an album query to be displayed in the list, given the parameters of the database connection, the albumID, and the queries array
		$sql = 'SELECT albumName, albumArtist, albumDescription, albumArt, avgScore FROM albums WHERE albumID = ?';
		if ($stmt = $conn->prepare($sql)) {
				$stmt->bind_param('i', $arr[$aID]);
				if ($stmt->execute()) {
					$stmt->store_result();
					if ($stmt->affected_rows > 0) {//used to be num_rows == 1
						$_SESSION['nQ'] = true; //sets the sessions variable for if there is a next query to true
						$stmt->bind_result($albumName, $albumArtist, $albumDescript, $albumArt, $avgScore);
						$stmt->fetch();
						if ($arr[$aID] > 0) { //if all else has succeeded witht the query, the code below generates the album Query for the list and its html code
							echo '<button class="albumQuery hItem flip-in-hor-top" type="submit" name="albButton" value="'.$arr[$aID].'" style="margin: 25px 150px;">
								<div class="aQImg">
									<img src="albArt/'.$albumArt.'">
								</div>
								<div class="abqHeader">	
									<h1>'.$albumName.'</h1>
									<h2>Artist: '.$albumArtist.'&emsp;&emsp;MM Score: '.$avgScore.'</h2>
								</div>
							</button>';
						} 
					} else {
						$_SESSION['nQ'] = false;
					}
				}
		}
	}
	
	if (isset($_REQUEST['albButton'])) { //if any of the album queries in the list is clicked, it will take you to the respective album page
		$_SESSION['albNum'] = $_REQUEST['albButton'];
		$mysql_db->close();
		header('location: albumProto.php');
	}
	
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="newStyle.css">
		<?php
			//this php code displays the proper html title for the page depending on the type of list
			if ($_SESSION['listType'] == 1) {
				echo '<title>Search Results</title>';
			} else if ($_SESSION['listType'] == 3){
				echo '<title>Top 10 Albums</title>';
			} else if ($_SESSION['listType'] == 2) {
				echo '<title>Editor\'s Choice</title>';
			}
		?>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<div class="homeLetter hItem">
		<?php 
			//this php code displays the proper text blob above the list queries depending on the type of list
			if ($_SESSION['listType'] == 1) {
				echo '<h1>Search results for: "'.$_SESSION['sText'].'"</h1>';
			} else if ($_SESSION['listType'] == 3){
				echo '<h1>Top 10 Albums</h1>';
			} else if ($_SESSION['listType'] == 2) {
				echo '<h1>Editor\'s Choice</h1>';
			}
		?>
	</div>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<?php 
			if ($_SESSION['listType'] == 3) { //if the list show is the top 10 of the site list, the maximum number of queries per page is 10, otherwise its 5
				$pLim = 10;
			} else {
				$pLim = $pN*5;
			}				
			while ($nextQuery && $num < $pLim) { //loops through the queries array by calling the createAlbumQuery function until either the page maximum is hit, or there are no more queries to go through
				createAlbumQuery($mysql_db, $num, $queries);
				$nextQuery = $_SESSION['nQ'];
				$num = $num + 1;
				if ($num >= $lQI) {
					$nextQuery = false;
				}
			}
			if ($lQI < 1) { //if the list query produces no results, this code informs the user through a notification blob
				echo '<div class="genSection hItem" style="margin: 0px 80px;">
					<h2>No results found. Please try a different search query.</h2>
				</div>';
			}
			$mysql_db->close();
			if ($_SESSION['listType'] == 1) { //if the list is the result of a search query, this code creates the html for the previous page and next page buttons to exist
				if ($pN > 1) {
					echo '<button name="prevPage" class="hubButtons hItem" type="submit">Previous Page</button>';
				}
				echo '<input type="text" name="typePN" size="1" style="margin: 20px;" value="'.$pN.'">';
				if ($nextQuery) {
					echo '<button name="nextPage" class="hubButtons hItem" type="submit">Next Page</button>';
				}
			}
		?>
	</form>
</body>
</html>