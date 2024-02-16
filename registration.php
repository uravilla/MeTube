<head>
    <title>Registration</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/main.css">
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
        <a class="navbar-brand" href="browse.php"><img src="img/icon_metube.png" style="width:63px;" alt="logo"></a>
    </nav>
    <br><br><br><br>
    <?php
    session_start();
    include_once "functions.php";

    if (isset($_POST['submit'])) {
        if ($_POST['usernamereg'] == "" || $_POST['mailreg'] == "" || $_POST['passwordreg'] == "" || $_POST['cpasswordreg'] == "") {
            $login_error = "One or more fields are empty.";
        } else {
            $username = $_POST['usernamereg'];
            $qry = "SELECT * from account where username='$username'";
            $result = mysqli_query($con, $qry);
            $row = mysqli_fetch_row($result);
            if ($row) {
                $login_error = "Username already exists. Please enter other username to Sign up";
            } else {
                $email = $_POST['mailreg'];
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $login_error = "Invalid email format. Please enter valid mailId";
                } else {
                    $pass = $_POST['passwordreg'];
                    $cpass  = $_POST['cpasswordreg'];
                    if (strcmp($pass, $cpass)) {
                        $login_error = "Passwords do not match";
                    } else {
                        
                        $username = $_POST['usernamereg'];
                        $qry = "INSERT INTO account(username, id, password, email) VALUES ('$username', NULL, '$pass','$email')";
                        $result = mysqli_query($con, $qry);
                        if ($result) {
                            $smsg = "User Created Successfully. Please Login to your account";
                            $_SESSION['username'] = $username;
                            $_SESSION['logged_in'] = 1;
                            header("Location: browse.php");
                            exit;
                        } else {
                            $fmsg = "User Registration failed" . mysqli_error($con);
                        }
                    }
                }
            }
        }
    }
    ?>
    <form align="center" method="post" action="registration.php">
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
        <center>
            <h3>Sign Up</h3>
            <br>
        </center>
        <table align=center>
            <tr>
                <td> <label for="usernamereg">Username:</label></td>
                <td> <input type="text" id="usernamereg" name="usernamereg" placeholder="Enter Username"></td>

            </tr>
            <tr></tr>
            <tr>
                <td> <label for="mailreg">Email:</label></td>
                <td><input type="text" id="mailreg" name="mailreg" placeholder="Enter Email"></td>
            </tr>
            <tr>
                <td><label for="passwordreg">Password:</label></td>
                <td> <input type="password" id="passwordreg" name="passwordreg" placeholder="Enter Password"></td>
            </tr>
            <tr>
                <td> <label for="cpasswordreg"> Confirm Password:</label></td>
                <td> <input type="password" id="cpasswordreg" name="cpasswordreg" placeholder="Confirm Password"></td>
            </tr>
            <tr>
        </table>
        <br>
        <input type="submit" value="Submit" , name="submit">
        <input type="reset" value="Reset">
        <br><br>
        <p>Already a user?<a href=index.php>Sign in</a></p>
        <p>Continue as Guest<a href=browse.php>Browse</a></p>
    </form>
    <?php
    if (isset($_POST['submit'])) {
        if (isset($login_error)) {?>
            <div class="alert alert-danger" role="alert">
        <?php echo "<p align='center'><b>" .  $login_error . "</b><p>"; ?>
        </div> 
        <?php }
    }
    ?>
</body>