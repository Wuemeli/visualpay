<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "../config.php";

$stmt = $link->prepare('SELECT balance FROM users WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

if(isset($_POST['paypalmail'])) {
	if(isset($_SESSION['deposit']['amount'])) {
			
		$amount = $_SESSION['deposit']['amount'];
		
		if($_SESSION['deposit']['amount'] >= 0.5 && $_SESSION['deposit']['amount'] <= 500) {
			
			$stmtr = $link->prepare("INSERT INTO deposit_withdraw (username,amount, type, payment, address) VALUES (?,?,'deposit','paypal',?)");
			$stmtr->bind_param("sds", $_SESSION['username'], $amount, $_POST['paypalmail']);
			$stmtr->execute();
			unset($_SESSION['deposit']);
			
			//TODO: Error Message
			$_SESSION['message']['success'] = 'We received the request. We will check very soon and update soon your VisualPay money.';
		}
			
	} else {
		//TODO: Error Message
		$_SESSION['message']['error'] = "If you want to deposit again, make a new form on the deposit page.";
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

.create form input[type="submit"], .view form input[type="submit"] {
    display: block;
    background-color: #38b673;
    border: 0;
    font-weight: bold;
    font-size: 14px;
    color: #FFFFFF;
    cursor: pointer;
    width: 150px;
	height: 30px;
    margin-top: 15px;
    border-radius: 5px;
}
.create form input[type="submit"]:hover, .view form input[type="submit"]:hover {
    background-color: #32a367;
}

.create form input[type="text"] {
	width: 300px;
}

.errorsuccessmsg .error label {
	color: red;
	font-weight: bold;
}

.errorsuccessmsg .success label {
	color: green;
	font-weight: bold;
}

</style>

<!DOCTYPE html>



<html>
	<head>
		<title>VisualPay - Home</title>
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
	
	<body>
		<nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="../logo.png" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name">VisualPay</span>
                    <span class="profession">Digital Paying System</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">

                <li class="menu-links">
                    <li class="nav-link">
                        <a href="../welcome.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="../sendmoney.php">
                            <i class='bx bx-bar-chart-alt-2 icon' ></i>
                            <span class="text nav-text">Send Money</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="../transactions.php">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Transactions</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="../deposit.php">
                            <i class='bx bx-wallet icon' ></i>
                            <span class="text nav-text">Deposit</span>
                        </a>
                    </li>
					
					<li class="nav-link">
                        <a href="../payout.php">
                            <i class='bx bx-bell icon'></i>
                            <span class="text nav-text">Payout</span>
                        </a>
                    </li>

                </li>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="../logout.php"> <?php //TODO: Logout on all Pages ?>
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>

                
                
            </div>
        </div>

    </nav>
	</body>

	<body class="loggedin">
		
		<nav class="navtop">

			<div>
				<h1>VisualPay</h1>
				<a href="../welcome.php"><i class="fas fa-home"></i>Home</a>
				<a href="../profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="../resetpassword.php"><i class="fa fa-key"></i>Reset Password</a>
				<a href="../ticket.php"><i class="fa fa-ticket"></i>Support Ticket</a>
				<a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		
		
		<div class="content">
			
			<h2>Deposit</h2>
			
				<div class="content create">
				
					<?php
						if(isset($_SESSION['deposit']['amount'])) {
							$amount = $_SESSION['deposit']['amount'];
							
							echo "<label><strong>Send " . number_format($amount, 2) . "$ to our paypal mail.</strong> Please type your paypal mail so we can check which username belongs to which VisualPay account.</label>";
						}
					?>
					<form action='deposit-paypal.php' method='post'>
						<input type='text' placeholder='Your Paypal Address' id='paypalmail' name='paypalmail' required>
						<br>
						<br>
						<label>Our Paypal address: <strong>dannyjesden962@gmail.com</strong></label>
						<br>
						<br>
						<?php
							if(isset($_SESSION['deposit']['amount'])) {
								echo "<label>Make sure, that the Paypal address is typed correctly. We won't do any refund!</label>";
							}
						?>
						<br>
						<br>
						<label><strong>IMPORTANT: </strong>Set to 'send to friends & family' and set the transaction text to your VisualPay username.</label>
						<div class='errorsuccessmsg'>
							<?php 
								if(isset($_SESSION['message']['error'])) {
									echo "<br>";
									echo "<div class='error'><label>". $_SESSION['message']['error'] ."</label></div>";
									unset($_SESSION['message']);
								} else if(isset($_SESSION['message']['success'])) {
									echo "<br>";
									echo "<div class='success'><label>". $_SESSION['message']['success'] ."</label></div>";
									unset($_SESSION['message']);
								}
							?>
						</div>
						<br>
						<input type='submit' name='submit' placeholder='Deposit' value='I sent the money'>
					</form>
			
				</div>
			
		</div>
		
		
<script>
	const body = document.querySelector('body'),
      sidebar = body.querySelector('nav'),
      toggle = body.querySelector(".toggle"),
      searchBtn = body.querySelector(".search-box"),
      modeSwitch = body.querySelector(".toggle-switch"),
      modeText = body.querySelector(".mode-text");


toggle.addEventListener("click" , () =>{
    sidebar.classList.toggle("close");
})

searchBtn.addEventListener("click" , () =>{
    sidebar.classList.remove("close");
})

modeSwitch.addEventListener("click" , () =>{
    body.classList.toggle("dark");
    
    if(body.classList.contains("dark")){
        modeText.innerText = "Light mode";
    }else{
        modeText.innerText = "Dark mode";
        
    }
});
    </script>