<?php
session_start();

include_once "functions.php";

?>

<head>
  <title>Add Contact</title>
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
  $username = $_SESSION['username'];

  if (isset($_POST['submit'])) {
    if ($_POST['contactname'] == "") {
      $fmsg = "Please enter a contact name.";
    } else {
      $contactname = $_POST['contactname'];
      $relation = $_POST['relation'];
      $check = addContact($_SESSION['username'], $contactname, $relation);

      if ($check == 1) {
        $fmsg = "User " . $_POST['contactname'] . " not found.";
      } elseif ($check == 2) {
        $fmsg = "You already have " . $contactname . " as a contact.";
      } else if ($check == 3) {
        $fmsg = "Some other error.";
      } else if ($check == 4) {
        $fmsg = "User blocked you, cannot add";
      } else if ($check == 0) {
        $smsg = "Contact created successfully";
        $qry = "INSERT INTO conversations(conversationid,userA, userB) VALUES(NULL,'$username', '$contactname')";
        $res = mysqli_query($con, $qry);
        if (!$res) {
          echo "error";
          echo mysqli_error($con);
        }
      }
    }
  }



  ?> <br><br>
  <h3>Add Contact</h3>
  <br>
  <form method="post" action="<?php echo "add_contact.php"; ?>">

    <table width="100%">
      <tr>
        <td width="15%">Contact Username:</td>
        <td width="90%"><input class="text" type="text" name="contactname" maxlength="15"><br /></td>
      </tr>
      <tr>
        <td width="15%">Relation:</td>
        <td width="90%"><select name="relation">
            <option value="none">None</option>
            <option value="family">Family</option>
            <option value="friend">Friend</option>
            <option value="favorite">Favorite</option>
          </select><br /></td>
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