
<?php
	if (!isset($_SESSION)) {
		session_start();
	}
	
	if (!empty($_POST['hSearch']) && isset($_REQUEST['hSearch'])) {
		if (!empty($_REQUEST['hSearch'])) {
			$_SESSION['sQuery'] = "SELECT albumID FROM `albums` WHERE albumName LIKE '%".trim($_REQUEST['hSearch'])."%' order by case
				when albumName LIKE '".trim($_REQUEST['hSearch'])."%' then 1
				when albumName LIKE '%".trim($_REQUEST['hSearch'])."%' then 2
				else 3
				end";
			$_SESSION['sText'] = trim($_REQUEST['hSearch']);
			$_SESSION['listType'] = 1;
			$_SESSION['pageNum'] = 1;
			$_SESSION['nQ'] = true;
			header('location: eChoice.php');
		}
	} 
	
	
	if (isset($_REQUEST['homeB'])) {
		header('location: home.php');
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
	} else if (isset($_REQUEST['aUB'])) {
		header('location: home.php');
	} else if (isset($_REQUEST['aPB'])) {
		header('location: accountPage.php?uNamePage='.$_SESSION['username']);
	} 
	
	/*else if (isset($_POST['hSearch'])) {
		echo "<h1>TEST</h1>";
		if (!empty($_REQUEST['hSearch'])) {
			$sQuery = "SELECT albumID FROM `albums` WHERE albumName LIKE '".trim($_REQUEST['hSearch'])."%'";
			$mysql_db->prepare($sQuery);
			echo "<h1>WHADDUP BITHCESS</h1>";
		}
		
	}*/
	//echo "<h1>TEST</h1>";
	echo '<div class="hubBar hItem">
		<div class="logo"> 
			<img src="logo4.png" alt = "logo">
		</div>
		<div class="hItem">
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
			<button name="tB" class="hubButtons hB"> Top 10 of the Site</button>
			<button name="lOB" class="hubButtons hB"> Log Out</button>
			<button name="aUB" class="hubButtons hB"> About Us</button>'; 
		if ($_SESSION['isAdmin'] == 1) {
			echo '<button name="aPB" class="hubButtons hB">Admin Page</button>';	
		} else {
			echo '<button name="aPB" class="hubButtons hB">Account Page</button>';
		}
	echo '</form></div></div>';
?>