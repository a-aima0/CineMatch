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
    <title>Random Recommendation - CineMatch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include_once('header.php'); ?>

    <!-- Hero Section for the Random Movie -->
    <section class="hero">
        <div class="hero-content">
            <h1 id="random-title">Loading...</h1>
            <div class="info">
                <span class="badge">HD</span>
                <span id="random-duration"></span>
                <span id="random-imdb-rating"></span>
                <span id="random-genres"></span>
            </div>
            <p id="random-description">Fetching random movie...</p>
        </div>
    </section>

    <script src="script.js"></script>
    <script>
        async function fetchRandomMovieOrTV() {
            const randomType = Math.random() < 0.5 ? "movie" : "tv"; // 50% chance of movie or TV show
            const randomPage = Math.floor(Math.random() * 500) + 1; // Random page from TMDb
            const res = await fetch(`${BASE_URL}/discover/${randomType}?api_key=${API_KEY}&page=${randomPage}&vote_count.gte=300&vote_average.gte=6.5`);
            const data = await res.json();

            if (data.results.length > 0) {
                const randomIndex = Math.floor(Math.random() * data.results.length);
                const movie = data.results[randomIndex];
                displayRandomMovie(movie, randomType);
            }
        }

        function displayRandomMovie(movie, type) {
            document.getElementById("random-title").innerText = movie.title || movie.name;
            // document.getElementById("random-duration").innerText = type === "movie" ? `Duration: ${movie.runtime || "N/A"} min` : "";
            document.getElementById("random-imdb-rating").innerText = `IMDB: ${movie.vote_average}`;
            document.getElementById("random-description").innerText = movie.overview;
            document.getElementById("random-genres").innerText = "Genre: Fetching...";

            // Fetch genre names
            fetch(`${BASE_URL}/genre/${type}/list?api_key=${API_KEY}`)
                .then(res => res.json())
                .then(genreData => {
                    const movieGenres = movie.genre_ids.map(id => genreData.genres.find(g => g.id === id)?.name).join(", ");
                    document.getElementById("random-genres").innerText = `Genre: ${movieGenres}`;
                });

            // Set high-quality background image
            document.querySelector(".hero").style.backgroundImage = `url(${IMG_PATH_HIGH_QUALITY + movie.backdrop_path})`;
        }

        fetchRandomMovieOrTV();
    </script>

</body>
</html>
