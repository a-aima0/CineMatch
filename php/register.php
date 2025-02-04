<?php
session_start();

// if any errors, use for debugging
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Registration</h1>
    <form action="addUser.php"  method="POST">
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="firstName" placeholder="First Name" required>

            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" placeholder="Last Name" required>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required >

        <input type="hidden" name="table" value="users"/>
        <button type="submit" id="submit">Add User</button>
    </form>


    <?php
    if (isset($_SESSION['response'])) {
        echo '<p style="color:' . ($_SESSION['response']['success'] ? 'green' : 'red') . ';">' .
            htmlspecialchars($_SESSION['response']['message']) . '</p>';
        unset($_SESSION['response']);
    }
    ?>

    <a href="login.php">Login</a>


</body>
</html>
