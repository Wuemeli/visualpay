<?php
	session_start();
	
	require_once('../config.php');
	
	$stmt = $link->prepare('SELECT balance FROM users WHERE id = ?');
	// In this case we can use the account ID to get the account info.
	$stmt->bind_param('i', $_SESSION['id']);
	$stmt->execute();
	$stmt->bind_result($balance);
	$stmt->fetch();
	$stmt->close();
	


	if(isset($_POST['amount'])) {
		if($_POST['amount'] >= 5 && $_POST['amount'] <= 78) {
			if($balance >= $_POST['amount']) {
				if ($_POST['action'] == 'Bank Transfer') {
					$_SESSION['payout']['amount'] = $_POST['amount'];
					$_SESSION['payout']['type'] = "Bank Transfer";
					unset($_POST);
					header("location: payout-bank.php");
					die();
				} else {
					echo "Error";
				}
			} else {
				echo "<div><h1>You don't have enough money!</h1></div>";
			}
		} else {
			echo '<div><h1>You need to withdraw at least 7$!</h1></div>';
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