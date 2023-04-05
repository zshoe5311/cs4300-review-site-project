<?php
	session_start();
	
	if ($_SESSION['isAdmin'] == 0) {
		header('location: index.php');//real
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="newStyle.css">
	<title>Admin Page - MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<div class="genSection hItem">	
		<h1>Welcome to your admin page <?php echo $_SESSION['username'] ?>! Please choose your action:</h1>
		<a class="hubButtons" href="addAlbum.php"> Add Album</a>
		<a class="hubButtons" href="deleteAlbum.php"> Delete Album</a>
	</div>
</body>
</html>