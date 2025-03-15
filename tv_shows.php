<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");  // Redirect to login if not logged in
    exit();
}

require_once('connection.php'); // Include your database connection


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Shows - CineMatch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include_once('header.php'); ?>
    <!-- TV Shows Section -->
    <section class="movie-grid">
        <h2>Popular TV Shows</h2>
        <div class="grid" id="tv-show-list"></div>
    </section>

    <script src="script.js"></script>
    <script>
        async function fetchTVShows() {
            const res = await fetch(`${BASE_URL}/discover/tv?api_key=${API_KEY}&language=en-US&vote_count.gte=300`);
            const data = await res.json();
            displayMovies(data.results, "tv-show-list");
        }

        fetchTVShows();
    </script>

</body>
</html>
