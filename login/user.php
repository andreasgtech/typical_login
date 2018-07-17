<!DOCTYPE html>
<html>
<head>
	<title>SITE</title>
</head>

<body>
<?php
	session_start();
	//TODO: insert correct admin username here
	if(!isset($_SESSION['username']) || empty($_SESSION['username']) || ($_SESSION['username'] === 'admin@gmail.com')) {
		session_destroy();
		header("location: index.php");
		exit;
	}
	echo 'EDW EXEI SITE' . '<br>';
	echo $_SESSION['username'] . '<br>';
	echo '<form method="post">
		  <button type="submit" name="submit" class="submitbtn">Logout</button>
		  </form>';
	
	if (isset($_POST['submit'])) {
		//unset should only be used when logging out
		unset($_SESSION['username']);
		session_destroy();
		header("location: index.php");
		exit;
	}
?>
</body>
</html>
