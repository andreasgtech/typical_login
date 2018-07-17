<!DOCTYPE html>
<html>
<head>
	<title>PASS RESET</title>
</head>

<body>
<?php
	/*session_start();
	//TODO: insert correct admin username here
	if(!isset($_SESSION['username']) || empty($_SESSION['username']) || ($_SESSION['username'] === 'admin@gmail.com')) {
		session_destroy();
		header("location: index.php");
		exit;
	}
	echo 'EDW EXEI SITE' . '<br>';
	echo $_SESSION['username'] . '<br>';*/
	echo '<form method="post">
		  <input type="text" placeholder="Enter e-mail" name="email" required>
		  <br>
		  <button type="submit" name="submit" class="submitbtn">Send</button>
		  </form>';
	
	if (isset($_POST['submit'])) {
		//echo 'EDW STELNW TO MAIL STON' . '<br>';
		//echo htmlspecialchars(trim($_POST['email'])) . '<br>';
		//echo password_hash("12345", PASSWORD_DEFAULT) . '<br>';
		
		define('DB_SERVER', 'cihstest');
		define('DB_USERNAME', 'user');
		define('DB_PASSWORD', 'bar_usr');
		define('DB_NAME', 'barcodes');

		// Attempt to connect to MySQL database 

		$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

		//Check connection
		if($link === false) {
			die("ERROR: Could not connect. " . mysqli_connect_error());
		}

		//prepare statement for user check
		$sql = "SELECT email FROM users WHERE email = ?";
		
		if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "s", $param_username);

			// Set parameters
			$param_username = htmlspecialchars(trim($_POST['email']));

			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Store result
				mysqli_stmt_store_result($stmt);	
				// Check if username exists, if yes then proceed
				if(mysqli_stmt_num_rows($stmt) == 1){
					// Close statement
					mysqli_stmt_close($stmt);
					
					//prepare statement for reset_pass change
					$sql = "UPDATE users SET reset_password = ? , first_or_reset = '1' WHERE email = ?;";
					
					if($stmt = mysqli_prepare($link, $sql)){
						// Bind variables to the prepared statement as parameters
						mysqli_stmt_bind_param($stmt, "ss", $param_reset_pass_hashed, $param_email);
						// Set parameters
						$param_reset_pass = substr(hash("sha256",(uniqid(mt_rand(), true))), 0, 8);
						$param_reset_pass_hashed = password_hash($param_reset_pass, PASSWORD_DEFAULT);
						$param_email = htmlspecialchars(trim($_POST['email']));
						if(mysqli_stmt_execute($stmt)){
							
							//mail configuration
							ini_set("SMTP", "mail.elta.gr");
							ini_set("sendmail_from", "userplir2@elta-net.gr");
							
							$to = $param_email;
							$subject = "Barcodes password reset";
							$txt = "Ο προσωρινός σας κωδικός είναι ο: " . $param_reset_pass . ". Αν δεν ευθύνεστε για την ενέργεια αυτή, αγνοήστε αυτό το e-mail.";
							$header = "Content-Type: text/html;charset=utf-8";
							mail($to, $subject, $txt, $header);
							
						}
						else {
							echo "Oops! Something went wrong. Please try again later.";
						}
					}
					// Close statement
					mysqli_stmt_close($stmt);
					// Close connection
					mysqli_close($link);
				}
				echo 'Παρακαλώ ελέγξτε τη δοσμένη διεύθυνση e-mail.';
			}
			else {
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
	}
?>
</body>
</html>
