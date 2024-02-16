<!DOCTYPE html>
<?php
session_start();
include_once "functions.php";
?>
<html>

<head>
  <title>Word Cloud</title>
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
  $values = [];
  $query = "SELECT distinct keyword,count from keywords";
  $result = mysqli_query($con, $query);
  $totalcount = 0;
  while ($keyword = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $word = $keyword[0];
    $count = $keyword[1];
    $new = array("$word" => "$count");
    $values = array_merge($values, $new);
    $totalcount += $count;
  }
  foreach ($values as $word => $count1) { ?>
    <div style="font-size: <?php
 
                            $per = ($count1 / $totalcount) * 100; 
                            if ($per >= 90 && $per <= 100) {
                              echo "150px; color:blue;";
                            } elseif ($per >= 80 && $per < 90) {
                              echo "135px; color:brown;";
                            } elseif ($per >= 70 && $per < 80) {
                              echo "105px; color:navy;";
                            } elseif ($per >= 60 && $per < 70) {
                              echo "90px; color:maroon;";
                            } elseif ($per >= 50 && $per < 60) {
                              echo "75px; color:white;";
                            } elseif ($per >= 40 && $per < 50) {
                              echo "60px; color:aqua;";
                            } elseif ($per >= 30 && $per < 40) {
                              echo "45px; color:green;";
                            } elseif ($per >= 20 && $per < 30) {
                              echo "30px; color:lime;";
                            } else {
                              echo "15px; color:red;";
                            } ?>"><?php echo $word; ?> </div>
  <?php
  }
  ?>

</body>

</html>