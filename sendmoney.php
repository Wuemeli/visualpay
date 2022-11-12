<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "config.php";

$stmt = $link->prepare('SELECT balance FROM users WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

$stmt = $link->prepare('SELECT username FROM users');
// In this case we can use the account ID to get the account info.
$stmt->execute();
$avaibleusernames = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// Check if POST data exists (user submitted the form)
if (isset($_POST['username'], $_POST['amount']) && !empty($_POST['username']) && !empty($_POST['amount'])) {
    // Validation checks... make sure the POST data is not empty
	$amont = $_POST['amount'];
	
	//Changeable
	$fees = 5;
	//NOT
	
	$amont = ($amont * $fees) / 100;
	
	$rightamont = $amont + $_POST['amount'];
	
	
    if (empty($_POST['username']) || empty($_POST['amount'])) {
        $msg = 'Please complete the form!';
    } else if(number_format($rightamont, 2) >= $balance) {
        $enoughBalance = false;
	} else if($_POST['username'] == $_SESSION['username']) {
		$sameusername = false;
	} else if($_POST['amount'] <= 0.09) {
		$underminimum = true;
    } else {
		
		$usernameFound = false;
		
		foreach ($avaibleusernames as $subarray)
		{
			if(in_array($_POST['username'], $subarray))
			{
				echo "he";
				$usernameFound = true;
				
				echo "test";
				
				$stringArray = implode("", $subarray);
				
				echo "test";
				
				$_SESSION['postdata'] = $_POST;
				$_SESSION['postdata']['receivername'] = $stringArray;
				unset($_POST);
				header("Location: ".$_SERVER['PHP_SELF']);
				exit;
				
				
				
				
				break;
			}
		}
		
	}
	
} else if(isset($_POST['username'], $_POST['amount']) && (empty($_POST['username']) || empty($_POST['amount']))) {
	$formincomplete = true;
}

if (array_key_exists('postdata', $_SESSION)) {
	
	$stringArray = $_SESSION['postdata']['receivername'];

	$stmt = $link->prepare('SELECT balance FROM users WHERE username = ?');
	// In this case we can use the account ID to get the account info.
	$stmt->bind_param('s', $stringArray);
	$stmt->execute();
	$stmt->bind_result($oldBalanceReceiver);
	$stmt->fetch();
	$stmt->close();
	
	$amont = $_SESSION['postdata']['amount'];
	
	//Changeable
	$fees = 5;
	//NOT
	
	$newFees = 100 - $fees;
	
	$amont = ($amont * $fees) / 100;
	
	$newBalancePayer = $balance - ($_SESSION['postdata']['amount'] + $amont);
	
	$reallyNewBalanceReceiver = number_format($oldBalanceReceiver + $_SESSION['postdata']['amount']);
	
	//Begin
	
	$stmt = $link->prepare('UPDATE users SET balance = ? WHERE id = ?');
	$stmt->bind_param("di", $newBalancePayer, $_SESSION['id']);
	$result = $stmt->get_result();
	$stmt->execute();
	
	
	$stmts = $link->prepare('UPDATE users SET balance = ? WHERE username = ?');
	$stmts->bind_param("ds", $reallyNewBalanceReceiver, $stringArray);
	$result2 = $stmts->get_result();
	$stmts->execute();
	
	$stmtr = $link->prepare('INSERT INTO transactions (payer_username,receiver_username,amount) VALUES (?,?,?)');
	$stmtr->bind_param("ssd", $_SESSION['username'], $_SESSION['postdata']['receivername'], $_SESSION['postdata']['amount']);
	$stmtr->execute();
	
	//TODO: Bug Fix required
	$sentsuccessfully = true;
	
	unset($_SESSION['postdata']);
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
    margin: 0;
	background: #f7f5f0;
}
.navtop {
	background-color: #142c8e;
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
    color: #ecf0f6;
    font-weight: normal;
}
.navtop div a {
    padding: 0 20px;
    text-decoration: none;
    color: #c5d2e5;
    font-weight: bold;
}
.navtop div a i {
    padding: 2px 8px 0 0;
}
.navtop div a:hover {
    color: #ecf0f6;
}
.content {
	width: 650px;
	margin: 0 auto;
	align-items: center;
}
.content h2 {
	margin: 0;
	padding: 25px 0;
	font-size: 22px;
	border-bottom: 1px solid #2f3947;
	color: #2f3947;
	
}
.btns {
	display: relative;
	border-radius: 5px;
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
.btns .btn:hover {
    background-color: #32a367;
}
.btns .btn.red {
    background-color: #b63838;
}
.btns .btn.red:hover {
    background-color: #a33232;
}
.home .tickets-list {
    display: flex;
    flex-flow: column;
}
.home .tickets-list .ticket {
    padding: 15px 0;
    width: 100%;
    border-bottom: 1px solid #ebebeb;
    display: flex;
    text-decoration: none;
}
.home .tickets-list .ticket .con {
    display: flex;
    justify-content: center;
    flex-flow: column;
}
.home .tickets-list .ticket i {
    text-align: center;
    width: 80px;
    color: #b3b3b3;
}
.home .tickets-list .ticket .title {
    font-weight: 600;
    color: #666666;
}
.home .tickets-list .ticket .msg {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    max-width: 400px;
    color: #999999;
    font-size: 14px;
}
.home .tickets-list .ticket .created {
    flex-grow: 1;
    align-items: flex-end;
    color: #999999;
    font-size: 14px;
}
.home .tickets-list .ticket:last-child {
    border-bottom: 0;
}
.home .tickets-list .ticket:hover {
    background-color: #fcfcfc;
}
.view h2 .open, .view h2 .resolved {
    color: #38b673;
}
.view h2 .closed {
    color: #b63838;
}
.view .ticket {
    padding: 20px 0;
}
.view .ticket .created {
    color: gray;
}
.view .comments {
    margin-top: 15px;
    border-top: 1px solid #ebebeb;
    padding: 25px 0;
}
.view .comments .comment {
    display: flex;
    padding-bottom: 5px;
}
.view .comments .comment div {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    width: 70px;
    color: #e6e6e6;
    transform: scaleX(-1);
}
.view .comments .comment p {
    margin: 0 0 20px 0;
}
.view .comments .comment p span {
    display: flex;
    font-size: 14px;
    padding-bottom: 5px;
    color: gray;
}
.create form, .view form {
    padding: 15px 0;
    display: flex;
    flex-flow: column;
    width: 400px;
}
.create form label, .view form label {
    display: inline-flex;
    width: 100%;
    padding: 10px 0;
    margin-right: 25px;
}
.create form input, .create form textarea, .view form input, .view form textarea {
    padding: 10px;
    width: 100%;
    margin-right: 25px;
    margin-bottom: 15px;
    border: 1px solid #cccccc;
}
.create form textarea, .view form textarea {
    height: 200px;
}
.errorr h6 {
	color: #FF0000
}
.create form input[type="submit"], .view form input[type="submit"] {
    display: block;
    background-color: #38b673;
    border: 0;
    font-weight: bold;
    font-size: 14px;
    color: #FFFFFF;
    cursor: pointer;
    width: 200px;
    margin-top: 15px;
    border-radius: 5px;
}
.create form input[type="submit"]:hover, .view form input[type="submit"]:hover {
    background-color: #32a367;
}

	body.loggedin {
		background: #f7f5f0;
	}
	.content {
		width: 650px;
		margin: 0 auto;
		align-items: center;
	}
	.content h2 {
		margin: 0;
		padding: 25px 0;
		font-size: 22px;
		border-bottom: 1px solid #2f3947;
		color: #2f3947;
		
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

.blueclass h6 {
	color: green;
}

</style>

<!DOCTYPE html>



<html>
	<head>
		<title>VisualPay - Home</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link rel="stylesheet" href="/font-awesome.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
		
	</head>
	
	<body>
		<nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="logo.png" alt="">
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
                        <a href="welcome.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="sendmoney.php">
                            <i class='bx bx-bar-chart-alt-2 icon' ></i>
                            <span class="text nav-text">Send Money</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="transactions.php">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Transactions</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="deposit.php">
                            <i class='bx bx-wallet icon' ></i>
                            <span class="text nav-text">Deposit</span>
                        </a>
                    </li>
					
					<li class="nav-link">
                        <a href="payout.php">
                            <i class='bx bx-bell icon'></i>
                            <span class="text nav-text">Payout</span>
                        </a>
                    </li>

                </li>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="logout.php">
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
				<a href="welcome.php"><i class="fas fa-home"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="resetpassword.php"><i class="fa fa-key"></i>Reset Password</a>
				<a href="ticket.php"><i class="fa fa-ticket"></i>Support Ticket</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
	


		<div class="content">
		<h2>Send Money</h2>
			<div class="content create">
				<label>Balance: <?=number_format($balance, 2)?>$</label>
				<form action="sendmoney.php" method="post">
					<label for="title">Username</label>
					<input type="text" name="username" placeholder="Username" id="username" required>
					<?php
						if(isset($formincomplete) && $formincomplete) {
							echo "<span class='errorr'><h6>Please complete the form!</h6></span>";
						}
						if(isset($usernameFound) && !$usernameFound) {
							echo "<span class='errorr'><h6>Username was not found!</h6></span>";
						}
						if(isset($enoughBalance) && !$enoughBalance) {
							echo "<span class='errorr'><h6>You don't have enough balance!</h6></span>";
						}
						if(isset($sameusername)) {
							echo "<span class='errorr'><h6>You can't send yourself money!</h6></span>";
						}
						if(isset($underminimum) && $underminimum) {
							echo "<span class='errorr'><h6>You must send at least 0.10$!</h6></span>";
						}
						if(isset($sentsuccessfully) && $sentsuccessfully) {
							echo "<span class='blueclass'><h6>The money was sent successfully!</h6></span>";
						}
					?>
					<label for="msg">Amount</label>
					<input type="number" min="0.1" max="10000" step="0.01" name="amount" id="amount" placeholder="Enter amount..." required="required">
					<input type="submit" value="Send">
					<label id="feestext"><pre>You will pay<b id="feesnumber"> ... </b>because of fees!</pre></label>
				</form>
			</div>
		</div>
	</body>
</html>

<script>

document.getElementById("amount").oninput = function() {myFunction()};

function myFunction() {
  document.getElementById('feesnumber').innerHTML = ' ' + (((document.getElementById("amount").value * 5) / 100) + parseFloat(document.getElementById("amount").value)).toFixed(2) + ' ';
}

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