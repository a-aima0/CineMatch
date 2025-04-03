<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // redirect to login if not logged in
    exit();
}

require_once('connection.php');

$username = $_SESSION['username'];


$queryUserDetails = "
    SELECT firstName, lastName, username, email
    FROM users
    WHERE username = ?;
";

if ($stmt = $conn->prepare($queryUserDetails)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("Error: no user details found for user");
    }
    $stmt->close();
} else {
    die("Error: sql query failed");
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineMatch - <?php echo htmlspecialchars($user['username']); ?> </title>
    <link rel="stylesheet" href="profilestyle.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>

<?php include_once('header.php'); ?>

<div class="page">
    <div class="profile">
        <div class="profile-pic-container">
            <img class="profile-pic" src="assets/profilepic.png" alt="Profile Picture">
        </div>
        <div class="profile-info">
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstName']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastName']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

            <a href="#" class="change-password">Change Password</a>
            <a href="#" class="delete-account">Delete Account</a>
        </div>
        <div class="logout-button" >
            <a href="logout.php">Logout</a>
        </div>
    </div>

<!--    <div class="watchlist-container">-->
<!--        <h1 class="watchlist-title">Watchlist</h1>-->
<!--        <div class="watchlist-wrapper">-->
<!--            <i class="bx bx-chevron-left arrow left-arrow"></i>-->
<!--            <div class="watchlist">-->
<!--                <div class="watchlist-item">-->
<!--                    <img class="watchlist-item-img" src="assets/film2.jpg" alt="">-->
<!--                </div>-->
<!--                <div class="watchlist-item">-->
<!--                    <img class="watchlist-item-img" src="assets/film2.jpg" alt="">-->
<!--                </div>-->
<!---->
<!--            </div>-->
<!--            <i class="bx bx-chevron-right arrow right-arrow"></i>-->
<!--        </div>-->
<!--    </div>-->
</div>



<script src="act.js"></script>
<script src="script.js"></script>






</body>
</html>
