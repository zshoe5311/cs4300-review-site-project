<?php
	if (!isset($_SESSION)) {
		session_start();
	}
	
	if (isset($_REQUEST['homeB'])) {
		header('location: home.php');
	} else if (isset($_REQUEST['eCB'])) {
		$_SESSION['pageNum'] = 1;
		$_SESSION['nQ'] = true;
		header('location: eChoice.php');
	} else if (isset($_REQUEST['tB'])) {
		$_SESSION['pageNum'] = 1;
		$_SESSION['nQ'] = true;
		header('location: eChoice.php');
	} else if (isset($_REQUEST['lOB'])) {
		header('location: logout.php');
	} else if (isset($_REQUEST['aUB'])) {
		header('location: home.php');
	} else if (isset($_REQUEST['aPB'])) {
		header('location: adminPage.php');
	}
	
	echo '<div class="hubBar hItem">
		<div class="logo"> 
			<img src="logo4.png" alt = "logo">
		</div>
		
		<form method="post" action="'.$_SERVER['PHP_SELF'].'">
		<div class="hB search">
			<input type="text" placeholder="Search...">
		</div>
		<button name="homeB" class="hubButtons hB" href="home.php"> Home</button>
		<button name="eCB" class="hubButtons hB" href="eChoice.php"> Editor\'s Choice</button>
		<button name="tB" class="hubButtons hB" href="eChoice.php"> Top 10 of the Week</button>
		<button name="lOB" class="hubButtons hB" href="logout.php"> Log Out</button>
		<button name="aUB" class="hubButtons hB" href="#"> About Us</button>'; 
	if ($_SESSION['isAdmin'] == 1) {
		echo '<button name="aPB" class="hubButtons hB" href="adminPage.php">Admin Page</button>';	
	}
	echo '</form></div>';
?>