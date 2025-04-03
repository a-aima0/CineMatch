<?php
session_start();

// if any errors, use for debugging
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width-device-width,
         initial-scale=1">
    <title>CineMatch - Registration</title>
    <link rel="stylesheet" href="regstyle.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<div class="container">
    <div class="leftBox"></div>
    <div class="wrapper">
        <form action="addUser.php" method="post">
            <h1>Welcome to Cinematch</h1>
            <div class="input-box">
                <input type="text" placeholder="First name" name="firstName" id="firstName" required>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Last name" name="lastName" id="lastName" required>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Enter Email" name="email" id="email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Create Username"  name="username" id="username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Create Password" name="password" id="password" required>
                <i class='bx bxs-lock-alt' ></i>
            </div>
            <div class="remember">
                <label><input type="checkbox">Remember me</label>
            </div>
            <input type="hidden" name="table" value="users"/>
            <button type="submit" class="btn"  id="submit">Create account</button>
        </form>

        <?php
        if (isset($_SESSION['response'])) {
            echo '<p style="color:' . ($_SESSION['response']['success'] ? 'green' : 'red') . ';">' .
                htmlspecialchars($_SESSION['response']['message']) . '</p>';
            unset($_SESSION['response']);
        }
        ?>

        <a href="login.php">Login</a>
    </div>
</div>
</body>
</html>