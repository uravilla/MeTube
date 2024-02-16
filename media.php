<?php
session_start();
include_once "functions.php";
?>
<html>

<head>
    <title>Media</title>
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
            </ul>
        <?php   } ?>

    </nav>

    <br><br>
    <?php
    if (!empty($_SESSION['logged_in'])) {
        $username = $_SESSION['username'];
        $qry = "SELECT id from account where username='$username'";
        $res = mysqli_query($con, $qry);
        $row = mysqli_fetch_row($res);
        $id = $row[0];
        $qry = "SELECT * from contacts where contactid='$id' and isblock='block'";
        $res = mysqli_query($con, $qry);
        $row = mysqli_fetch_row($res);
    } else {
        $row = [];
    }
    if ($row == NULL) {
        if (isset($_GET['id'])) {
            $mediaid = $_GET['id'];
            $qry = "SELECT * from media where mediaid='$mediaid'";
            $res = mysqli_query($con, $qry);
            $media_row = mysqli_fetch_row($res);
            $filename = $media_row[1];
            $filepath = $media_row[2];
            $type = $media_row[3];

            if (isset($_POST['do_comment'])) {
                $username = $_SESSION['username'];
                $comment = $_POST['comment'];
                $qry = "INSERT into comments(id,mediaid,comment,username,commentTime) values(NULL,'$mediaid','$comment','$username',CURRENT_TIME())";
                $res = mysqli_query($con, $qry);
                if ($res) {
                    $smsg = "Comment created sucessfully";
                    header("Location: media.php?id=$mediaid");
                } else {
                    $fmsg = "Comment failed.".mysqli_fetch_error($con);
                }
            }

            if (isset($_POST['del_comment'])) {
                $commentid = $_POST['del_comment1'];
                $res = mysqli_query($con, "DELETE from comments where id='$commentid'");
            }

            if (isset($_POST['submit_rate'])) {
                $username = $_SESSION['username'];
                $rate = $_POST['rate'];
                $r_mediaid = $_POST['mediaid'];
                $qry = "INSERT into ratingdata(rating, mediaid, username) values('$rate','$r_mediaid','$username')";
                $res = mysqli_query($con, $qry);
                if (!$res) {
                    echo "the rating failed" . mysqli_error($con);
                }
            }

            if (substr($type, 0, 5) == 'image') {
                echo "<img src='$filepath$filename' width=\"350px\" height=\"300px\">";
            } else { ?>
                <video width="320" height="240" controls>
                    <source src='<?php echo $filepath . $filename; ?>' type="video/mp4">
                </video>
                <?php }

            $qry = "SELECT allow_rating from media where mediaid='$mediaid'";
            $res = mysqli_query($con, $qry);
            $allw_wrtng_row = mysqli_fetch_row($res);
            $action = "media.php?id=" . $_GET['id'];
            if (!empty($_SESSION['logged_in'])) {
                if ($allw_wrtng_row[0] == 'yes') { ?>
                    
                    <?php $qry = "SELECT AVG(rating) from ratingdata where mediaid='$mediaid'";
                $res = mysqli_query($con, $qry);
                $row = mysqli_fetch_row($res);
                if ($row[0] == "NULL") {
                    $rate = "0";
                } else {
                    $rate = $row[0];
                } ?>
                <br>Rating: <?php echo $rate; ?><br>
                    <form method="post" action="<?php echo $action; ?>">
                        <select name='rate' type="text">
                            <option value="1" selected="selected">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <input type="hidden" name="mediaid" value="<?php echo $mediaid ?>" />
                        <input type="submit" name="submit_rate" value="submit" />
                    </form>
            <?php } ?>
            <?php
                $username = $_SESSION['username'];
                $action = "media.php?id=" . $mediaid;
                $qry = "SELECT COUNT(*) from playlists where playlist='favourite' and mediaid='$mediaid' and username='$username'";
                $res = mysqli_query($con, $qry);
                $fav_row = mysqli_fetch_row($res);
                if ($fav_row[0] == 0) { ?>
                    <form method="post" action="<?php echo $action; ?>">
                        <input type="hidden" name="favourite" value="<?php echo $mediaid; ?>">
                        <input type="submit" value="Favourite">
                    </form><br>
                <?php } else { ?>
                    <form method="post" action="<?php echo $action; ?>">
                        <input type="hidden" name="unfavourite" value="<?php echo $mediaid; ?>">
                        <input type="submit" value="Unfavourite">
                    </form><br>
                <?php }

                $res = mysqli_query($con, "SELECT user from media where mediaid='$mediaid'");
                $row = mysqli_fetch_row($res);
                $user = $row[0];
                $qry = "SELECT COUNT(*) from subscribe where subscribed='yes' and username='$username' and owner='$user'";
                $res = mysqli_query($con, $qry);
                $row = mysqli_fetch_row($res);
                $action = "media.php?id=" . $_GET['id'];
                if ($row[0] == 0) { ?>
                    <form method="post" action="<?php echo $action; ?>">
                        <input type="hidden" name="subscribe1" value="<?php echo $user; ?>" />
                        <input type="submit" name="subscribe" value="Subscribe" />
                    </form><br>
                <?php } else { ?>
                    <form method="post" action="<?php echo $action; ?>">
                        <input type="hidden" name="unsubscribe1" value="<?php echo $user; ?>" />
                        <input type="submit" name="unsubscribe" value="Unubscribe" />
                    </form><br>
                <?php } ?>
                <h3>Add to playlist</h3>
                <?php
                $play_res = mysqli_query($con, "SELECT * from userplaylist where username='$username'");
                ?>
                <form method="post" action="<?php echo $action; ?>">
                    <input type="hidden" name="media_addto_playlst" value="<?php echo $mediaid; ?>" />
                    <select name="opt" type="text">
                        <?php
                        while ($row = mysqli_fetch_array($play_res, MYSQLI_NUM)) { ?>
                            <option value="<?php echo $row[0]; ?>"><?php echo $row[0]; ?></option>
                        <?php } ?>
                    </select>
                    <input type="submit" name="add_to_playlist" value="Submit">
                </form>

                <?php }
            if (!empty($_SESSION['logged_in'])) {
                $username = $_SESSION['username'];
                if (isset($_POST['favourite'])) {
                    $mediaid = $_POST['favourite'];
                    $qry = "INSERT into playlists(playlist, username, mediaid) values('favourite','$username','$mediaid')";
                    $res = mysqli_query($con, $qry);
                    $action = "media.php?id=" . $mediaid;
                ?>
                    <meta http-equiv="refresh" content="0;url=<?php echo $action; ?>"><?php
                                                                                    }

                                                                                    if (isset($_POST['unfavourite'])) {
                                                                                        $mediaid = $_POST['unfavourite'];
                                                                                        $res = mysqli_query($con, "DELETE from playlists where playlist='favourite' and username='$username' and mediaid='$mediaid'");
                                                                                        $action = "media.php?id=" . $mediaid;
                                                                                        ?>
                    <meta http-equiv="refresh" content="0;url=<?php echo $action; ?>"><?php
                                                                                    }

                                                                                    if (isset($_POST['subscribe'])) {
                                                                                        $user = $_POST['subscribe1'];
                                                                                        $action = "media.php?id=" . $mediaid;
                                                                                        $res = mysqli_query($con, "SELECT * from subscribe where username='$username' and owner='$user'");
                                                                                        $row = mysqli_fetch_row($res);
                                                                                        if ($row[0] == NULL && $user != $username) {
                                                                                            $res = mysqli_query($con, "INSERT into subscribe(username, owner, subscribed) values('$username','$user','yes')");
                                                                                        } ?>
                    <meta http-equiv="refresh" content="0;url=<?php echo $action; ?>"><?php
                                                                                    }

                                                                                    if (isset($_POST['unsubscribe'])) {
                                                                                        $user = $_POST['unsubscribe1'];
                                                                                        $action = "media.php?id=" . $mediaid;
                                                                                        $res = mysqli_query($con, "DELETE from subscribe where username='$username' and owner='$user' and subscribed='yes'");
                                                                                        ?>
                    <meta http-equiv="refresh" content="0;url=<?php echo $action; ?>"><?php
                                                                                    }

                                                                                    if (isset($_POST['add_to_playlist'])) {
                                                                                        $mediaid = $_POST['media_addto_playlst'];
                                                                                        $playlist = $_POST['opt'];
                                                                                        $qry = "SELECT * from playlists where username='$username' and mediaid='$mediaid' and playlist='$playlist'";
                                                                                        $res = mysqli_query($con, $qry);
                                                                                        $row = mysqli_fetch_row($res);
                                                                                        if (!$row) {
                                                                                            $qry = "INSERT into playlists(playlist, username, mediaid) values('$playlist','$username','$mediaid')";
                                                                                            $res = mysqli_query($con, $qry);
                                                                                            if ($res) {
                                                                                                echo "Added to the playlist";
                                                                                            } else {
                                                                                                echo "adding to the playlist failed " . mysqli_error($con);
                                                                                            }
                                                                                        } else {
                                                                                            echo 'This already exists in this playlist';
                                                                                        }
                                                                                    }
                                                                                } ?>

            <br> <br>
            <h4>Details</h4>
            <p>Owner: <?php echo $media_row[7]; ?></p>
            <p>Date Uploaded: <?php echo $media_row[10]; ?></p>
            <p>Category: <?php echo $media_row[6]; ?></p>
            <p>Description: <?php echo $media_row[5]; ?></p>
            <a href="media_download_process.php?id=<?php echo $media_row[0]; ?>" target="_blank" onclick="javascript:savedownload(<?php echo $media_row[0]; ?>);">Download</a>
            <?php
            $view_res = mysqli_query($con, "SELECT views from media where mediaid='$media_row[0]'");
            $view_row = mysqli_fetch_row($view_res);
            ?>
            <a href="<?php echo $filepath . $filename; ?>" target='_blank' onclick="<?php $view_row[0] = $view_row[0] + 1; ?>">View</a>

            <?php
            $view_res = mysqli_query($con, "UPDATE media set views='$view_row[0]' where mediaid='$mediaid'");

            $id = $_GET['id'];
            $qry = "SELECT allow_desc from media where mediaid='$id'";
            $disc_row = mysqli_fetch_row(mysqli_query($con, $qry));
            if ($disc_row[0] == 'yes') { ?>
                <br><br>
                <h4>Comments</h4>
                <?php
                $id = $_GET['id'];
                $qry = "SELECT * from comments where mediaid='$id' ORDER BY commenttime";
                $res = mysqli_query($con, $qry);
                ?>
                <table>
                    <tr>
                        <th>User</th>
                        <th>Comment</th>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) { ?>
                        <tr>
                            <td><?php echo $row[3]; ?></td>
                            <td><?php echo $row[2]; ?></td>
                            <?php
                            if (!empty($_SESSION['logged_in'])) {
                                if ($_SESSION['username'] == $row[3]) {
                                    $action = "media.php?id=" . $_GET['id'];
                            ?>
                                    <td>
                                        <form method="post" action="<?php echo $action; ?>">
                                            <input type="hidden" name="del_comment1" value="<?php echo $row[0]; ?>" />
                                            <input type="submit" name="del_comment" value="Delete" />
                                        </form>
                                    </td>
                            <?php }
                            }
                        }
                        echo "</tr>";
                        if (!empty($_SESSION['logged_in'])) {
                            $action = "media.php?id=" . $_GET['id'];
                            ?>
                            <form method="post" action="<?php echo $action; ?>">
                        <tr>
                            <td><input type="text" name="comment" placeholder="New Comment (max 100 characters)" maxlength="100"></td>
                        </tr>
                        <tr>
                            <td><input type="submit" name="do_comment" value="Post"></td>
                        </tr>
                        </form>
                <?php }
                        echo "</table>";
                    }
                    if (isset($smsg)) echo $smsg;
                    if (isset($fmsg)) echo $fmsg;
                } else { ?>
                <meta http-equiv="refresh" content="0;url=media.php?id=" .<?php echo $_GET['id']; ?>>
            <?php } ?>
            <br>
            <h4>Recomendations</h4><br><br>
            <?php
            $arr = array();
            $mediaid = $_GET['id'];
            $qry = "SELECT keyword from keywords where mediaid='$mediaid'";
            $res = mysqli_query($con, $qry);
            while ($row = mysqli_fetch_row($res)) {
                $qry = "SELECT mediaid from keywords where mediaid!='$mediaid' and keyword='$row[0]'";
                $res = mysqli_query($con, $qry);
                while ($res_row = mysqli_fetch_row($res)) {
                    array_push($arr, $res_row[0]);
                    $res1 = mysqli_query($con, "SELECT * from media where mediaid='$res_row[0]'");
                    while ($res1_row = mysqli_fetch_row($res1)) {
                        $filename = $res1_row[1];
                        $filepath = $res1_row[2];
                        $type = $res1_row[3];
                        if (substr($type, 0, 5) == "image") {
                            echo "<img src='" . $filepath . $filename . "' height=200 width=300/>";
                        } else { ?>
                            <video width="320" height="240" controls>
                                <source src="<?php echo $filepath . $filename; ?>" type="video/mp4">
                            </video>
                        <?php } ?>
                        <h4><a href="media.php?id=<?php echo $_GET['id']; ?>" target="_blank"><?php echo $res1_row[4]; ?></a></h4>
        <?php }
                }
            }
        } else {
            echo "no such files found";
        }
        ?>
</body>

</html>