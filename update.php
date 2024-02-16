<?php
session_start();
include_once "functions.php";
?>
<html>

<head>
    <title>Update Profile</title>
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
    <?php
    $user = $_SESSION['username'];
    $qry = "SELECT * from account where username='$user'";
    $res = mysqli_query($con, $qry);
    $row = mysqli_fetch_row($res);
    $email = $row[3];
    $password = $row[2];

    if (isset($_POST['update_pass'])) {
        if ($_POST['email'] == "" || $_POST['old_password'] == "" || $_POST['new_password'] == "" || $_POST['confirm_password'] == "") {
            $update_error = "One or more fileds are empty. Please check";
        } else {
            $email = $_POST['email'];
            $old_password = $_POST['old_password'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $update_error = "Email format is incorrect.";
            } else {
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                if ($old_password != $password) {
                    $update_error = "Old password is incorrect.";
                } else {
                    if ($new_password != $confirm_password) {
                        $update_error = "New passwords does not match.";
                    } else {
                        $qry = "UPDATE account set email='$email',password='$new_password' where username='$user'";
                        $res = mysqli_query($con, $qry);
                        if ($res) {
                            $smsg = "Password updated sucessfully!";
                        } else {
                            $fmsg = "Password update failed " . mysqli_error($con);
                        }
                    }
                }
            }
        }
    }

    if (isset($smsg)) {?>

        <div class="alert alert-success" role="alert">
    <?php echo "<p align='center'><b>" .  $smsg . "</b><p>"; ?>
    </div>
    <?php } 
    elseif (isset($fmsg)) {?>
        <div class="alert alert-danger" role="alert">
    <?php echo "<p align='center'><b>" .  $fmsg . "</b><p>"; ?>
    </div>            
    <?php } 
    elseif (isset($update_error)) {?>
        <div class="alert alert-danger" role="alert">
    <?php echo "<p align='center'><b>" .  $update_error . "</b><p>"; ?>
    </div>            
    <?php } 

    if (isset($_POST['delcontact'])) {
        $delusername = $_POST['delcontactv'];
        $res = mysqli_query($con, "SELECT conversationid from conversations where (userA='$user' AND userB='$delusername') OR (userB='$user' AND userA='$delusername')");
        $convid_row = mysqli_fetch_row($res);
        $del_convid = (int)$convid_row[0];
        $qry = "SELECT id FROM account WHERE username='$user'";
        $res = mysqli_query($con, $qry);
        $row = mysqli_fetch_row($res);
        $userid = (int)$row[0];
        $qry = "SELECT id FROM account WHERE username='$delusername'";
        $res = mysqli_query($con, $qry);
        $row = mysqli_fetch_row($res);
        $contactid = (int)$row[0];

        $qry = "DELETE from conversations where conversationid='$del_convid'";
        $res = mysqli_query($con, $qry);
        if (!$res) {
            echo mysqli_error($con);
        }
        $qry = "DELETE from messages where conversationid='$del_convid'";
        $res = mysqli_query($con, $qry);
        if (!$res) {
            echo mysqli_error($con);
        }
        $qry = "DELETE from contacts where userid='$userid' and contactid='$contactid'";
        $res = mysqli_query($con, $qry);
        if (!$res) {
            echo mysqli_error($con);
        }
        $qry = "DELETE from contacts where userid='$contactid' and contactid='$userid'";
        $res = mysqli_query($con, $qry);
        if (!$res) {
            echo mysqli_error($con);
        }
    } ?>
    <br>
    <h2>My Profile</h2>
    <h4>User Info</h4>
    <form method="post" action="update.php">
        Username: <?php echo $_SESSION['username']; ?> <br><br>
        <label for="mail">Email:</label>
        <input type="text" id="mail" name="email" placeholder="Enter Email" /><br><br>
        <label for="old_password">Old Password:</label>
        <input type="password" id="old_password" name="old_password" placeholder="Current Password" /><br><br>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" placeholder="New Password" /><br><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" /><br><br>
        <input type="submit" value="Update Password" name="update_pass">
        <input type="reset" value="reset" />
    </form>

    <h4>Contacts</h4>
    <?php
    $qry = "SELECT id from account where username='$user'";
    $res = mysqli_query($con, $qry);
    $row = mysqli_fetch_row($res);
    $userid = $row[0];
    $qry = "SELECT username, email, priority from account INNER JOIN contacts on account.id=contacts.contactid where contacts.userid='$userid' ORDER BY priority";
    $res1 = mysqli_query($con, $qry);

    if (!$res) {
        echo "contact failed" . mysqli_error($con);
    } else { ?>
        <table>
            <tr>
                <td>Username</td>
                <td>Email</td>
                <td>Relation</td>
                <td>Message</td>
            </tr>
            <?php
            $username = $_SESSION['username'];
            while ($row1 = mysqli_fetch_row($res1)) {
                $conv_qry = "SELECT conversationid from conversations where (userA='$username' and userB='$row1[0]') or (userA='$row1[0]' and userB='$username')";
                $conv_res = mysqli_query($con, $conv_qry);

                $conv_row = mysqli_fetch_row($conv_res);
                $convid = $conv_row[0];
            ?>
                <tr>
                    <td><?php echo $row1[0]; ?></td>
                    <td><?php echo $row1[1]; ?></td>
                    <td><?php echo $row1[2]; ?></td>
                    <td><a href="message.php?id=<?php echo $convid; ?>" target="_blank">Message</a></td>
                </tr>
                <?php
                if (!empty($_SESSION['logged_in'])) {
                    $qry = "SELECT id from account where username='$user'";
                    $res = mysqli_query($con, $qry);
                    $res_row = mysqli_fetch_row($res);
                    $id = $res_row[0];
                    $qry = "SELECT id from account where username='$row1[0]'";
                    $res = mysqli_query($con, $qry);
                    $res_row = mysqli_fetch_row($res);
                    $contact_id = $res_row[0];
                    $qry = "SELECT COUNT(*) from contacts where isblock='block' and userid='$userid' and contactid='$contact_id'";
                    $favs = mysqli_query($con, $qry);
                    $favs_row = mysqli_fetch_row($favs);
                    if ($favs_row[0] == 0) { ?>
                        <td>
                            <form method="post" action="update.php">
                                <input type="hidden" name="block" value="<?php echo $contact_id; ?>" />
                                <input type="submit" value="Block" /><br>
                            </form>
                        </td>
                    <?php } else { ?>
                        <td>
                            <form method="post" action="update.php">
                                <input type="hidden" name="unblock" value="<?php echo $contact_id; ?>" />
                                <input type="submit" value="Unblock" /><br>
                            </form>
                        </td>
                <?php }
                }
                ?>
                <td>
                    <form method="post" action="update.php">
                        <input type="hidden" value="<?php echo $row1[0]; ?>" name="delcontactv">
                        <input type="submit" value="Delete" name="delcontact" /><br>
                    </form>
                </td>
        <?php }
        } ?>
        </table>
        <p>Click <a href="add_contact.php">here</a> to add a contact by username</p>
        <?php
        if (isset($_POST['block'])) {
            $qry = "SELECT id from account where username='$user'";
            $res = mysqli_query($con, $qry);
            $row = mysqli_fetch_row($res);
            $id = $row[0];
            $contactid = $_POST['block'];
            $qry = "UPDATE contacts set isblock='block' where userid='$id' and contactid='$contactid'";
            $res = mysqli_query($con, $qry);
            echo "<meta http-equiv='refresh' content='0;url='update.php'>";
        }

        if (isset($_POST['unblock'])) {
            $qry = "SELECT id from account where username='$user'";
            $res = mysqli_query($con, $qry);
            $row = mysqli_fetch_row($res);
            $id = $row[0];
            $contactid = $_POST['unblock'];
            $qry = "UPDATE contacts set isblock='unblock' where userid='$id' and contactid='$contactid'";
            $res = mysqli_query($con, $qry);
            echo "<meta http-equiv='refresh' content='0;url='update.php'>";
        } ?>
        <h4>Friends</h4><br>
        <table>
            <tr>
                <td>Username</td>
                <td>Email</td>
                <td>Message</td>
            </tr>
            <?php
            $username = $_SESSION['username'];
            $qry = "SELECT id from account where username='$username'";
            $res = mysqli_query($con, $qry);
            $row = mysqli_fetch_row($res);
            $userid = $row[0];
            $qry = "SELECT username,email from account INNER JOIN contacts on account.id=contacts.contactid where contacts.userid='$userid' and contacts.priority='friend'";
            $res = mysqli_query($con, $qry);
            while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) {
                $conv_qry = "SELECT conversationid  from conversations where (userA='$username' and userB='$row[0]') or (userA='$row[0]' and userB='$username')";
                $conv_res = mysqli_query($con, $conv_qry);
                $conv_row = mysqli_fetch_row($conv_res);
                $conv_id = $conv_row[0];
            ?>
                <tr>
                    <td><?php echo $row[0]; ?></td>
                    <td><?php echo $row[1]; ?></td>
                    <td><a href="message.php?id=<?php echo $conv_id; ?>" target="_blank">Message</a></td>
                    <td>
                        <form method="post" action="update.php">
                            <input type="hidden" name="delcontactv" value="<?php echo $row[0]; ?>" />
                            <input type="submit" value="Delete" name="delcontact" />
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <h4>My media</h4>
        <table width="50%">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category:
                    <form method="post" action="update.php">
                        <select name="type" type="text">
                            <option value="all" selected="selected">ALL</option>
                            <option value="image">Images</option>
                            <option value="video">Videos</option>
                            <option value="audio">Audio</option>
                        </select>
                        <input type="submit" value="Sort" name="sort" />
                    </form>
                </th>
            </tr>
            <?php
            $catqry = "";
            if (isset($_POST['sort'])) {
                $type = $_POST['type'];
                if ($type == 'all') {
                    $catqry = "AND media.category in ('video','audio','image')";
                } elseif ($type == 'image') {
                    $catqry = "AND media.category='image'";
                } elseif ($type == 'video') {
                    $catqry = "AND media.category='video'";
                } elseif ($type == 'audio') {
                    $catqry = "AND media.category='audio'";
                }
            }
            $qry = "SELECT * from media INNER JOIN upload on media.mediaid=upload.mediaid INNER JOIN account on upload.username=account.username where account.username='$user' $catqry";
            $res = mysqli_query($con, $qry);
            if (!$res) {
                die("Could not query the media table. " . mysqli_error($con));
            }
            while ($row = mysqli_fetch_array($res, MYSQLI_NUM)) { ?>
                <tr valign="top">
                    <td>
                        <h3><a href="media.php?id=<?php echo $row[0]; ?>" target="_blank"><?php echo $row[4]; ?></a></h3>
                    </td>
                    <td><?php echo $row[5]; ?></td>
                    <td><?php echo $row[6]; ?></td>
                </tr>
            <?php } ?>
        </table>

        <h4>Groups</h4>
        <table width="50%">
            <tr>
                <th>Group Name</th>
            </tr>
            <?php
            $qry = "SELECT `groupname` from `groups`";
            $res1 = mysqli_query($con, $qry);
            $ind = 0;
            while ($groupname = mysqli_fetch_row($res1)) {
                echo "<tr>";
                if ($groupname != NULL) {
                    echo "<td>";
                    $qry = "SELECT username from groupuser where groupname='$groupname[0]' and username='$user'";
                    $res = mysqli_query($con, $qry);
                    $row = mysqli_fetch_row($res);
                    if ($row != NULL && $row[0] == $user) {
                        $href = "groups.php?id=" . $groupname[0];
                    } else {
                        $href = "update.php";
                    } ?>
                    <a href="<?php echo $href; ?>" target="_blank"><?php echo $groupname[0]; ?></a>
                    </td>
                    <?php
                    $qry = "SELECT username from groupuser where groupname='$groupname[0]' and username='$user'";
                    $res = mysqli_query($con, $qry);
                    $usrname = mysqli_fetch_row($res);
                    if ($usrname != NULL && $usrname[0] == $user) { ?>
                        <td>
                            <form method="post" action="update.php">
                                <input type="hidden" name="leave1" value="<?php echo $groupname[0]; ?>" />
                                <input type="submit" value="Leave" name="leave" />
                            </form>
                        </td>
                    <?php } else { ?>
                        <td>
                            <form method="post" action="update.php">
                                <input type="hidden" name="join1" value="<?php echo $groupname[0]; ?>">
                                <input type="submit" name="join" value="Join" />
                            </form><br>
                        </td>
            <?php }
                }
            } ?>
            </tr>

        </table>
        <?php
        echo "<p> Click <a href='add_group.php'>here</a> to create a new group.</p>";
        ?>
</body>

</html>
<?php
if (isset($_POST['join'])) {
    $groupname = $_POST['join1'];
    $qry = "INSERT into groupuser(groupname, username) values('$groupname','$user')";
    $res = mysqli_query($con, $qry);
    if (!$res) {
        echo "join failed " . mysqli_error($con);
    } ?>
    <meta http-equiv="refresh" content="0;url=update.php">
<?php }

if (isset($_POST['leave'])) {
    $groupname = $_POST['leave1'];
    $qry = "DELETE from groupmessages where groupname='$groupname' and username='$user'";
    $dqry = "DELETE from groupuser where groupname='$groupname' and username='$user'";
    $res = mysqli_query($con, $qry);
    $dres = mysqli_query($con, $dqry);
    if (!$res || !$dres) {
        echo "leave failed. " . mysqli_error($con);
    } ?>
    <meta http-equiv="refresh" content="0;url=update.php"><?php
                                                        }
    ?>