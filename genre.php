<?php
session_start();

// check if the user is logged in
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
    <title>Genre Movies</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include_once('header.php'); ?>

    <section class="genre-movies">
        <h2 id="genre-title">Movies</h2>
        <div class="movie-grid" id="movie-list"></div>
    </section>

    <script src="script.js"></script>
    <script>

        async function fetchMoviesByGenre() {
            const urlParams = new URLSearchParams(window.location.search);
            const genreId = urlParams.get("genre_id");
            const genreName = urlParams.get("genre_name");

            if (!genreId) return;

            document.getElementById("genre-title").innerText = `Genre: ${genreName}`;

            const res = await fetch(`${BASE_URL}/discover/movie?api_key=${API_KEY}&with_genres=${genreId}&language=en-US&vote_count.gte=300`);
            const data = await res.json();
            displayMovies(data.results);
        }
        function displayMovies(movies) {
            const movieGrid = document.getElementById("movie-list");
            movieGrid.innerHTML = "";

            movies.forEach(movie => {
                const movieLink = document.createElement("a");
                movieLink.href = `movie.php?movie_id=${movie.id}`;
                movieLink.classList.add("movie-link");

                const movieCard = document.createElement("div");
                movieCard.classList.add("movie-card");
                movieCard.innerHTML = `
            <img src="${IMG_PATH + movie.poster_path}" alt="${movie.title}">
            <h4>${movie.title}</h4>
<button class="watchlist-btn" onclick="addToWatchlist(${movie.id}, '${movie.title || movie.name}', '${IMG_PATH + movie.poster_path}')">⭐ Add to Watchlist</button>
        `;

                movieLink.appendChild(movieCard);
                movieGrid.appendChild(movieLink);
            });
        }



        fetchMoviesByGenre();
    </script>

</body>
</html>
