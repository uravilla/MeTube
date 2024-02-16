<!DOCTYPE html>
<?php
session_start();
include_once "functions.php";
?>
<html>

<head>
	<title>Message</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="stylesheet" type="text/css" href="default.css" />

</head>

<body>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<a class="navbar-brand" href="browse.php"><img src="img/icon_metube.png" style="width:65px;" alt="logo"></a>

		<ul class="navbar-nav">
			<li class="nav-item" style="float:right"><a class="nav-link" href="update.php">Welcome <?php $username = $_SESSION['username'];
																									echo "$username" ?> </a></li>
			<li class="nav-item">
				<a class="nav-link" href="update.php">Profile</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="wordcloud.php">Word Cloud</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="media_upload.php">Upload File</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
					Subscriptions
				</a>

				<div class="dropdown-content">
					<?php
					$username = $_SESSION['username'];
					$qry = "select owner from subscribe where username='$username'";
					$res = mysqli_query($con, $qry);
					while ($row = mysqli_fetch_row($res)) {
						echo "<a href=\"subscriptions.php?id=" . $row[0] . "\">" . $row[0] . "</a>";
					}
					?>
				</div>

			</li>
			<li class="nav-item">
				<a class="nav-link" href="browse.php">Back</a>
			</li>
			<li class="nav-item">
				<!-- Search form -->
				<div class="search-container">
					<form action="browsefilter.php" method="post">
						<input type="text" id="searchwords" name="searchwords" size="60" placeholder="Search Keywords">
						<input type="submit" name="submit" value="Search">
					</form>
				</div>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="logout.php">Logout</a>
			</li>
		</ul>
	</nav>
	<br>
	<?php
	$username = $_SESSION['username'];
	$qry = "SELECT id from account where username='$username'";
	$res = mysqli_query($con, $qry);
	$row = mysqli_fetch_row($res);
	$id = $row[0];
	$qry = "SELECT * from contacts where (userid='$id' and isblock='unblock') or (contactid='$id' and isblock='unblock')";
	$res = mysqli_query($con, $qry);
	$row = mysqli_fetch_row($res);
	if ($row[0] != NULL) {
		if (isset($_POST['submit'])) {
			$username = $_SESSION['username'];
			$convid = $_GET['id'];
			$msg = $_POST['message'];
			$qry = "INSERT INTO messages(conversationid, sender, message,timesent) VALUES ('$convid', '$username', '$msg',CURRENT_TIME())";
			$res = mysqli_query($con, $qry);

			if ($res) {
				$smsg = "Message Created Successfully";
				$msgpath = 'Location: message.php?id=' . $_GET["id"];
				header($msgpath);
			} else {
				$fmsg = "Message Failed" . mysqli_error($con);
			}
		}
	} else {?>
		<div class="alert alert-danger" role="alert">
        <p align='center'><b>Sorry cannot send a message as this user is blocked.</b><p>
        </div> 		
	<?php }
	$convid = $_GET['id'];
	$qry = "SELECT userA, userB FROM conversations WHERE conversationid='$convid'";
	$users_result = mysqli_query($con, $qry);
	$user_row = mysqli_fetch_row($users_result);
	$userA = $user_row[0];
	$userB = $user_row[1];
	$qry = "SELECT * FROM messages WHERE conversationid='" . $_GET['id'] . "'" . "ORDER BY timesent";
	$res = mysqli_query($con, $qry);
	?>

	<h3>Messages between <?php echo $userA . " and " . $userB; ?></h3>
	<table>
		<tr>
			<th>Username</th>
			<th>Message</th>
		</tr>

		<?php
		while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
		?>
			<tr>
				<td><?php echo $row[1] ?></td>
				<td><?php echo $row[2] ?></td>
			</tr>
		<?php } ?>
		<?php
		$msgpath = "message.php?id=" . $_GET["id"]; ?>
		<form method="POST" action=<?php echo $msgpath ?>>
			<tr>
				<td></td>
				<td><input name="message" type="text" placeholder="New message (max 200 characters)..." maxlength="200"><br><br>
					<input name="submit" type="submit" value="Post">
				</td>
			</tr>
		</form>
	</table>
	<?php
        if (isset($smsg)) {?>

            <div class="alert alert-success" role="alert">
        <?php echo "<p align='center'><b>" .  $smsg . "</b><p>"; ?>
        </div>
        <?php } 
        if (isset($fmsg)) {?>
            <div class="alert alert-danger" role="alert">
        <?php echo "<p align='center'><b>" .  $fmsg . "</b><p>"; ?>
        </div>            
        <?php } ?>
</body>
</html>