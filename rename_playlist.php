<?php
session_start();
include_once "functions.php";
?>
<html>

<head>
    <title>Rename playlist</title>
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
        <?php
        $poldname = $_GET['id'];
        ?>
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
    <?php
    if (isset($_POST['submit'])) {
        $newplaylistname = $_POST['pnewname'];

        $username = $_SESSION['username'];
        $qry = "SELECT COUNT(*) from userplaylist where playlist='$newplaylistname' and username='$username'";
        $res = mysqli_query($con, $qry);
        $row = mysqli_fetch_row($res);
        if ($row[0] == 0) {

            $qry = "UPDATE userplaylist set playlist='$newplaylistname' where playlist='$poldname' and username='$username'";
            $res = mysqli_query($con, $qry);
            
            if (!$res) {
                echo "the rename failed " . mysqli_error($con);
            } ?>
            <meta http-equiv="refresh" content="0;url=manage_playlists.php?user=<?php echo $username; ?>"><?php
                                                                                                        }
                                                                                                    } ?>
    <br><br>
    <form method="post" action="rename_playlist.php?id=<?php echo $poldname; ?>">
        <input type="text" value=<?php echo $poldname; ?>>
        <input type="text" name="pnewname" placeholder="New Playlist">
        <input type="submit" value="Submit" name="submit">
    </form>
</body>

</html>