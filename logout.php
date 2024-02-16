<?php
session_start();
?>
<html>

<head>
    <title>Logout</title>
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

    </nav>
    <br><br>
    <?php
    session_destroy();
    echo '<p>You have been logged out. <a href="index.php"> click here to Login</a><p>';
    echo '<p>Redirect to <a href="browse.php">Home Page</a></p>'; ?>
</body>
</html>