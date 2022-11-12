<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "config.php";

$payers = array();
$payer = array();

$receivers = array();
$receiver = array();

$stmt = $link->prepare('SELECT amount, receiver_username, created_at FROM transactions WHERE payer_username = ? ORDER BY created_at ASC');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('s', $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($receiverTransactionAmount, $receiverUsername, $receiverdate);

while($stmt->fetch()) {
    $payers[] = array('amount'=>$receiverTransactionAmount,'payer'=>$receiverUsername,'date'=>$receiverdate);
}
$stmt->close();

$stmt = $link->prepare('SELECT amount, payer_username, created_at FROM transactions WHERE receiver_username = ? ORDER BY created_at ASC');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('s', $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($payerTransactionAmount, $payerUsername, $payerdate);	

while($stmt->fetch()) {
    $receivers[] = array('amount'=>$payerTransactionAmount,'receiver'=>$payerUsername,'date'=>$payerdate);
}
$stmt->close();

//Alle Arrays Summe

$payerAmounts = array();
$receiverAmounts = array();

$r = 0;



foreach($payers as $vas) {
	$amont = $payers[$r]['amount'];
	
	//Changeable
	$fees = 5;
	//NOT ANYMORE

	$amont = ($amont * $fees) / 100;
	$amount = $payers[$r]['amount'] + $amont;
	
	array_push($payerAmounts, $amount);
	$r++;
}

$g = 0;

foreach($receivers as $vass) {
	array_push($receiverAmounts, $receivers[$g]['amount']);
	$g++;
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
		background: #f7f5f0;
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
	opacity: 1;
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







@import url('https://fonts.googleapis.com/css2?family=Lato&family=Poppins&display=swap');
  * {
  padding:0;
  margin:0;
  box-sizing:border-box
}

.wrapper {
  background-color:#222;
  min-height:100px;
  max-width:800px;
  margin:10px auto;
  padding:10px 30px
}
.dark,.input:focus {
  background-color:#222
}
.navbar {
  padding:0.5rem 0rem
}
.navbar .navbar-brand {
  font-size:30px
}
#income {
  border-right:1px solid #bbb
}
.notify {
  display:none
}
.nav-item .nav-link .fa-bell-o,.fa-long-arrow-down,.fa-long-arrow-up {
  padding:10px 10px;
  background-color:#444;
  border-radius:50%;
  position:relative;
  font-size:18px
}
.nav-item .nav-link .fa-bell-o::after {
  content:'';
  position:absolute;
  width:7px;
  height:7px;
  border-radius:50%;
  background-color:#ffc0cb;
  right:10px;
  top:10px
}
.nav-item input {
  border:none;
  outline:none;
  box-shadow:none;
  padding:3px 8px;
  width:75%;
  color:#eee
}
.nav-item .fa-search {
  font-size:18px;
  color:#bbb;
  cursor:pointer
}
.navbar-nav:last-child {
  display:flex;
  align-items:center;
  width:40%
}
.navbar-nav .nav-item {
  padding:0px 0px 0px 10px
}
.navbar-brand p {
  display:block;
  font-size:14px;
  margin-bottom:3px
}
.text {
  color:#bbb
}
.money {
  font-family:'Lato',sans-serif
}
.fa-long-arrow-down,.fa-long-arrow-up {
  padding-left:12px;
  padding-top:8px;
  height:30px;
  width:30px;
  border-radius:50%;
  font-size:1rem;
  font-weight:100;
  color:#28df99
}
.fa-long-arrow-up {
  color:#ffa500
}
.nav.nav-tabs {
  border:none
}
.nav.nav-tabs .nav-item .nav-link {
  color:#bbb;
  border:none
}
.nav.nav-tabs .nav-link.active {
  background-color:#222;
  border:none;
  color:#fff;
  border-bottom:4px solid #fff
}
.nav.nav-tabs .nav-item .nav-link:hover {
  border:none;
  color:#eee
}
.nav.nav-tabs .nav-item .nav-link.active:hover {
  border-bottom:4px solid #fff
}
.nav.nav-tabs .nav-item .nav-link:focus {
  border-bottom:4px solid #fff;
  color:#fff
}
.table-dark {
  background-color:#222
}
.table thead th {
  text-transform:uppercase;
  color:#bbb;
  font-size:12px
}
.table thead th,.table td,.table th {
  border:none;
  overflow:auto auto
}
td .fa-briefcase,td .fa-bed,td .fa-exchange,td .fa-cutlery {
  color:#ff6347;
  background-color:#444;
  padding:5px;
  border-radius:50%
}
td .fa-bed,td .fa-cutlery {
  color:#40a8c4
}
td .fa-exchange {
  color:#b15ac7
}
tbody tr td .fa-cc-mastercard,tbody tr td .fa-cc-visa {
  color:#bbb
}
tbody tr td.text-muted {
  font-family:'Lato',sans-serif
}
tbody tr td .fa-long-arrow-up,tbody tr td .fa-long-arrow-down {
  font-size:12px;
  padding-left:7px;
  padding-top:3px;
  height:20px;
  width:20px
}
.results span {
  color:#bbb;
  font-size:12px
}
.results span b {
  font-family:'Lato',sans-serif
}
.pagination .page-item .page-link {
  background-color:#444;
  color:#fff;
  padding:0.3rem .75rem;
  border:none;
  border-radius:0
}
.pagination .page-item .page-link span {
  font-size:16px
}
.pagination .page-item.disabled .page-link {
  background-color:#333;
  color:#eee;
  cursor:crosshair
}
@media(min-width:768px) and (max-width:991px) {
  .wrapper {
  margin:auto
}
.navbar-nav:last-child {
  display:flex;
  align-items:start;
  justify-content:center;
  width:100%
}
.notify {
  display:inline
}
.nav-item .fa-search {
  padding-left:10px
}
.navbar-nav .nav-item {
  padding:0px
}
}@media(min-width:300px) and (max-width:767px) {
  .wrapper {
  margin:auto
}
.navbar-nav:last-child {
  display:flex;
  align-items:start;
  justify-content:center;
  width:100%
}
.notify {
  display:inline
}
.nav-item .fa-search {
  padding-left:10px
}
.navbar-nav .nav-item {
  padding:0px
}
#income {
  border:none
}
}@media(max-width:400px) {
  .wrapper {
  padding:10px 15px;
  margin:auto
}
.btn {
  font-size:12px;
  padding:10px
}
.nav.nav-tabs .nav-link {
  padding:10px
}
}


</style>

<!DOCTYPE html>



<html>
	<head>
		<title>VisualPay - Home</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link rel="stylesheet" href="/font-awesome.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
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
		
		
			
			<div class="wrapper rounded">
  <nav class="navbar navbar-expand-lg navbar-dark dark d-lg-flex align-items-lg-start">
    <a class="navbar-brand" href="#">
      Transactions
		  <p class="text-muted pl-1">
			Welcome to your transactions
		  </p>
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
		aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		  <span class="navbar-toggler-icon">
		  </span>
		</button>
	  </nav>
	  <div class="row mt-2 pt-2">
		<div class="col-md-6" id="income">
		  <div class="d-flex justify-content-start align-items-center">
			<p class="fa fa-long-arrow-down">
			</p>
			<p class="text mx-3">
			  Income
			</p>
			<p class="text-white ml-4 money">
			<?php
				echo number_format(array_sum($receiverAmounts), 2);
			?>
			</p>
		  </div>
		</div>
		<div class="col-md-6">
		  <div class="d-flex justify-content-md-end align-items-center">
			<div class="fa fa-long-arrow-up">
			</div>
			<div class="text mx-3">
			  Expense
			</div>
			<div class="text-white ml-4 money">
			<?php
				echo number_format(array_sum($payerAmounts), 2);
			?>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="d-flex justify-content-between align-items-center mt-3">
		<ul class="nav nav-tabs w-75">
		  <li class="nav-item">
			<a class="nav-link active" href="#history">
			  History
			</a>
		  </li>
		</ul>
	  </div>
	  <div class="table-responsive mt-3">
		<table class="table table-dark table-borderless">
		  <thead>
			<tr>
			  <th scope="col">
				User
			  </th>
			  <th scope="col">
				Type
			  </th>
			  <th scope="col">
				Date
			  </th>
			  <th scope="col" class="text-right">
				Amount
			  </th>
			</tr>
		  </thead>
		  <tbody>
				<?php
					if(count($payers) <= 0 && count($receivers) <= 0) {
						
					} else {
						
					
						//DELETE AND RESET AUTOINCREMENT:
						//ALTER TABLE transactions AUTO_INCREMENT = 1
						$i = 0;
					
						foreach($receivers as $val) {
							
							echo '<tr>';
							echo '<td scope="row">'.$receivers[$i]['receiver'].'</td>';
							echo '<td><span></span></td>';
							echo '<td class="text-muted">'.$receivers[$i]['date'].'</td>';
							echo '<td class="d-flex justify-content-end align-items-center">';
							echo '<span class="fa fa-long-arrow-down mr-1"></span>';
							echo number_format($receivers[$i]['amount'], 2).'</td>';
							echo '</tr>';
							
							$i++;
							
						}
						
						$f = 0;
						
						foreach($payers as $val) {
							
							$amont = $payers[$f]['amount'];
	
							//Changeable
							$fees = 5;
							//NOT ANYMORE
							
							$amont = ($amont * $fees) / 100;
							
							$amount = $payers[$f]['amount'] + $amont;
							
							echo '<tr>';
							echo '<td scope="row">'.$payers[$f]['payer'].'</td>';
							echo '<td><span></span></td>';
							echo '<td class="text-muted">'.$payers[$f]['date'].'</td>';
							echo '<td class="d-flex justify-content-end align-items-center">';
							echo '<span class="fa fa-long-arrow-up mr-1"></span>';
							echo number_format($amount, 2).'</td>';
							echo '</tr>';
							
							$f++;
						}	
						
					}
				?>
		  </tbody>
		</table>
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