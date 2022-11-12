<?php

	session_start();
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require 'phpmailer/src/Exception.php';
	require 'phpmailer/src/PHPMailer.php';
	require 'phpmailer/src/SMTP.php';
	$_SESSION['msg'] = '';
	
	//---------
	//EMAIL
	//---------
	
	if(isset($_SESSION['posts']['email'])) {
		include "config.php";
		$result = mysqli_query($link,"SELECT * FROM users WHERE email='" . $_SESSION['posts']['email'] . "'");
		$row= mysqli_num_rows($result);
		if($row < 1) {
			$token = md5($_SESSION['posts']['email']).rand(10,9999);
			mysqli_query($link, "INSERT INTO users(email, username, password, rang, email_verification_link) VALUES('" . $_SESSION['posts']['email'] . "', '" . $_SESSION['posts']['username'] . "',  '" . password_hash($_SESSION['posts']['password'], PASSWORD_DEFAULT) . "','member', '" . $token ."')");
			$lins = "<a href='https://xenor.net/verify-email.php?key=".$_SESSION['posts']['email']."&token=".$token."'>Click and Verify Email</a>";
			
			
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
			$mail->addAddress($_SESSION['posts']['email'], $_SESSION['posts']['username']);
			$mail->Subject  =  'Verify Email';
			$mail->IsHTML(true);
			$mail->Body    = 'Click On This Link to Verify Email '.$lins.'';
			
			unset($_SESSION['posts']);
			
			if($mail->Send()) {
				$_SESSION['msg'] = "Check Your Email box and Click on the email verification link.";
			} else {
				$_SESSION['msg'] = "Mail Error - >".$mail->ErrorInfo;
			}
		} else {
			$_SESSION['msg'] = "You have already registered with us. Check Your email box and verify email.";
		}
	}
?>

<style>
	p {
		text-align: center;
	}
</style>

<html>
	<head>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	</head>
	<body>
		<div class="container mt-3">
			<div class="card">
				<div class="card-header text-center">
					User Verification
				</div>
				<div class="card-body">
					<p><?php echo $_SESSION['msg'];
						unset($_SESSION['msg']);
					?></p>
				</div>
			</div>
		</div>
	</body>
</html>