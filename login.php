<?php
session_start();
require_once('connection.php'); // Include your database connection

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

    // Run Select SQL query
    $results = $conn->query($query);

    $count = $results->num_rows;

    // Close connection after executing the query
    $conn->close();

    // If result matched given username and password, there must be 1 row
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineMatch</title>
</head>
<body>

    <?php if (!empty($error_message)) { ?>
        <p><?= $error_message ?></p>
    <?php } ?>

    <h1>CineMatch</h1>
    <form action="login.php" method="POST">
        <label for="username">Username</label>
        <input type="text" placeholder="username" name="username" required/>

        <label for="password">Password</label>
        <input type="password" placeholder="password" name="password" required/>

        <input type="submit" value="Login">
    </form>

    <a href="register.php">Register</a>

</body>
</html>
