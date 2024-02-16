<!DOCTYPE html>
<?php
session_start();
include_once "functions.php";
?>
<html>

<head>
  <title>Subscriptions</title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" type="text/css" href="default.css" />

</head>
<!-- User can subscribe to a particular playlist based on there preferences -->

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
  <h3>Files Uploaded by <?php echo $_GET['id']; ?></h3>
  <br /><br />
  <?php
  $user = $_GET['id'];
  $query = "select * from media where user='$user'";
  $result = mysqli_query($con, $query);
  ?>
  <?php
  while ($row = mysqli_fetch_row($result)) { ?>
    <div class="mediabox">
      <?php
      $mediaid = $row[0];
      $filename = $row[1];
      $filepath = $row[2];
      $type = $row[3];
      if (substr($type, 0, 5) == "image") //view image
      {
        echo "<img src='" . $filepath . $filename . "' height=200 width=300/>";
      } else //view movie
      {
      ?>
        <video width="320" height="240" controls>
          <source src="<?php echo $filepath . $filename; ?>" type="video/mp4">
        </video>
      <?php } ?>
      <div>
        <h4><a href="media.php?id=<?php echo $row[0]; ?>" target="_blank"><?php echo $row[4]; ?></a></h4>
      </div>
      <br>
    </div>

  <?php } ?>

  </div>

</body>

</html>
