<?php
	session_start();
	
	require_once('../config.php');
	

	if(isset($_POST['amount'])) {
		if($_POST['amount'] >= 0.5) {
			if ($_POST['action'] == 'PayPal') {
				$_SESSION['deposit']['amount'] = $_POST['amount'];
				$_SESSION['deposit']['type'] = "PayPal";
				unset($_POST);
				header("location: deposit-paypal.php");
				die();
			} else if ($_POST['action'] == 'Crypto') {
				$_SESSION['deposit']['amount'] = $_POST['amount'];
				$_SESSION['deposit']['type'] = "Crypto";
				unset($_POST);
				header("location: deposit-crypto.php");
				die();
			} else if ($_POST['action'] == 'Bank Transfer') {
				$_SESSION['deposit']['amount'] = $_POST['amount'];
				$_SESSION['deposit']['type'] = "Bank Transfer";
				unset($_POST);
				header("location: deposit-bank.php");
				die();
			} else if ($_POST['action'] == 'Stripe (Credit-Card, SEPA etc)') {
				$_SESSION['deposit']['amount'] = $_POST['amount'];
				$_SESSION['deposit']['type'] = "Stripe (Credit-Card, SEPA etc)";
				unset($_POST);
				header("location: deposit-stripe.php");
				die();
			} else {
				echo "Error";
			}
		} else {
			echo '<div><h1>You need to deposit at least 0.50$!</h1></div>';
		}
	} else {
		echo '<div><h1>Error!</h1></div>';
	}
?>

<style>
	div {
		margin-top: 20px;
		width: 320px;
		padding: 20px 20px 20px 20px;
		text-color: black;
		background: red;
		border-radius: 5px;
		opacity: 0.5;
	}
	h1 {
		color: black;
		opacity: 1;
	}
</style>