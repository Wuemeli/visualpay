<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<style>
	* {
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, "segoe ui", roboto, oxygen, ubuntu, cantarell, "fira sans", "droid sans", "helvetica neue", Arial, sans-serif;
    font-size: 16px;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
body {
    background-color: #FFFFFF;
    margin: 0;
}
.navtop {
    background-color: #3f69a8;
    height: 60px;
    width: 100%;
    border: 0;
}
	.navtop {
		background-color: #2f3947;
		height: 60px;
		width: 100%;
		border: 0;
	}
	.navtop div {
		display: flex;
		margin: 0 auto;
		width: 1000px;
		height: 100%;
	}
	.navtop div h1, .navtop div a {
		display: inline-flex;
		align-items: center;
	}
	.navtop div h1 {
		flex: 1;
		font-size: 24px;
		padding: 0;
		margin: 0;
		color: #eaebed;
		font-weight: normal;
	}
	.navtop div a {
		padding: 0 20px;
		text-decoration: none;
		color: #c1c4c8;
		font-weight: bold;
	}
	.navtop div a i {
		padding: 2px 8px 0 0;
	}
	.navtop div a:hover {
		color: #eaebed;
	}
	body.loggedin {
		background-color: #f3f4f7;
	}
	.content {
		width: 1000px;
		margin: 0 auto;
	}
	.content h2 {
		margin: 0;
		padding: 25px 0;
		font-size: 22px;
		border-bottom: 1px solid #e0e0e3;
		color: #4a536e;
	}
	.content > p, .content > div {
		box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
		margin: 25px 0;
		padding: 25px;
		background-color: #fff;
	}
	.content > p table td, .content > div table td {
		padding: 5px;
	}
	.content > p table td:first-child, .content > div table td:first-child {
		font-weight: bold;
		color: #4a536e;
		padding-right: 15px;
	}
	.content > div p {
		padding: 5px;
		margin: 0 0 10px 0;
	}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="/font-awesome.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body class="loggedin">

<nav class="navtop">
			<div>
				<h1>VisualPay</h1>
				<a href="welcome.php"><i class="fas fa-home"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="resetpassword.php"><i class="fa fa-key"></i>Reset Password</a>
				<a href="ticket.php"><i class="fa fa-ticket"></i>Support Ticket</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Reset Password</h2>
			<p>Please fill out this form to reset your password.</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-group">
					<label>New Password</label>
					<input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
					<span class="invalid-feedback"><?php echo $new_password_err; ?></span>
				</div>
				<div class="form-group">
					<label>Confirm Password</label>
					<input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
					<span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit">
					<a class="btn btn-link ml-2" href="welcome.php">Cancel</a>
				</div>
			</form>
		</div>
    </div>    
</body>
</html>