<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $link->prepare('SELECT email, rang FROM users WHERE id = ' . $_SESSION['id']);
// In this case we can use the account ID to get the account info.
$stmt->execute();
$stmt->bind_result($email, $rank);
$stmt->fetch();
$stmt->close();

if(isset($_POST['text'])) {
	$text = $_POST['text'];
	echo $text;
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
<html>
	<head>
		<meta charset="utf-8">
		<title>VisualPay - Profile</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link rel="stylesheet" href="/font-awesome.css">
		<script src="https://js.stripe.com/v3/"></script>
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
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?=$_SESSION['username']?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
					<tr>
						<td>Rank:</td>
						<td><?=$rank?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>

