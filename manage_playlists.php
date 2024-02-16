<!DOCTYPE html>
<?php
session_start();
include_once "functions.php";
?>
<html>

<head>
	<title>Manage Playlists</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="stylesheet" type="text/css" href="default.css" />

</head>
<!-- User can create there own playlist and can add there favourite videos,songs based on there preferences -->

<?php
$username = $_SESSION['username'];
if (isset($_POST['playlistname'])) {
	$playlistname = $_POST['playlistname'];

	$qry = "DELETE FROM userplaylist WHERE playlist='$playlistname' AND username='$username'";
	$res = mysqli_query($con, $qry);

	$qry = "DELETE FROM playlists WHERE playlist='$playlistname' AND username='$username'";
	$res = mysqli_query($con, $qry);
}
if (isset($_POST['mediaid'])) {
	$mediaid = $_POST['mediaid'];
	$qry = "DELETE FROM playlists WHERE mediaid='$mediaid' AND username='$username'";
	$res = mysqli_query($con, $qry);
	if (!$res) {
		echo mysqli_error($con);
	}
}

?>

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
	<br><br>

	<?php
	$user = $_GET['user'];

	$qry = "SELECT playlist FROM userplaylist WHERE username='$user'";
	$res = mysqli_query($con, $qry);
	$count = mysqli_num_rows($res);

	if ($count < 1) {
		echo "You have no playlists.";
	}

	while ($row = mysqli_fetch_row($res)) {
		$playlistname = $row[0]; ?>
		<table>
			<tr>
				<td>Playlist:</td>
				<td><?php echo $row[0]; ?></td>

				<td>
					<?php $path = "manage_playlists.php?user=" . $_GET['user']; ?>
					<form action=<?php echo $path ?> method="post">
						<input type="hidden" name="playlistname" value="<?php echo $playlistname; ?>">
						<input type="submit" value="Delete Playlist">
					</form>
				</td>
				<td>
					<form method="post" action="rename_playlist.php?id=<?php echo $row[0]; ?>">
						<input type="hidden" name="old_name" value="<?php echo $row[0]; ?>">
						<input type="submit" value="Rename" name="rename_playlist">
					</form>
				</td>
			</tr>
			<?php
			$qry = "SELECT media.mediaid, title FROM media INNER JOIN playlists ON media.mediaid=playlists.mediaid WHERE playlists.username='$username' AND playlists.playlist='$playlistname'";
			$titles = mysqli_query($con, $qry);
			if (!$titles) {
				echo mysqli_error($con);
			}
			while ($title = mysqli_fetch_row($titles)) {
				$mediaid = $title[0]; ?>
				<tr>
					<td><?php echo $title[1]; ?></td>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td>
						<?php $path = "manage_playlists.php?user=" . $_GET['user']; ?>
						<form action=<?php echo $path ?> method="post">
							<input type="hidden" name="mediaid" value="<?php echo $mediaid; ?>">
							<input type="submit" value="Delete Media">
						</form><br>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>

</body>

</html>
