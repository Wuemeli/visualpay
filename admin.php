<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rank"] !== 'admin'){
	header("location: login.php");
	exit;
}
require_once "config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$samples = array();
$sample = array();

$transactions = array();

$stmt = $link->prepare('SELECT id,username,amount,type,payment,address FROM deposit_withdraw');
// In this case we can use the account ID to get the account info.
$stmt->execute();
//$stmt->bind_result($id, $username, $amount, $type, $payment);

$stmt->bind_result($id, $username, $amount, $type, $payment, $address);
//fetch each row of results and push the resultant array into the $samples array
while($stmt->fetch()) {
    $samples[] = array('id'=>$id,'username'=>$username,'amount'=>$amount,'type'=>$type,'payment'=>$payment,'address'=>$address);
}

$stmt->close();

//------------------------------------

$stmt = $link->prepare('SELECT id,payer_username,receiver_username,amount,created_at FROM transactions');
// In this case we can use the account ID to get the account info.
$stmt->execute();
//$stmt->bind_result($id, $username, $amount, $type, $payment);

$stmt->bind_result($id, $payer_name, $receiver_name, $amount, $date);
//fetch each row of results and push the resultant array into the $samples array
while($stmt->fetch()) {
    $transactions[] = array('id'=>$id,'payer_name'=>$payer_name,'receiver_name'=>$receiver_name,'amount'=>$amount,'date'=>$date);
}

$stmt->close();

if(isset($_POST['deletedepositwithdrawbutton'])) {
	$id = $_POST['ids'];
	
	$stmt = $link->prepare("DELETE FROM deposit_withdraw WHERE id = ?");
	$stmt->bind_param("i", $id);
	$result = $stmt->get_result();
	$stmt->execute();
	
	header('location: admin.php');
	die();
}

if(isset($_POST['okdepositwithdrawbutton'])) {
	$id = $_POST['ids'];
	$idsr = $_POST['idsr'];
	$amount = $_POST['amounts'];
	$types = $_POST['types'];
	
	$stmt = $link->prepare('SELECT balance FROM users WHERE username = ?');
	$stmt->bind_param('s', $idsr);
	$stmt->execute();
	$stmt->bind_result($balances);
	$stmt->fetch();
	$stmt->close();
	
	if($types == "deposit") {
		$newBalance = $amount+$balances;
		$stmt = $link->prepare('UPDATE users SET balance = ? WHERE username = ?');
		$stmt->bind_param("ds", $newBalance, $idsr);
		$result = $stmt->get_result();
		$stmt->execute();
	}
	
	$stmt = $link->prepare("DELETE FROM deposit_withdraw WHERE id = ?");
	$stmt->bind_param("i", $id);
	$result = $stmt->get_result();
	$stmt->execute();

	require 'phpmailer/src/Exception.php';
	require 'phpmailer/src/PHPMailer.php';
	require 'phpmailer/src/SMTP.php';
	
	$stmt = $link->prepare('SELECT email FROM users WHERE username = ?');
	$stmt->bind_param('s', $idsr);
	$stmt->execute();
	$stmt->bind_result($emails);
	$stmt->fetch();
	$stmt->close();

	if($types == "deposit") {
		$mail = new PHPMailer();
		$mail->CharSet =  "utf-8";
		$mail->IsSMTP();
		// enable SMTP authentication
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "tls";
		// GMAIL username			
		$mail->Username = "visualpay@web.de";
		// GMAIL password
		$mail->Password = "Halleluia123!";
		// sets GMAIL as the SMTP server
		$mail->Host = "smtp.web.de";
		// set the SMTP port for the GMAIL server
		$mail->Port = "587";
		$mail->setFrom('visualpay@web.de', 'VisualPay');
		$mail->addAddress($emails, $idsr);
		$mail->Subject = "Deposit Request";
		$mail->IsHTML(true);
		$mail->Body = "We checked your Deposit Request and we got the money.\nYour Balance now is ".$amount+$balances."$";
		if($mail->Send()) {
			echo "Mail sent.";
		} else {
			echo "Mail Error - >".$mail->ErrorInfo;
		}
	} else if($types == "payout") {
		$mail = new PHPMailer();
		$mail->CharSet =  "utf-8";
		$mail->IsSMTP();
		// enable SMTP authentication
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "tls";
		// GMAIL username			
		$mail->Username = "visualpay@web.de";
		// GMAIL password
		$mail->Password = "Halleluia123!";
		// sets GMAIL as the SMTP server
		$mail->Host = "smtp.web.de";
		// set the SMTP port for the GMAIL server
		$mail->Port = "587";
		$mail->setFrom('visualpay@web.de', 'VisualPay');
		$mail->addAddress($emails, $idsr);
		$mail->Subject = "Withdrawal Request";
		$mail->IsHTML(true);
		$mail->Body = "We checked your Withdrawal Request and accepted it.\nYou will soon receive your money on the selected payout type.";
		if($mail->Send()) {
			echo "Mail sent.";
		} else {
			echo "Mail Error - >".$mail->ErrorInfo;
		}
		
	}
	header('location: admin.php');
	die();
	
}


if(isset($_POST['deletealltransactions'])) {
	if($_SESSION['deletetransactions']['areyousuretext'] == 'Are you sure?') {
		$stmt = $link->prepare("TRUNCATE TABLE transactions");
		$result = $stmt->get_result();
		$stmt->execute();
		
		header('location: admin.php');
		die();
	} else {
		$_SESSION['deletetransactions']['areyousuretext'] = 'Are you sure?';
	}
} else {
	$_SESSION['deletetransactions']['areyousuretext'] = 'Delete all Transactions';
}

if(isset($_GET['searchdepositwithdraw'])) {
	if(!empty($_GET['searchdepositwithdraw'])) {
		$stmt = $link->prepare('SELECT id,username,amount,type,payment,address FROM deposit_withdraw WHERE username = ? or type = ? or payment = ?');
		$stmt->bind_param('sss', $_GET['searchdepositwithdraw'], $_GET['searchdepositwithdraw'], $_GET['searchdepositwithdraw']);
		// In this case we can use the account ID to get the account info.
		$stmt->execute();
		//$stmt->bind_result($id, $username, $amount, $type, $payment);

		$stmt->bind_result($id, $username, $amount, $type, $payment, $address);
		//fetch each row of results and push the resultant array into the $samples array
		while($stmt->fetch()) {
			$searchdepositwithdraw[] = array('id'=>$id,'username'=>$username,'amount'=>$amount,'type'=>$type,'payment'=>$payment,'address'=>$address);
		}
	}
}

if(isset($_GET['searchtransactions'])) {
	if(!empty($_GET['searchtransactions'])) {
		$stmt = $link->prepare('SELECT id,payer_username,receiver_username,amount,created_at FROM transactions WHERE payer_username = ? or receiver_username = ?');
		$stmt->bind_param('ss', $_GET['searchtransactions'], $_GET['searchtransactions']);
		// In this case we can use the account ID to get the account info.
		$stmt->execute();
		//$stmt->bind_result($id, $username, $amount, $type, $payment);

		$stmt->bind_result($id, $payer_name, $receiver_name, $amount, $date);
		//fetch each row of results and push the resultant array into the $samples array
		while($stmt->fetch()) {
			$searchtransactions[] = array('id'=>$id,'payer_name'=>$payer_name,'receiver_name'=>$receiver_name,'amount'=>$amount,'date'=>$date);
		}
	}
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
    background-s: #FFFFFF;
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
	
	.content > p {
		padding: 100px;
		font-weight: 500;
		font-size: 75px;
	}
	
	.content > div p {
		padding: 5px;
		margin: 0 0 10px 0;
	}
	
	.btns {
		display: flex;
	}
	.btns .btn {
		display: inline-block;
		text-decoration: none;
		background-color: #38b673;
		font-weight: bold;
		font-size: 14px;
		border-radius: 5px;
		color: #FFFFFF;
		padding: 10px 15px;
		margin: 15px 10px 15px 0;
	}
	.btns > p {
		padding: 25px;
		background-color: #fff;
		color: #4a536e;
		padding-right: 15px;
		padding: 100px;
		font-weight: 500;
		font-size: 75px;
		
	}
	.btns .btn:hover {
		background-color: #32a367;
	}
	.btns .btn.red {
		background-color: #b63838;
	}
	.btns .btn.red:hover {
		background-color: #a33232;
	}
	}
	
	
/* Google Font Import - Poppins */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

:root{
    /* ===== Colors ===== */
    --body-color: #E4E9F7;
    --sidebar-color: #FFF;
    --primary-color: #695CFE;
    --primary-color-light: #F6F5FF;
    --toggle-color: #DDD;
    --text-color: #707070;

    /* ====== Transition ====== */
    --tran-03: all 0.2s ease;
    --tran-03: all 0.3s ease;
    --tran-04: all 0.3s ease;
    --tran-05: all 0.3s ease;
}

body{
    min-height: 100vh;
    background-color: var(--body-color);
    transition: var(--tran-05);
}

::selection{
    background-color: var(--primary-color);
    color: #fff;
}

body.dark{
    --body-color: #18191a;
    --sidebar-color: #242526;
    --primary-color: #3a3b3c;
    --primary-color-light: #3a3b3c;
    --toggle-color: #fff;
    --text-color: #ccc;
}

/* ===== Sidebar ===== */
 .sidebar{
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 250px;
    padding: 10px 14px;
    background: var(--sidebar-color);
    transition: var(--tran-05);
    z-index: 100;  
}
.sidebar.close{
    width: 88px;
}

/* ===== Reusable code - Here ===== */
.sidebar li{
    height: 50px;
    list-style: none;
    display: flex;
    align-items: center;
    margin-top: 10px;
}

.sidebar header .image,
.sidebar .icon{
    min-width: 60px;
    border-radius: 6px;
}

.sidebar .icon{
    min-width: 60px;
    border-radius: 6px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.sidebar .text,
.sidebar .icon{
    color: var(--text-color);
    transition: var(--tran-03);
}

.sidebar .text{
    font-size: 17px;
    font-weight: 500;
    white-space: nowrap;
    opacity: 1;
}
.sidebar.close .text{
    opacity: 0;
}
/* =========================== */

.sidebar header{
    position: relative;
}

.sidebar header .image-text{
    display: flex;
    align-items: center;
}
.sidebar header .logo-text{
    display: flex;
    flex-direction: column;
}
header .image-text .name {
    margin-top: 2px;
    font-size: 18px;
    font-weight: 600;
}

header .image-text .profession{
    font-size: 16px;
    margin-top: -2px;
    display: block;
}

.sidebar header .image{
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar header .image img{
    width: 40px;
    border-radius: 6px;
}

.sidebar header .toggle{
    position: absolute;
    top: 50%;
    right: -25px;
    transform: translateY(-50%) rotate(180deg);
    height: 25px;
    width: 25px;
    background-color: var(--primary-color);
    color: var(--sidebar-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    cursor: pointer;
    transition: var(--tran-05);
}

body.dark .sidebar header .toggle{
    color: var(--text-color);
}

.sidebar.close .toggle{
    transform: translateY(-50%) rotate(0deg);
}

.sidebar .menu{
    margin-top: 40px;
}

.sidebar li.search-box{
    border-radius: 6px;
    background-color: var(--primary-color-light);
    cursor: pointer;
    transition: var(--tran-05);
}


.sidebar li.search-box input{
    height: 100%;
    width: 100%;
    outline: none;
    border: none;
    background-color: var(--primary-color-light);
    color: var(--text-color);
    border-radius: 6px;
    font-size: 17px;
    font-weight: 500;
    transition: var(--tran-05);
}
.sidebar li a{
    list-style: none;
    height: 100%;
    background-color: transparent;
    display: flex;
    align-items: center;
    height: 100%;
    width: 100%;
    border-radius: 6px;
    text-decoration: none;
    transition: var(--tran-03);
}

.sidebar li a:hover{
    background-color: var(--primary-color);
}
.sidebar li a:hover .icon,
.sidebar li a:hover .text{
    color: var(--sidebar-color);
}
body.dark .sidebar li a:hover .icon,
body.dark .sidebar li a:hover .text{
    color: var(--text-color);
}

.sidebar .menu-bar{
    height: calc(100% - 55px);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow-y: scroll;
}
.menu-bar::-webkit-scrollbar{
    display: none;
}
.sidebar .menu-bar .mode{
    border-radius: 6px;
    background-color: var(--primary-color-light);
    position: relative;
    transition: var(--tran-05);
}

.menu-bar .mode .sun-moon{
    height: 50px;
    width: 60px;
}

.mode .sun-moon i{
    position: absolute;
}
.mode .sun-moon i.sun{
    opacity: 0;
}
body.dark .mode .sun-moon i.sun{
    opacity: 1;
}
body.dark .mode .sun-moon i.moon{
    opacity: 0;
}

.menu-bar .bottom-content .toggle-switch{
    position: absolute;
    right: 0;
    height: 100%;
    min-width: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    cursor: pointer;
}
.toggle-switch .switch{
    position: relative;
    height: 22px;
    width: 40px;
    border-radius: 25px;
    background-color: var(--toggle-color);
    transition: var(--tran-05);
}

.switch::before{
    content: '';
    position: absolute;
    height: 15px;
    width: 15px;
    border-radius: 50%;
    top: 50%;
    left: 5px;
    transform: translateY(-50%);
    background-color: var(--sidebar-color);
    transition: var(--tran-04);
}

body.dark .switch::before{
    left: 20px;
}

.home{
    position: absolute;
    top: 0;
    top: 0;
    left: 250px;
    height: 100vh;
    width: calc(100% - 250px);
    background-color: var(--body-color);
    transition: var(--tran-05);
}
.home .text{
    font-size: 30px;
    font-weight: 500;
    color: var(--text-color);
    padding: 12px 60px;
}

.sidebar.close ~ .home{
    left: 78px;
    height: 100vh;
    width: calc(100% - 78px);
}
body.dark .home .text{
    color: var(--text-color);
}

.create form input[value="PayPal"], .view form input[type="submit"] {
    display: block;
    background-color: #0079C1;
    border: 0;
    font-weight: bold;
    font-size: 20px;
    color: #FFFFFF;
    cursor: pointer;
    width: 150px;
    margin-top: 15px;
    border-radius: 5px;
}
.create form input[value="PayPal"]:hover, .view form input[type="submit"]:hover {
    background-color: #00457C;
}

.create form input[value="Crypto"], .view form input[type="submit"] {
    display: block;
    background-color: #f2a900;
    border: 0;
    font-weight: bold;
    font-size: 20px;
    color: #FFFFFF;
    cursor: pointer;
    width: 150px;
    margin-top: 15px;
    border-radius: 5px;
}
.create form input[value="Crypto"]:hover, .view form input[type="submit"]:hover {
    background-color: #F7931A;
}



#navcontainer ul
{
margin: 0;
padding: 0;
list-style-type: none;
}

#navcontainer a
{
display: block;
color: #FFF;
background-color: #036;
width: 18em;
padding: 3px 12px 3px 8px;
text-decoration: none;
border-bottom: 1px solid #fff;
font-weight: bold;
}

#navcontainer a:hover
{
background-color: #369;
color: #FFF;
}

#navcontainer li li a
{
display: block;
color: #FFF;
background-color: #69C;
width: 18em;
padding: 3px 3px 3px 17px;
text-decoration: none;
border-bottom: 1px solid #fff;
font-weight: normal;
}

#deletedepositwithdrawbutton {
	color: black;
	background-color: red;
}

#okdepositwithdrawbutton {
	color: black;
	background-color: green;
}

#deletealltransactions {
	color: black;
	background-color: red;
}

</style>


<!DOCTYPE html>



<html>
	<head>
		<title>VisualPay - Admin</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link rel="stylesheet" href="/font-awesome.css">
		
		<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="style.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
		
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

			<h2>Admin Page</h2>
			<div class="btns">
				<div id="navcontainer">
					<h3>Deposits/Withdrawals</h3>
					<br>
					<form action="">
						<label>Search for type, username or payment method.</label>
						<br>
					  <input type="text" placeholder="Search.." name="searchdepositwithdraw">
					  <button type="submit">Submit</button>
					</form>
					<br>
						<?php
							$y = 0;
						
							if(isset($_GET['searchdepositwithdraw'])) {
								if(isset($searchdepositwithdraw)) {
									foreach($searchdepositwithdraw as $val) {
									
										echo '<li><a>'.$y.'</a>';
										echo '<ul>';
										echo '<li><a>Username: '.$searchdepositwithdraw[$y]['username'].'</a>';
										echo '<li><a>Amount: '.number_format($searchdepositwithdraw[$y]['amount'], 2).'</a>';
										echo '<li><a>Type: '.$searchdepositwithdraw[$y]['type'].'</a>';
										echo '<li><a>Payment Method: '.$searchdepositwithdraw[$y]['payment'].'</a>';
										if(isset($searchdepositwithdraw[$y]['address'])) {
											echo '<li><a>Address: '.$searchdepositwithdraw[$y]['address'].'</a>';
										}
										echo '</ul>';
										echo '</li>';
										
										$y++;
									
									}
								}
							}
						?>
					<br>
					<ul>
						<?php
							//DELETE AND RESET AUTOINCREMENT:
							//ALTER TABLE deposit_withdraw AUTO_INCREMENT = 1
							$i = 0;
						
							foreach($samples as $val) {
								
								echo '<li><a>'.$i.'</a>';
								echo '<ul>';
								echo '<li><a>Username: '.$samples[$i]['username'].'</a>';
								echo '<li><a>Amount: '.number_format($samples[$i]['amount'], 2).'</a>';
								echo '<li><a>Type: '.$samples[$i]['type'].'</a>';
								echo '<li><a>Payment Method: '.$samples[$i]['payment'].'</a>';
								if(isset($samples[$i]['address'])) {
									echo '<li><a>Address: '.$samples[$i]['address'].'</a>';
								}
								echo '<form method="post">';
								echo '<input type="hidden" name="ids" value="'.$samples[$i]['id'].'">';
								echo '<input type="hidden" name="idsr" value="'.$samples[$i]['username'].'">';
								echo '<input type="hidden" name="amounts" value="'.$samples[$i]['amount'].'">';
								echo '<input type="hidden" name="types" value="'.$samples[$i]['type'].'">';
								echo '<input type="submit" name="deletedepositwithdrawbutton" id="deletedepositwithdrawbutton" value="Delete">';
								echo '<input type="submit" name="okdepositwithdrawbutton" id="okdepositwithdrawbutton" value="OK">';
								echo '</form>';
								echo '</ul>';
								echo '</li>';
								
								$i++;
								
							}
						?>
					</ul>
					
					<br>
					<h3>Transactions</h3>
					<br>
					<form action="">
					  <input type="text" placeholder="Search username.." name="searchtransactions">
					  <button type="submit">Submit</button>
					</form>
					<br>
						<?php
							$y = 0;
						
							if(isset($_GET['searchtransactions'])) {
								if(isset($searchtransactions)) {
									foreach($searchtransactions as $val) {
									
										echo '<li><a>'.$y.'</a>';
										echo '<ul>';
										echo '<li><a>From: '.$searchtransactions[$y]['payer_name'].'</a>';
										echo '<li><a>To: '.$searchtransactions[$y]['receiver_name'].'</a>';
										echo '<li><a>Amount: '.number_format($searchtransactions[$y]['amount'], 2).'</a>';
										echo '<li><a>date: '.$searchtransactions[$y]['date'].'</a>';
										echo '</ul>';
										echo '</li>';
										
										$y++;
									
									}
								}
							}
						?>
					<br>
					<ul>
						<?php
							//DELETE AND RESET AUTOINCREMENT:
							//ALTER TABLE deposit_withdraw AUTO_INCREMENT = 1
							$fees = 5;
							
							$transactionstotal = array();
							$feestotal = array();
							
							$y = 0;
							$g = 0;
							$v = 0;
							
							foreach($transactions as $vas) {
								array_push($transactionstotal, $transactions[$g]['amount']);
								$g++;
							}
							
							foreach($transactions as $vas) {
								$amo = ($transactions[$v]['amount'] * $fees) / 100;
								array_push($feestotal, $amo);
								$v++;
							}
							
							echo '<form method="post">';
							echo '<input type="submit" name="deletealltransactions" id="deletealltransactions" value="'.$_SESSION['deletetransactions']['areyousuretext'].'">';
							
							echo '</form>';
							
							echo '<label>Total fees: '.number_format(array_sum($feestotal), 2).'$</label>';
							echo '<br>';
							echo '<label>Total trade amount: '.number_format(array_sum($transactionstotal), 2).'$</label>';
						
							foreach($transactions as $val) {
								
								echo '<li><a>'.$y.'</a>';
								echo '<ul>';
								echo '<li><a>From: '.$transactions[$y]['payer_name'].'</a>';
								echo '<li><a>To: '.$transactions[$y]['receiver_name'].'</a>';
								echo '<li><a>Amount: '.number_format($transactions[$y]['amount'], 2).'</a>';
								echo '<li><a>date: '.$transactions[$y]['date'].'</a>';
								echo '</ul>';
								echo '</li>';
								
								$y++;
								
							}
						?>
					</ul>
					<br>
					<br>
					<?php 

						require 'phpmailer/src/Exception.php';
						require 'phpmailer/src/PHPMailer.php';
						require 'phpmailer/src/SMTP.php';

						if(isset($_POST['submit'])){

							$mail = new PHPMailer();
							$mail->CharSet =  "utf-8";
							$mail->IsSMTP();
							// enable SMTP authentication
							$mail->SMTPAuth = true;
							$mail->SMTPSecure = "tls";
							// GMAIL username			
							$mail->Username = "visualpay@web.de";
							// GMAIL password
							$mail->Password = "Halleluia123!";
							// sets GMAIL as the SMTP server
							$mail->Host = "smtp.web.de";
							// set the SMTP port for the GMAIL server
							$mail->Port = "587";
							$mail->setFrom('visualpay@web.de', 'VisualPay');
							$mail->addAddress($_POST['email'], $_POST['username']);
							$mail->Subject = $_POST["subject"];
							$mail->IsHTML(true);
							$mail->Body = $_POST['message'];
							if($mail->Send()) {
								echo "Mail sent.";
							} else {
								echo "Mail Error - >".$mail->ErrorInfo;
							}
							}
						?>
						<!DOCTYPE html>
						<head>
						<title>Form submission</title>
						</head>
						<body>
						<form action="" method="post">
						Username: <input type="text" name="username"><br>
						Email: <input type="text" name="email"><br>
						Subject: <input type="text" name="subject"><br>
						Message:<br><textarea rows="5" name="message" cols="30"></textarea><br>
						<input type="submit" name="submit" value="Submit">
						</form>
						</body>
						</html> 
				</div>
			</div>
		</div>
	</body>
</html>