<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

require_once('connection.php'); // Include database connection

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

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineMatch - <?php echo htmlspecialchars($user['username']); ?> </title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<body>

<?php include_once('header.php'); ?>

    <h2>User Profile</h2>
    <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstName']); ?></p>
    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastName']); ?></p>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

<script src="script.js"></script>

<div class="logout-button"  style="float:left;">
    <a href="logout.php">Logout</a>
</div>

</body>
</html>
