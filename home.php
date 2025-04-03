<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");  // redirect to login if not logged in
    exit();
}

require_once('connection.php');


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineMatch - Discover Movies</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<body>

    <?php include_once('header.php'); ?>


    <section class="hero">
        <div id="featured-movies" class="hero-content">
            <h1 id="featured-title">Movie Title</h1>
            <div class="info">
                <span class="badge">NA</span>
                <span id="duration">NA min</span>
                <span id="imdb-rating">IMDB: NA</span>
                <span id="genres">Genre: NA</span>
            </div>
            <p id="description">This is a short movie description...</p>
        </div>
    </section>


    <section class="trending">
        <h2>Trending Movies</h2>
        <div class="trending-container">
            <div class="trending-scroll" id="trending-movies"></div>
        </div>
    </section>


    <section class="movie-grid">
        <h2 id="recommended-for-you">Popular Movies</h2>
        <div class="grid" id="movie-list"></div>
    </section>

    <script src="script.js"></script>
</body>
</html>
