<?php
$albums = array("good kid, m.A.A.d city", "Rubber Soul", "Discovery", "Certified Lover Boy", "The College Dropout");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="newStyle.css">
	<title>Editor's Choice -MM</title>
</head>
<body>
	<?php include 'hdr.php'; ?>
	<div class="homeLetter hItem">
		<h1>List Elements:</h1>
	</div>
	<?php
		foreach ($albums as $value) {
			$imgStr = $value.".jpg";
			echo '
			<div class="albumQuery">
				<img src="' . $imgStr . '">
				<h1>'.$value.' is an album in this list.</h1>
				<p>testingtestingtesting<br>testingtesting<br>testing</p>
			</div>
			';
		}
	?>
</body>
</html>