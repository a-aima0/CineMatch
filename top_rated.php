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
    <title>Top Rated Movies - CineMatch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include_once('header.php'); ?>

    <!-- Top Rated Movies Section -->
    <section class="movie-grid">
        <h2>Top Rated Movies</h2>
        <div class="grid" id="top-rated-list"></div>
    </section>

    <script src="script.js"></script>
    <script>
        async function fetchTopRatedMovies() {
            const res = await fetch(`${BASE_URL}/movie/top_rated?api_key=${API_KEY}`);
            const data = await res.json();
            displayMovies(data.results, "top-rated-list");
        }

        fetchTopRatedMovies();
    </script>

</body>
</html>
