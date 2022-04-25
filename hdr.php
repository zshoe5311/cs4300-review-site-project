<?php
	if (!isset($_SESSION)) {
		session_start();
	}
	
	echo '<div class="hubBar hItem">
		<div class="logo"> 
			<img src="logo4.png" alt = "logo">
		</div>
		<div class="hB search">
			<input type="text" placeholder="Search...">
		</div>

		
		<a class="hubButtons hB" href="home.php"> Home</a>
		<a class="hubButtons hB" href="eChoice.php"> Editor\'s Choice</a>
		<a class="hubButtons hB" href="eChoice.php"> Top 10 of the Week</a>
		
		<a class="hubButtons hB" href="logout.php"> Log Out</a>
		<a class="hubButtons hB" href="#"> About Us</a>'; 
	if ($_SESSION['isAdmin'] == 1) {
		echo '<a class="hubButtons hB" href="adminPage.php">Admin Page</a>';	
	}
	echo '</div>';
?>