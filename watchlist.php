<?php
session_start();
require_once('connection.php');

if (!isset($_SESSION['username'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$username = $_SESSION['username'];

// Fetch user ID from the users table
$userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$result = $userQuery->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["error" => "User not found"]);
    exit();
}

$user_id = $user['id'];

// Detect if the request is an AJAX call
$is_ajax = !empty($_POST['action']);

if ($is_ajax) {
    header('Content-Type: application/json'); // Set response type to JSON
    $action = $_POST['action'];

    if ($action === 'add') {
        $movie_id = $_POST['movie_id'];
        $title = $_POST['title'];
        $image = $_POST['image'];

        // Check if movie is already in watchlist
        $checkStmt = $conn->prepare("SELECT id FROM watchlist WHERE user_id = ? AND movie_id = ?");
        $checkStmt->bind_param("ii", $user_id, $movie_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows === 0) {
            // Insert the movie if not already added
            $stmt = $conn->prepare("INSERT INTO watchlist (user_id, movie_id, movie_title, movie_image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $user_id, $movie_id, $title, $image);
            if ($stmt->execute()) {
                echo json_encode(["success" => "Movie added to watchlist"]);
            } else {
                echo json_encode(["error" => "Failed to add movie"]);
            }
        } else {
            echo json_encode(["error" => "Movie already in watchlist"]);
        }
        exit();
    }

    if ($action === 'remove') {
        $movie_id = $_POST['movie_id'];

        $stmt = $conn->prepare("DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?");
        $stmt->bind_param("ii", $user_id, $movie_id);
        if ($stmt->execute()) {
            echo json_encode(["success" => "Movie removed from watchlist"]);
        } else {
            echo json_encode(["error" => "Failed to remove movie"]);
        }
        exit();
    }

    if ($action === 'load') {
        $stmt = $conn->prepare("SELECT movie_id, movie_title, movie_image FROM watchlist WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $watchlist = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($watchlist);
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watchlist - CineMatch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include_once('header.php'); ?>

<section class="watchlist">
    <h2>Your Watchlist</h2>
    <div class="movie-grid" id="watchlist"></div>
</section>

<script src="script.js"></script>

</body>
</html>
