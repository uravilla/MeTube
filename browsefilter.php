<!DOCTYPE html>
<?php
session_start();
include_once "functions.php";
?>
<html>
<!-- Used to filter so that user can search based on there preferences -->
<head>
    <title>Media browse</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="default.css" />
    <script>
        function saveDownload(id) {
            $.post("media_download_process.php", {
                    id: id,
                },
                function(message) {

                }
            );
        }
    </script>
</head>

<body>

    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <a class="navbar-brand" href="browse.php"><img src="img/icon_metube.png" style="width:65px;" alt="logo"></a>
        <?php
        if (!empty($_SESSION['logged_in'])) {

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
        <?php } else {
        ?>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="registration.php">Register</a>
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
            </ul>
        <?php   } ?>

    </nav>
    <br>
    <h2>Search Results For: <?php $sw = $_POST['searchwords'];
                            echo " '$sw'" ?></h2>
    <br /><br />

    <?php
     error_reporting(E_ALL & ~E_NOTICE ^ E_WARNING);
    $srch = $_POST['searchwords'];
    $query = "SELECT count from keywords where keyword='$srch'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_row($result);
    $count = $row[0] + 1;
    $query = "UPDATE keywords set count='$count' where keyword='$srch'";
    $result = mysqli_query($con, $query);
    if (isset($_POST['type'])) {
        $type = $_POST['type'];
        if ($type == 'all') {
            $query = "SELECT DISTINCT media.mediaid, media.filename, media.filepath, media.type, media.lastaccesstime, media.title, media.description, media.category, media.user FROM media LEFT JOIN keywords ON media.mediaid = keywords.mediaid WHERE media.title LIKE '%$srch%' OR media.description LIKE '%$srch%' OR keywords.keyword LIKE '%$srch%' OR media.user LIKE '%$srch%'";
        } else if ($type == 'images') {
            $query = "SELECT * from media WHERE category='image' AND title LIKE '%$srch%' OR description LIKE '%$srch%'";
        } else if ($type == 'videos') {
            $query = "SELECT * from media WHERE category='video' AND title LIKE '%$srch%'";
        } else if ($type == 'audio') {
            $query = "SELECT * from media WHERE category='audio' AND title LIKE '%$srch%'";
        } else {
            $query = "SELECT DISTINCT media.mediaid, media.filename, media.filepath, media.type, media.lastaccesstime, media.title, media.description, media.category, media.user FROM media LEFT JOIN keywords ON media.mediaid = keywords.mediaid WHERE media.title LIKE '%$srch%' OR media.description LIKE '%$srch%' OR keywords.keyword LIKE '%$srch%' OR media.user LIKE '%$srch%'";
        }
    } else {
        $query = "SELECT DISTINCT media.mediaid, media.filename, media.filepath, media.type, media.lastaccesstime, media.title, media.description, media.category, media.user FROM media LEFT JOIN keywords ON media.mediaid = keywords.mediaid WHERE media.title LIKE '%$srch%' OR media.description LIKE '%$srch%' OR keywords.keyword LIKE '%$srch%' OR media.user LIKE '%$srch%'";
    }
    $result = mysqli_query($con, $query);
    if (!$result) {
        die("Could not query the media table in the database: <br />" . mysqli_error($con));
    }
    ?>

    <?php
    if (isset($_POST['favorite'])) {
        $mediaid = $_POST['favorite'];
        $query = "INSERT INTO playlists(playlist,username, mediaid) VALUES('favorites', '$username', '$mediaid')";
        $favs = mysqli_query($con, $query);
    }
    if (isset($_POST['unfavorite'])) {
        $mediaid = $_POST['unfavorite'];
        $query = "DELETE FROM playlists WHERE playlist='favorites' AND username='$username' AND mediaid='$mediaid'";
        $favs = mysqli_query($con, $query);
    }
    if (isset($_POST['new_playlist'])) {
        $new_playlist = $_POST['new_playlist'];
        $query = "SELECT playlist FROM userplaylist WHERE username='$username' and playlist='$new_playlist'";
        $playlist_result = mysqli_query($con, $query);
        $row = mysqli_fetch_row($playlist_result);
        if (!$row) {
            $query = "INSERT into userplaylist(playlist, username) VALUES('$new_playlist', '$username')";
            $new_playlist_result = mysqli_query($con, $query);
        }

        if ($row) {
            echo 'You already have a playlist with that name.';
        }
    }
    if (isset($_POST['add_to_playlist'])) {
        $mediaid = $_POST['mediaAddToPlaylist'];
        $addToPlaylist = $_POST['add_to_playlist'];
        $query = "SELECT * FROM playlists WHERE username='$username' and playlist='$addToPlaylist' and mediaid='$mediaid'";
        $add_to_playlist_result = mysqli_query($con, $query);
        $row = mysqli_fetch_row($add_to_playlist_result);
        if (!$row) {
            $query = "INSERT INTO playlists(playlist,username, mediaid) VALUES('$addToPlaylist', '$username', '$mediaid')";
            $add_to_playlist_result = mysqli_query($con, $query);
        }

        if ($row) {
            echo 'This media is already part of that playlist';
        }
    }
    if (isset($_POST['new_channel'])) {
        $new_channel = $_POST['new_channel'];
        $query = "INSERT into channels(user, channel) VALUES('$username','$new_channel')";
        $channel_result = mysqli_query($con, $query);
        if (!$channel_result) {
            echo mysqli_error($con);
        }
    ?>
        <meta http-equiv="refresh" content="0;url=browse.php">
    <?php
    }

    ?>


    <div class="all_media">
        <?php
        if (!empty($_SESSION['logged_in'])) {
            $username = $_SESSION['username'];
            $qry = "SELECT id from account where username='$username'";
            $res = mysqli_query($con, $qry);
            $row = mysqli_fetch_row($res);
            $id = $row[0];
            $qry = "SELECT * from contacts where (contactid='$id' and isblock='block')";
            $res = mysqli_query($con, $qry);
            $row = mysqli_fetch_row($res);
        } else {
            $row = [NULL];
        }
        if ($row[0] == NULL) {
            while ($result_row = mysqli_fetch_row($result)) {
        ?>

                <div class="mediabox">
                    <?php
                    $mediaid = $result_row[0];
                    $filename = $result_row[1];
                    $filepath = $result_row[2];
                    $type = $result_row[3];
                    if (substr($type, 0, 5) == "image") //view image
                    {
                        echo "<img src='" . $filepath . $filename . "' height=200 width=300/>";
                    } else //view movie
                    {
                    ?>
                        <div>
                            <video width="320" height="240" controls>
                                <source src="<?php echo $result_row[2] . $result_row[1];  ?>" type="video/mp4">
                            </video>
                        </div>
                    <?php } ?>
                    <div>
                        <h4><a href="media.php?id=<?php echo $result_row[0]; ?>" target="_blank"><?php echo $result_row[5]; ?></a></h4>
                    </div>
                    <a href="<?php echo "media_download_process.php?id=" . $result_row[0]; ?>" target="_blank" onclick="javascript:saveDownload(<?php echo $result_row[0]; ?>);">Download</a>
                    <a href="<?php echo $result_row[2] . $result_row[1]; ?>" target="_blank" onclick="javascript:saveDownload(<?php echo $result_row[0]; ?>);">View</a>
                    <br>
                </div>
        <?php }
        } else {
            echo "no such files";
        } ?>
    </div>
</body>

</html>

