<?php
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";
$rank = "member";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	$data = array(
				'secret' => "",
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
			$login_err = "Please enter a username.";
		} elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
			$login_err = "Username can only contain letters, numbers, and underscores.";
		} else{
			// Prepare a select statement
			$sql = "SELECT id FROM users WHERE username = ?";
			
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_username);
				
				// Set parameters
				$param_username = trim($_POST["username"]);
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					/* store result */
					mysqli_stmt_store_result($stmt);
					
					if(mysqli_stmt_num_rows($stmt) == 1){
						$login_err = "This username is already taken.";
					} else{
						$username = trim($_POST["username"]);
					}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
		}
		
		if(empty(trim($_POST["email"]))){
			$login_err = "Please enter an email.";
		} else if(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
			$login_err = "Email is not valid!";
		} else{
			// Prepare a select statement
			$sql = "SELECT id FROM users WHERE email = ?";
			
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_email);
				
				// Set parameters
				$param_email = trim($_POST["email"]);
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					/* store result */
					mysqli_stmt_store_result($stmt);
					
					if(mysqli_stmt_num_rows($stmt) == 1){
						$login_err = "This email is already taken.";
					} else{
						$email = trim($_POST["email"]);
					}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
		}
		
		// Validate password
		if(empty(trim($_POST["password"]))){
			$login_err = "Please enter a password.";     
		} elseif(strlen(trim($_POST["password"])) < 6){
			$login_err = "Password must have atleast 6 characters.";
		} else{
			$password = trim($_POST["password"]);
		}
		
		// Validate confirm password
		if(empty(trim($_POST["confirm_password"]))){
			$login_err = "Please confirm password.";     
		} else{
			$confirm_password = trim($_POST["confirm_password"]);
			if(empty($password_err) && ($password != $confirm_password)){
				$login_err = "Password did not match.";
			}
		}
		
		// Check input errors before inserting in database
		if(empty($login_err)){
			
			$_SESSION['posts']['username'] = $_POST["username"];
			$_SESSION['posts']['email'] = $_POST["email"];
			$_SESSION['posts']['password'] = $_POST["password"];
			unset($_POST);
			session_regenerate_id(true);
			header("location: store-registration-send-email.php");
			die();
			
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
	.wrapper{
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
		width: 320px;
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
		<title>VisualPay - Login</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
		<style>
			body{ font: 14px sans-serif; }
			.wrapper{ width: 360px; padding: 20px; }
		</style>
	</head>
	<body>
	  <div class="wrapper">
		<header>Sign up</header>
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
			  <input type="text" placeholder="Email" name="email">
			  <i class="icon fas fa-envelope"></i>
			  <i class="error error-icon fas fa-exclamation-circle"></i>
			</div>
			<div class="error error-txt"><?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?></div>
		  </div>
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
		  <div class="field password">
			<div class="input-area">
			  <input type="password" placeholder="Confirm Password" name='confirm_password'>
			  <i class="icon fas fa-lock"></i>
			  <i class="error error-icon fas fa-exclamation-circle"></i>
			</div>
			<div class="error error-txt"><?php echo $password_err; ?></div>
		  </div>
		  <div class="h-captcha" data-sitekey="54f4e38c-845f-454e-bd67-cdc3d5384699"></div>
		  <input type="submit" value="Sign up">
		</form>
		<div class="sign-txt">You already have an account? <a href="login.php">Log in now</a></div>
	  </div>
   
	</body>
</html>