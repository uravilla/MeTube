<?php
session_start();

include_once "functions.php";

?>
<!-- Add Groups in the MeTube application so that they can you interact with common users and share your thoughts on particular topic -->

<head>
  <title>Add Group</title>
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

  <br><br>
  <?php
  $username = $_SESSION['username'];

  if (isset($_POST['submit'])) {
    if ($_POST['groupname'] == "") {
      $fmsg = "Please enter a Group name.";
    }
    if ($_POST['topic'] == "") {
      $fmsg = "Please enter the Topic to be discussed.";
    } else {
      $groupname = $_POST['groupname'];
      $topic = $_POST['topic'];
      $discussion = $_POST['discussion'];
      $check = addGroup($_SESSION['username'], $groupname, $topic, $discussion);

      if ($check == 1) {
        $fmsg = "" . $groupname . " already exists";
      } else if ($check == 2) {
        $fmsg = "Some other error.";
      } else if ($check == 0) {
        $smsg =  "Group created successfully";
      }
    }
  }
  
  ?>

  <h3>Create Group</h3>
  <br>
  <form method="post" action="<?php echo "add_group.php"; ?>">

    <table width="100%">
      <tr>
        <td width="10%">Group Name:</td>
        <td width="90%"><input class="text" type="text" name="groupname" maxlength="20"><br /></td>
      </tr>
      <tr>
        <td width="10%">Topic:</td>
        <td width="90%"><input class="text" type="text" name="topic" maxlength="20"><br /></td>
      </tr>
      <tr>
        <td width="10%">Discussion:</td>
        <td width="90%"><input class="text" type="text" name="discussion" maxlength="20"><br /></td>
      </tr>
      <tr>
        <td><input name="submit" type="submit" value="Submit"><br /></td>
      </tr>
    </table>
  </form>
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
