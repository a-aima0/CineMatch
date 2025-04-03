<?php
session_start();
require_once('connection.php');

//// Check if the user is already logged in, redirect to dashboard if true
//if (isset($_SESSION['username'])) {
//    header("Location: homepage.php");
//    exit();
//}

$error_message ="";

// If parameters username and password not properly set, redirect to home page
if (!empty($_POST['username']) && !empty($_POST['password'])) {

    // A separate file to hide login details
    include 'connection.php';

    // username and password sent from form
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT username, password FROM users WHERE username = '$username' and password = '$password'";

    // run Select SQL query
    $results = $conn->query($query);

    $count = $results->num_rows;

    // close connection after executing the query
    $conn->close();

    // if result matched given username and password, there must be 1 row
    if ($count == 1) {
        $_SESSION["username"] = $username;


        header("location: home.php");
        die();
    } else {
        $error_message = "Invalid username or password";
    }
}
?>


<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width-device-width,
         initial-scale=1">
    <title>CineMatch - Login</title>
    <link rel="stylesheet" href="loginstyle.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="login-page">
<?php if (!empty($error_message)) { ?>
    <p><?= $error_message ?></p>
<?php } ?>

<div class="container">
    <div class="leftBox"></div>
    <div class="wrapper">
        <form action="login.php" method="POST">
            <h1>Welcome to Cinematch</h1>
            <div class="input-box">
                <input type="text" placeholder="Username"  name="username"required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Password"  name="password"required>
                <i class='bx bxs-lock-alt' ></i>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox">Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <input type="submit" class="btn" value="Login">
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>
</div>
</body>
</html>