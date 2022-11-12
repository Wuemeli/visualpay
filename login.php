<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	$data = array(
            'secret' => "0x2B944C3c7e8E0a7c6Bf85a4dD6A3F36104923778",
            'response' => $_POST['h-captcha-response']
        );
	$verify = curl_init();
	curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
	curl_setopt($verify, CURLOPT_POST, true);
	curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($verify);
	// var_dump($response);
	$responseData = json_decode($response);
	if($responseData->success) {
		if(empty(trim($_POST["username"]))){
			$login_err = "Please enter your username.";
		} else{
			$username = trim($_POST["username"]);
		}
		
		// Check if password is empty
		if(empty(trim($_POST["password"]))){
			$login_err = "Please enter your password.";
		} else{
			$password = trim($_POST["password"]);
		}
		
		// Validate credentials
		if(empty($login_err)){
			// Prepare a select statement
			$sql = "SELECT id, email, username, password, rang, status FROM users WHERE username = ?";
			
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_username);
				
				// Set parameters
				$param_username = $username;
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					// Store result
					mysqli_stmt_store_result($stmt);
					
					// Check if username exists, if yes then verify password
					if(mysqli_stmt_num_rows($stmt) == 1){                    
						// Bind result variables
						mysqli_stmt_bind_result($stmt, $id, $email, $username, $hashed_password, $rang, $status);
						if(mysqli_stmt_fetch($stmt)){
							if(password_verify($password, $hashed_password)){
								if($status == 1) {
									// Password is correct, so start a new session
									session_start();
									
									// Store data in session variables
									$_SESSION["loggedin"] = true;
									$_SESSION["id"] = $id;
									$_SESSION["username"] = $username;
									$_SESSION["email"] = $email;
									$_SESSION["rank"] = $rang;
									
									// Redirect user to welcome page
									header("location: welcome.php");
								} else {
									$login_err = "Your Account isn't verified! Check your email.";
								}
							} else{
								// Password is not valid, display a generic error message
								$login_err = "Invalid username or password.";
							}
						}
					} else{
						// Username doesn't exist, display a generic error message
						$login_err = "Invalid username or password.";
					}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
		}
	} 
	else {
	   $login_err = "Please make the captcha!";
	}
	
    
    // Close connection
    mysqli_close($link);
}
?>

<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap')
	*{
	  margin: 0;
	  padding: 0;
	  box-sizing: border-box;
	  font-family: "Poppins", sans-serif;
	}
	body{
	  width: 100%;
	  height: 100vh;
	  display: flex;
	  align-items: center;
	  justify-content: center;
	  background: #3853bf;
	}
	::selection{
	  color: #fff;
	  background: #3853bf;
	}
	.wrapper {
	  width: 380px;
	  padding: 40px 30px 50px 30px;
	  background: #fff;
	  border-radius: 5px;
	  text-align: center;
	  box-shadow: 10px 10px 15px rgba(0,0,0,0.1);
	}
	.wrapper header{
	  font-size: 35px;
	  font-weight: 600;
	}
	.wrapper form{
	  margin: 40px 0;
	}
	form .field{
	  width: 100%;
	  margin-bottom: 20px;
	}
	form .field.shake{
	  animation: shake 0.3s ease-in-out;
	}
	@keyframes shake {
	  0%, 100%{
		margin-left: 0px;
	  }
	  20%, 80%{
		margin-left: -12px;
	  }
	  40%, 60%{
		margin-left: 12px;
	  }
	}
	form .field .input-area{
	  height: 50px;
	  width: 100%;
	  position: relative;
	}
	form input{
	  width: 100%;
	  height: 100%;
	  outline: none;
	  padding: 0 45px;
	  font-size: 18px;
	  background: none;
	  caret-color: #5372F0;
	  border-radius: 5px;
	  border: 1px solid #bfbfbf;
	  border-bottom-width: 2px;
	  transition: all 0.2s ease;
	}
	form .field input:focus,
	form .field.valid input{
	  border-color: #5372F0;
	}
	form .field.shake input,
	form .field.error input{
	  border-color: #dc3545;
	}
	.field .input-area i{
	  position: absolute;
	  top: 50%;
	  font-size: 18px;
	  pointer-events: none;
	  transform: translateY(-50%);
	}
	.input-area .icon{
	  left: 15px;
	  color: #bfbfbf;
	  transition: color 0.2s ease;
	}
	.input-area .error-icon{
	  right: 15px;
	  color: #dc3545;
	}
	form input:focus ~ .icon,
	form .field.valid .icon{
	  color: #5372F0;
	}
	form .field.shake input:focus ~ .icon,
	form .field.error input:focus ~ .icon{
	  color: #bfbfbf;
	}
	form input::placeholder{
	  color: #bfbfbf;
	  font-size: 17px;
	}
	form .field .error-txt{
	  color: #dc3545;
	  text-align: left;
	  margin-top: 5px;
	}
	form .field .error{
	  display: none;
	}
	form .field.shake .error,
	form .field.error .error{
	  display: block;
	}
	form .pass-txt{
	  text-align: left;
	  margin-top: -10px;
	}
	.wrapper a{
	  color: #5372F0;
	  text-decoration: none;
	}
	.wrapper a:hover{
	  text-decoration: underline;
	}
	form input[type="submit"]{
	  height: 50px;
	  margin-top: 30px;
	  color: #fff;
	  padding: 0;
	  border: none;
	  background: #5372F0;
	  cursor: pointer;
	  border-bottom: 2px solid rgba(0,0,0,0.1);
	  transition: all 0.3s ease;
	}
	form input[type="submit"]:hover{
	  background: #2c52ed;
	}
	
	.errormsg {
		margin-top: 20px;
		width: 340px;
		padding: 20px 20px 20px 20px;
		text-color: black;
		background: red;
		border-radius: 5px;
		opacity: 0.5;
	}
</style>
 
<!DOCTYPE html>

<html lang="en">
<head>
	<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>VisualPay - Login</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
	
  
</head>
<body>
			<body>
			  <div class="wrapper">
				<header>Login</header>
				<div class="errorclass">
        
					<?php 
					if(!empty($login_err)){
						echo "<div class='errormsg'>" . $login_err . "</div>";
					}
					?>
					
				</div>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				  <div class="field email">
					<div class="input-area">
					  <input type="text" placeholder="Username" name="username">
					  <i class="icon fas fa-envelope"></i>
					  <i class="error error-icon fas fa-exclamation-circle"></i>
					</div>
					<div class="error error-txt"><?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?></div>
				  </div>
				  <div class="field password">
					<div class="input-area">
					  <input type="password" placeholder="Password" name='password'>
					  <i class="icon fas fa-lock"></i>
					  <i class="error error-icon fas fa-exclamation-circle"></i>
					</div>
					<div class="error error-txt"><?php echo $password_err; ?></div>
				  </div>
				  <div class="h-captcha" data-sitekey="54f4e38c-845f-454e-bd67-cdc3d5384699"></div>
				  <br>
				  <div class="pass-txt"><a href="reset-password.php">Forgot password?</a></div>
				  <input type="submit" value="Login">
				</form>
				<div class="sign-txt">Not yet member? <a href="register.php">Signup now</a></div>
			  </div>


			</body>
			</html>
        </form>
</body>
</html>