<?php
session_start();

require_once "config.php";

$stmt = $link->prepare('SELECT * FROM tickets ORDER BY created DESC');
$stmt->execute();
$resultSet = $stmt->get_result();

// pull all results as an associative array
$tickets = $resultSet->fetch_all(MYSQLI_ASSOC);



// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Output message variable
$msg = '';
// Check if POST data exists (user submitted the form)
if (isset($_POST['title'], $_POST['msg'])) {
    // Validation checks... make sure the POST data is not empty
    if (empty($_POST['title']) || empty($_POST['msg'])) {
        $msg = 'Please complete the form!';
    } else {
        // Insert new record into the tickets table
        $stmt = $link->prepare('INSERT INTO tickets (title, msg, user) VALUES (?, ?, ?)');
        $stmt->execute([ $_POST['title'], $_POST['msg'], $_SESSION['username'] ]);
        // Redirect to the view ticket page, the user will see their created ticket on this page
        header('Location: view.php?id=' . $link->lastInsertId());
    }
}

if (isset($_POST['msg']) && !empty($_POST['msg'])) {
	echo 'test'; 
    // Insert the new comment into the "tickets_comments" table
    $stmt = $link->prepare('INSERT INTO tickets_comments (ticket_id, msg, username) VALUES (?, ?, ?)');
	$stmt->bind_param('iss', $_GET['id'], $_POST['msg'], $_SESSION['username']);
    $stmt->execute();
    header('Location: view.php?id=' . $_GET['id']);
    exit;
}
$stmtr = $link->prepare('SELECT * FROM tickets_comments WHERE ticket_id = ' . $_GET['id'] . ' ORDER BY created DESC');
$stmtr->execute();
$resultSet = $stmtr->get_result();
$comments = $resultSet->fetch_all(MYSQLI_ASSOC);

if (!isset($_GET['id'])) {
    exit('No ID specified!');
}

// MySQL query that selects the ticket by the ID column, using the ID GET request variable
$stmts = $link->prepare('SELECT * FROM tickets WHERE id = ' . $_GET['id']);
$stmts->execute();
$resultSet = $stmts->get_result();
$ticket = $resultSet->fetch_all(MYSQLI_ASSOC);


if (!$ticket) {
    exit('Invalid ticket ID!');
}

if (isset($_GET['status']) && in_array($_GET['status'], array('open', 'closed', 'resolved'))) {
	if($_SESSION["rank"] == 'admin') {
		$stmt = $link->prepare('UPDATE tickets SET status = ? WHERE id = ?');
		$stmt->bind_param("si", $_GET['status'], $_GET['id']);
		$stmt->execute();
		header('Location: view.php?id=' . $_GET['id']);
		exit;
	}
}

if (isset($_GET['status']) && in_array($_GET['status'], array('delete'))) {
	if($_SESSION["rank"] == 'admin') {
		$stmt = $link->prepare('DELETE FROM tickets WHERE id = ?');
		$stmt->bind_param("i", $_GET['id']);
		$stmt->execute();
		header('Location: ticket.php');
		exit;
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
    background-color: #f3f4f7;
    margin: 0;
}
.navtop {
    background-color: #3f69a8;
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
    width: 1000px;
    margin: 0 auto;
}
.content h2 {
    margin: 0;
    padding: 25px 0;
    font-size: 22px;
    border-bottom: 1px solid #ebebeb;
    color: #666666;
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
    margin-bottom: 15px;
    border: 1px solid #cccccc;
	margin-left: 70px;
}
.create form textarea, .view form textarea {
    height: 200px;
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

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
<link rel="stylesheet" href="/font-awesome.css">

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

<div class="content view">
	<?php foreach($ticket as $ticke): ?>
		<?php if($ticke['username'] == $_SESSION["username"] || $_SESSION["rank"] == 'admin'): ?>
			<h2><?=htmlspecialchars($ticke['title'], ENT_QUOTES)?> <span class="<?=$ticke['status']?>">(<?=$ticke['status']?>)</span></h2>

			<div class="ticket">
				<p class="created"><?=date('F dS, G:ia', strtotime($ticke['created']))?></p>
				<p class="created"><?=$ticke['username']?></p>
				<p class="msg"><?=nl2br(htmlspecialchars($ticke['msg'], ENT_QUOTES))?></p>
			</div>


		<div class="btns">
			<a href="view.php?id=<?=$_GET['id']?>&status=closed" class="btn red">Close</a>
			<a href="view.php?id=<?=$_GET['id']?>&status=resolved" class="btn">Resolve</a>
			<a href="view.php?id=<?=$_GET['id']?>&status=delete" class="btn red">Delete</a>
		</div>

		<div class="comments">
			<?php foreach($comments as $comment): ?>
			<div class="comment">
				<div>
					<i class="fas fa-comment fa-2x"></i>
				</div>
				<p>
					<span><?=$comment['username']?></span>
					<span><?=date('F dS, G:ia', strtotime($comment['created']))?></span>
					<?=nl2br(htmlspecialchars($comment['msg'], ENT_QUOTES))?>
				</p>
			</div>
			<?php endforeach; ?>
			<form action="" method="post">
				<textarea name="msg" placeholder="Enter your comment..."></textarea>
				<input type="submit" value="Post Comment">
			</form>
		</div>
		<?php else:
			echo "You dont have access to this site!";
			endif;
		
		endforeach; ?>

</div>