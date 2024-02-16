<!DOCTYPE html>
<html>

<head>
  <title>Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" type="text/css" href="default.css" />
</head>
<!-- User Login page, they can create an account or can login as a guest -->

<body>
  <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
    <a class="navbar-brand" href="browse.php"><img src="img/icon_metube.png" style="width:63px;" alt="logo"></a>
  </nav>
  <div class="body">
    <br><br><br><br><br>
    <center>
      <h1>Welcome to Metube!!</h1>
      <br>
    </center>
    <?php
    session_start();
    include_once "functions.php";
    if (isset($_POST['submit'])) {
      if ($_POST['username'] == "" || $_POST['password'] == "") {
        $login_error = "Please enter both username and password";
      } else {
        $res = user_pass_check($_POST['username'], $_POST['password']);
        if ($res == 1) {
          $login_error = "User " . $_POST['username'] . " not found.";
        } elseif ($res == 2) {
          $login_error = "Please check your password";
        } elseif ($res == 3) {
          $login_error = "User is not registered. Please Sign up";
        } elseif ($res == 0) {
          $_SESSION['username'] = $_POST['username'];
          $_SESSION['logged_in'] = 1;
          header("Location: browse.php");
          exit;
        }
      }
    }
    ?>

    <form align="center" method="post" action="index.php">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" placeholder="Enter Username"><br>
      <br>
      <label for="password">Password: </label>
      <input type="password" id="password" name="password" placeholder="Enter Password">
      <br><br><br>
      <input type="submit" value="Submit" name="submit">
      <input type="reset" value="Reset">
      <br><br>
      <p>Need an account? <a href=registration.php>Sign up</a></p>
      <p>Continue as Guest.. <a href=browse.php>Browse</a></p>
    </form>

    <?php
    if (isset($_POST['submit'])) {
      if (isset($login_error)) {
    ?>

        <div class="alert alert-danger" role="alert">
        <?php echo "<div id=\"login_error\"><p align='center'><b>" . $login_error . "</b><p></div>"; ?>
        </div>
    <?php
      }
    }
    ?>
  </div>
</body>
