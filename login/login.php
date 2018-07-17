<?php
	$usr = $passwd = "";
	$usr_err = $pass_err = "";
	if(empty($_POST['uname'])) {
		$this->HandleError("Username cannot be empty!");
		$usr_err = "Username cannot be empty!";
		return false;
	}

	if(empty($_POST['psw'])) {
		$this->HandleError("Password cannot be empty!");
		$pass_err = "Password cannot be empty!";
		return false;
	}

	$usr = trim(htmlspecialchars($_POST['uname']));
	$passwd = trim(htmlspecialchars($_POST['psw']));
	
	if(empty($username_err) && empty($password_err)){

		define('DB_SERVER', 'cihstest');
		define('DB_USERNAME', 'user');
		define('DB_PASSWORD', 'bar_usr');
		define('DB_NAME', 'barcodes');

		/* Attempt to connect to MySQL database */

		$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

		//Check connection
		if($link === false) {
			die("ERROR: Could not connect. " . mysqli_connect_error());
		}
		
		// Prepare a select statement
		$sql = "SELECT email, password FROM users WHERE email = ?";
		
		#echo $sql;
		
		#echo $usr;
		#echo $passwd;
		
		if($stmt = mysqli_prepare($link, $sql)){
			// Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "s", $param_username);

			// Set parameters
			$param_username = $usr;

			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Store result
				mysqli_stmt_store_result($stmt);	
				// Check if username exists, if yes then verify password
				if(mysqli_stmt_num_rows($stmt) == 1){                    
					// Bind result variables
					mysqli_stmt_bind_result($stmt, $username, $hashed_password);
					if(mysqli_stmt_fetch($stmt)){
						if(password_verify($passwd, $hashed_password)){
							/* Password is correct, so start a new session and
							save the username to the session */
							session_start();
							$_SESSION['username'] = $username;
							//TODO: insert correct admin email here
							if($username==='admin@gmail.com') {
								header("location: admin.php");
							}
							else {
								header("location: temp.php");
							}
						} else{
							// Display an error message if password is not valid
							$password_err = 'The password you entered was not valid.';
							if(!isset($_SESSION['username'])) {
								echo $GLOBALS['usr'] . '<br>';
							}
							header("location: index.php");
						}
					}
				} else {
					// Display an error message if username doesn't exist
					$username_err = 'No account found with that username.';
					$GLOBALS['err'] = $username_err;
					header("location: index.php");
				}

			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}

		}
		// Close statement
		mysqli_stmt_close($stmt);
	}
    // Close connection
    mysqli_close($link);

?>
