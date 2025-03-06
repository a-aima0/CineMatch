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
    <title>Search Results - CineMatch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include_once('header.php'); ?>

    <section class="search-results">
        <h2>Search Results for: <span id="search-query"></span></h2>
        <div class="movie-grid" id="search-results"></div>
    </section>

    <script src="script.js"></script>
    <script>
        async function fetchSearchResults() {
            const urlParams = new URLSearchParams(window.location.search);
            const query = urlParams.get("query");

            document.getElementById("search-query").innerText = query;

            const res = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&query=${query}`);
            const data = await res.json();
            displayMovies(data.results);
        }

        fetchSearchResults();
    </script>

</body>
</html>

<section class="related-movies">
    <h2>Related Movies</h2>
    <div class="movie-grid" id="related-movies"></div>
</section>

<script>
    async function fetchRelatedMovies(movieId) {
        const res = await fetch(`${BASE_URL}/movie/${movieId}/similar?api_key=${API_KEY}`);
        const data = await res.json();
        displayRelatedMovies(data.results);
    }

    function displayRelatedMovies(movies) {
        const relatedGrid = document.getElementById("related-movies");
        relatedGrid.innerHTML = "";

        movies.forEach(movie => {
            const movieCard = document.createElement("div");
            movieCard.classList.add("movie-card");
            movieCard.innerHTML = `
                <img src="${IMG_PATH + movie.poster_path}" alt="${movie.title}">
                <h4>${movie.title}</h4>
            `;
            relatedGrid.appendChild(movieCard);
        });
    }

    async function fetchSearchResults() {
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get("query");

        document.getElementById("search-query").innerText = query;

        const res = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&query=${query}`);
        const data = await res.json();
        displayMovies(data.results);

        // Fetch related movies using the first search result
        if (data.results.length > 0) {
            fetchRelatedMovies(data.results[0].id);
        }
    }

    fetchSearchResults();
</script>
