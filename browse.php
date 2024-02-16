<!DOCTYPE html>
<?php
session_start();
include_once "functions.php";
?>
<html>

<head>
    <meta charset="utf-8" />
    <title>Metube|Homepage</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="default.css" />
    <script>
        <?php
        if (!empty($_SESSION['logged_in'])) {
            if (isset($_REQUEST['result']) && $_REQUEST['result'] != 0) { ?>
                alert("<?php echo "Upload failed " . upload_error($_REQUEST['result']); ?>");
        <?php }
        } ?>
    </script>
    <script type="text/javascript">
        function saveDownload(id) {
            $.post("media_download_process.php", {
                    id: id,
                },
                function(message) {});
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

    <h3>Browse<h3>
            <br>
            <?php
            if (!empty($_SESSION['logged_in'])) {
                $username = $_SESSION['username'];
                if (isset($_POST['delchannel'])) {
                    $cname = $_POST['delchannel'];
                    $qry = "DELETE from channels where user='$username' and channel='$cname'";
                    $result = mysqli_query($con, $qry);
                    if (!$result) {
                        echo mysqli_error($con);
                    }
                }
            ?>
                <h4>Add new playlist</h4>
                <form method="post" action="browse.php">
                    <input name="newplaylist" type="text" placeholder="new playlist" maxlength="25">
                    <input type="submit" value="Submit" name="plysubmit">
                </form>
                <br>
                <h4><a href="manage_playlists.php?user=<?php echo $username; ?>" target="_blank">Manage Playlists</a></h4>
                <br><br>
                <h4>Add new Channel</h4>
                <form method="post" action="browse.php">
                    <?php
                    $qry = "select username from account where username != '$username' and username not in (select channel from channels where user='$username')";
                    $chnl_res = mysqli_query($con, $qry);
                    if (!$chnl_res) {
                        echo mysqli_error($con);
                    }
                    ?>
                    <select name="newchannel">
                        <?php
                        while ($chnl_row = mysqli_fetch_row($chnl_res)) { ?>
                            <option value="<?php echo $chnl_row[0]; ?>"><?php echo $chnl_row[0]; ?> </option><br>;
                        <?php } ?>
                    </select>
                    <input type="submit" value="Submit">
                </form>
                <br>
                <h5>My Channels</h5>
                <?php
                $qry = "SELECT channel from channels where user='$username'";
                $result = mysqli_query($con, $qry); ?>
                <table>
                    <?php
                    while ($chnl_row = mysqli_fetch_row($result)) { ?>
                        <tr>
                            <td><?php echo $chnl_row[0]; ?></td>
                            <td>
                                <form method="post" action="browse.php">
                                    <input type="hidden" name="delchannel" value="<?php echo $chnl_row[0]; ?>">
                                    <input type="submit" value="Delete">
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else {
                echo "Please login to upload the media";
            }
            if (isset($_POST['channel'])) {
                $channel = $_POST['channel'];
                if ($channel == 'all') {
                    $chnlqry = "SELECT mediaid from media";
                } elseif ($channel == "mine") {
                    $chnlqry = "SELECT mediaid from media where user='$username'";
                } else {
                    $chnlqry = "SELECT mediaid from media where user='$channel'";
                }
            } else {
                $chnlqry = "SELECT mediaid from media";
            }

            if (isset($_POST['type'])) {
                $type = $_POST['type'];
                if ($type == "all") {
                    $typeqry = "SELECT mediaid from media";
                } elseif ($type == "image") {
                    $typeqry = "SELECT mediaid from media where category='image'";
                } elseif ($type == "video") {
                    $typeqry = "SELECT mediaid from media where category='video'";
                } elseif ($type == "audio") {
                    $typeqry = "SELECT mediaid from media where category='audio'";
                }
            } else {
                $typeqry = "SELECT mediaid from media";
            }


            if (isset($_POST['playlist'])) {
                $playlist = $_POST['playlist'];
                if ($playlist == "all") {
                    $plylstqry = "SELECT mediaid from media";
                } else {
                    $plylstqry = "SELECT media.mediaid from media INNER JOIN playlists on media.mediaid=playlists.mediaid where playlists.playlist='$playlist' and username='$username'";
                }
            } else {
                $plylstqry = "SELECT mediaid from media";
            }

            $bigq = "SELECT media.mediaid from media where media.mediaid in ($chnlqry) and media.mediaid in ($typeqry) and media.mediaid in ($plylstqry)";
            $allq = "SELECT * from media where media.mediaid in ($bigq)";

            if (isset($_POST['order'])) {
                $order = $_POST['order'];
                if ($order == "recent") {
                    $allq = "SELECT * from media where media.mediaid in ($bigq) ORDER BY time DESC";
                }
                if ($order == "name") {
                    $allq = "SELECT * from media where media.mediaid in ($bigq) ORDER BY title";
                }
                if ($order == "size") {
                    $allq = "SELECT * from media where media.mediaid in ($bigq) ORDER BY size";
                }
                if ($order == "viewed") {
                    $allq = "SELECT * from media where media.mediaid in ($bigq) ORDER BY views DESC";
                }
            }

            $result = mysqli_query($con, $allq);
            if (!$result) {
                echo mysqli_error($con);
            }


            if (isset($_POST['newchannel'])) {
                $newchnl = $_POST['newchannel'];
                $qry = "INSERT into channels(user, channel) values('$username','$newchnl')";
                $chnl_res = mysqli_query($con, $qry);
                if (!$chnl_res) {
                    echo mysqli_error($con);
                }
                echo "<meta http-equiv=\"refresh\" content=\"0;url=browse.php\">";
            }


            if (isset($_POST['newplaylist'])) {
                $newplaylist = $_POST['newplaylist'];
                $qry = "SELECT playlist from userplaylist where username='$username' and playlist='$newplaylist'";
                $playlist_res = mysqli_query($con, $qry);
                $row = mysqli_fetch_row($playlist_res);
                if (!$row) {
                    $qry = "INSERT into userplaylist(playlist, username) values('$newplaylist','$username')";
                    $new_playlst_res = mysqli_query($con, $qry);
                } else {
                    echo 'You already have this playlist';
                }
            }
            ?>

            <h4>Filters</h4>
                    <table>
                        <tr>
                            <td>Category</td>
                            <?php if (!empty($_SESSION['logged_in'])) { ?>
                                <td>Playlist</td>
                                <td>Channel</td>
                                <td>Order By</td> <?php } ?>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <form method="post" action="browse.php">
                                    <select name="type" type="type">
                                        <option value="all" selected="selected">All</option>
                                        <option value="image">Images</option>
                                        <option value="video">Videos</option>
                                        <option value="audio">Audio</option>
                                    </select>
                            </td>
                            <?php
                            if (!empty($_SESSION['logged_in'])) { ?>
                                <td>
                                    <form method="post" action="browse.php">
                                        <?php
                                        $qry = "SELECT * from userplaylist where username='$username'";
                                        $playlist_res = mysqli_query($con, $qry); ?>
                                        <select name="playlist">
                                            <option value="all" selected="selected">All</option>
                                            <option value="favourite">Favourites</option>
                                            <?php
                                            while ($playlst_row = mysqli_fetch_row($playlist_res)) { ?>
                                                <option value="<?php echo $playlst_row[0]; ?>"><?php echo $playlst_row[0]; ?> </option><br>
                                            <?php } ?>
                                        </select>
                                </td>
                                <td>
                                    <form method="post" action="browse.php">
                                        <?php
                                        $qry = "SELECT channel from channels where user='$username'";
                                        $chnl_res = mysqli_query($con, $qry); ?>
                                        <select name="channel">
                                            <option value="all" selected="selected">Any</option>
                                            <option value="mine">My Channel</option>
                                            <?php
                                            while ($chnl_row = mysqli_fetch_row($chnl_res)) { ?>
                                                <option value="<?php echo $chnl_row[0]; ?>"><?php echo $chnl_row[0]; ?> </option><br>
                                            <?php } ?>
                                        </select>
                                </td>
                                <td>
                                    <form method="post" action="browse.php">
                                        <select name="order" type="text">
                                            <option value="recent" selected="selected">Most Recent</option>
                                            <option value="name">Name</option>
                                            <option value="size">Size</option>
                                            <option value="viewed">Most viewed</option>
                                        </select>
                                </td>
                            <?php } ?>
                            <td>
                                <input type="submit" value="Submit" name="options">
                                </form>
                            </td>
                        </tr>
                    </table>
                    <div class="allmedia">
                        <?php
                        error_reporting(E_ALL & ~E_NOTICE ^ E_WARNING);               
                        while ($result_row = mysqli_fetch_row($result)) //print the results 
                        { $media_print = 1;
                            if (empty($_SESSION['logged_in'])) {
                                $username = "NULL";
                            }
                            $qry = "SELECT id from account INNER JOIN media on account.username=media.user where mediaid='$result_row[0]'";
                            $res = mysqli_query($con, $qry);
                            $res_row = mysqli_fetch_row($res);
                            $id = $res_row[0];
                            $qry = "SELECT id from account where username='$username'";
                            $res = mysqli_query($con, $qry);
                            $res_row = mysqli_fetch_row($res);
                            if ($res_row != NULL) {
                                $contactid = $res_row[0];
                                $qry = "SELECT isblock from contacts where userid='$id' and contactid='$contactid'";
                                $res = mysqli_query($con, $qry);
                                $res_row = mysqli_fetch_row($res);
                                //$isblock=$res_row[0];
                                if ($res_row != NULL && $res_row[0] == 'block') {
                                    $media_print = 0;
                                }
                            }
                            $qry = "SELECT user from media where mediaid='$result_row[0]'";
                            $user_share_res = mysqli_query($con, $qry);
                            $user_share_row = mysqli_fetch_row($user_share_res);
                            if (($result_row[9] == "me") && ($user_share_row[0] != $username)) {
                                $media_print = 0;
                            }
                            $qry = "SELECT priority from contacts INNER JOIN account on account.id=contacts.contactid where account.username='$username'";
                            $user_share_res = mysqli_query($con, $qry);
                            $user_share_row1 = mysqli_fetch_row($user_share_res);
                            if (($result_row[9] == "friends") && ((is_null($user_share_row1[0]) || (!is_null($user_share_row1[0]) && $user_share_row1[0] != "friend")))) {
                                if($user_share_row[0] != $username) {
                                    $media_print = 0;
                                }
                            }
                            if (($result_row[9] == "family") && ((is_null($user_share_row1[0]) || (!is_null($user_share_row1[0]) && $user_share_row1[0] != "family")))) {
                                if ($user_share_row[0] != $username) {
                                    $media_print = 0;
                                }
                            }
                            if (($result_row[9] == "favourites") && ((is_null($user_share_row1[0]) || (!is_null($user_share_row1[0]) && $user_share_row1[0] != "favorite")))) {
                                if ($user_share_row[0] != $username) {
                                    $media_print = 0;
                                }
                            } ?>
                            <?php if($media_print == 1) {?>
                            <div class="mediabox">
                            
                                <?php
                                $mediaid = $result_row[0];
                                $filename = $result_row[1];
                                $filepath = $result_row[2];
                                $type = $result_row[3];
                                if (substr($type, 0, 5) == "image") {
                                    echo "<img src='" . $filepath . $filename . "' height=240 width=320/>";
                                } else { ?>
                                    <video width="320" height="240" controls>
                                        <source src="<?php echo $filepath . $filename; ?>" type="video/mp4">
                                    </video>
                                <?php } ?>
                                <h4><a href="media.php?id=<?php echo $result_row[0]; ?>" target="_blank"><?php echo $result_row[4]; ?></a></h4>
                                <form method="post" action="browse.php">
                                    Rating:
                                    <?php
                                    $qry = "SELECT AVG(rating) from ratingdata where mediaid='$result_row[0]'";
                                    $rate_res = mysqli_query($con, $qry);
                                    $rate_row = mysqli_fetch_row($rate_res);
                                    if ($rate_row[0] == NULL) {
                                        echo "0";
                                    } else {
                                        echo $rate_row[0];
                                    }
                                    ?>
                                </form>
                                Views:
                                <?php
                                $qry = "SELECT views from media where mediaid='$result_row[0]'";
                                $rate_res = mysqli_query($con, $qry);
                                $rate_row = mysqli_fetch_row($rate_res);
                                if ($rate_row[0] == NULL) {
                                    echo "0";
                                } else {
                                    echo $rate_row[0];
                                }
                                ?>

                            </div>
                        <?php } }?>
                      
                    </div>
                            
</body>

</html>