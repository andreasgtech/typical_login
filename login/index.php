<!DOCTYPE html>
<?php
	if(!isset($_POST['submit'])) {
		echo '<html>
			  <link rel="stylesheet" type="text/css" href="index.css" media="screen" />

			  <head>
				<title>Barcodes</title>
			  </head>

			  <body>
				<form method="post">
				 <div class="container_center">
				   <h1>Welcome to Barcodes!</h1>
				 </div>

				 <div class="container_center">
				   <label for="uname"><b>Username</b></label>
				   <input type="text" placeholder="Enter Username" name="uname" required>
				   <br>
				   <label for="psw"><b>Password</b></label>
				   <input type="password" placeholder="Enter Password" name="psw" required>
				   <br>
				   <button type="submit" name="submit" class="submitbtn">Login</button>
				   <br>
				   <label>
					 <input type="checkbox" checked="checked" name="remember"> Remember me
				   </label>
				 </div>
				</form>
			  </body>
			</html>';
	} 
	else {
		$flag = 0;
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
									header("location: user.php");
								}
							} else{
								// Display an error message if password is not valid
								$password_err = 'The password you entered was not valid.';
								$flag = 1;						
							}
						}
					} else {
						// Display an error message if username doesn't exist
						$username_err = 'No account found with that username.';
						$flag = 1;
					}
					if ($flag == 1) {
						echo '<html>
							  <link rel="stylesheet" type="text/css" href="index.css" media="screen" />

							  <head>
								<title>Barcodes</title>
							  </head>

							  <body>
								<form method="post">
								 <div class="container_center">
								   <h1>Welcome to Barcodes!</h1>
								 </div>

								 <div class="container_center">
								   <label for="uname"><b>Username</b></label>
								   <input type="text" placeholder="Enter Username" name="uname" required>
								   <br>
								   <label for="psw"><b>Password</b></label>
								   <input type="password" placeholder="Enter Password" name="psw" required>
								   <br>
								   <span style="color: red">Invalid username or password.</span><br>
								   <button type="submit" name="submit" class="submitbtn">Login</button>
								   <br>
								   <label>
									 <input type="checkbox" checked="checked" name="remember"> Remember me
								   </label>
								 </div>

								 <div class="container_left" style="background-color:#f1f1f1">
								   <span class="psw">Forgot <a href="respass.php">password?</a></span>
								 </div>
								</form>
							  </body>
							</html>';
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
	}
?>