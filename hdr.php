<?php
	if (!isset($_SESSION)) {//starts sessions
		session_start();
	}
	
	if (!empty($_POST['hSearch']) && isset($_REQUEST['hSearch'])) {//if there is a non empty search query in the hub bar's search box, this code creates the SQL query to be run and its results
		if (!empty($_REQUEST['hSearch'])) {						   // displayed on the eChoice.php (list) page, and attaches it and other important information about the search to their respective Session variables
			$_SESSION['sQuery'] = "SELECT albumID FROM `albums` WHERE albumName LIKE '%".trim($_REQUEST['hSearch'])."%' order by case
				when albumName LIKE '".trim($_REQUEST['hSearch'])."%' then 1
				when albumName LIKE '%".trim($_REQUEST['hSearch'])."%' then 2
				when albumName LIKE '%".trim($_REQUEST['hSearch'])."' then 3
				end";
			$_SESSION['sText'] = trim($_REQUEST['hSearch']);
			$_SESSION['listType'] = 1;
			$_SESSION['pageNum'] = 1;
			$_SESSION['nQ'] = true;
			header('location: eChoice.php');
		}
	} 
	
	
	if (isset($_REQUEST['homeB'])) { //this if-else chain sets the values for different sessions variables and redirects the user to different pages
		header('location: index.php');//based on which button was pressed in the hub bar
	} else if (isset($_REQUEST['eCB'])) {
		$_SESSION['listType'] = 2;
		$_SESSION['pageNum'] = 1;
		$_SESSION['nQ'] = true;
		header('location: eChoice.php');
	} else if (isset($_REQUEST['tB'])) {
		$_SESSION['listType'] = 3;
		$_SESSION['pageNum'] = 1;
		$_SESSION['nQ'] = true;
		header('location: eChoice.php');
	} else if (isset($_REQUEST['lOB'])) {
		header('location: logout.php');
	} else if (isset($_REQUEST['lIB'])) {
		header('location: login.php');
	} else if (isset($_REQUEST['aPB'])) {
		header('location: accountPage.php?uNamePage='.$_SESSION['username']);
	} 
	
	//the echo statements below run the html code of the hub bar, controlling for expected differences with conditional if-else statements 
	
	echo '<div class="hubBar hItem"> 
		<div class="logo"> 
			<img src="logo4.png" alt = "logo">
		</div>';
		if ($_SESSION['loggedin'] == true) { //if the user is logged in, this code will display a welcome message with their user name in the hub bar next to the Music Madness logo
			echo '<div class="hItem" style="float: left; margin-top: 40px; margin-left: 60px;">
			<h3>Welcome, '.$_SESSION['username'].'.</h3>
			</div>';
		}
		echo '<div class="hItem">
		<form name="hSForm" method="post" action="'.$_SERVER['PHP_SELF'].'">
		<div class="hB search">
				<input name="hSearch" type="text" placeholder="Search...">
			</div>
		</form>
		</div>
		
		<div class="hItem">
			<form name="hdrForm" method="post" action="'.$_SERVER['PHP_SELF'].'">
			<button name="homeB" class="hubButtons hB"> Home</button>
			<button name="eCB" class="hubButtons hB"> Editor\'s Choice</button>
			<button name="tB" class="hubButtons hB"> Top 10 of the Site</button>';
		if ($_SESSION['loggedin'] == true) { //if the user is logged in, this code displays the log out button, else it displays the log in button
			echo '<button name="lOB" class="hubButtons hB"> Log Out</button>';
		} else {
			echo '<button name="lIB" class="hubButtons hB"> Log In</button>';
		}
		if ($_SESSION['loggedin'] == true) { //if the user is logged in, this code will either display the 'Account Page' button, or the 'Admin Page' button if the user account is an admin account
			if ($_SESSION['isAdmin'] == 1) {
				echo '<button name="aPB" class="hubButtons hB">Admin Page</button>';	
			} else {
				echo '<button name="aPB" class="hubButtons hB">Account Page</button>';
			}
		}
	echo '</form></div></div>';
?>