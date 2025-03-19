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

    <!-- Filters Section -->
    <section class="filters">
        <label for="genre-filter">Genre:</label>
        <select id="genre-filter">
            <option value="all">All</option>
        </select>

        <label for="year-filter">Year:</label>
        <select id="year-filter">
            <option value="all">All</option>
        </select>

        <label for="rating-filter">Rating:</label>
        <select id="rating-filter">
            <option value="all">All</option>
            <option value="8">8+</option>
            <option value="7">7+</option>
            <option value="6">6+</option>
            <option value="5">5+</option>
        </select>

        <button id="apply-filters">Apply</button>
    </section>

    <!-- Top Rated Movies Section -->
    <section class="movie-grid">
        <h2>Top Rated Movies</h2>
        <div class="grid" id="top-rated-list"></div>
    </section>

<script src="script.js"></script>
<script>
    let allTopRatedMovies = [];

    async function fetchTopRatedMovies() {
        try {
            const res = await fetch(`${BASE_URL}/movie/top_rated?api_key=${API_KEY}`);
            const data = await res.json();

            allTopRatedMovies = data.results;
            displayMovies(allTopRatedMovies, "top-rated-list");

            fetchGenres();
            populateYears();
        } catch (error) {
            console.error("Error fetching top-rated movies:", error);
        }
    }

    async function fetchGenres() {
        try {
            const res = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}&language=en-US`);
            const data = await res.json();

            const genreDropdown = document.getElementById("genre-filter");
            genreDropdown.innerHTML = `<option value="all">All</option>`;

            data.genres.forEach(genre => {
                let option = document.createElement("option");
                option.value = genre.id;
                option.textContent = genre.name;
                genreDropdown.appendChild(option);
            });
        } catch (error) {
            console.error("Error fetching genres:", error);
        }
    }

    function populateYears() {
        const yearDropdown = document.getElementById("year-filter");
        yearDropdown.innerHTML = `<option value="all">All</option>`;
        for (let year = new Date().getFullYear(); year >= 1950; year--) {
            let option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            yearDropdown.appendChild(option);
        }
    }

    document.getElementById("apply-filters").addEventListener("click", function () {
        let filteredMovies = allTopRatedMovies;

        const selectedGenre = document.getElementById("genre-filter").value;
        const selectedYear = document.getElementById("year-filter").value;
        const selectedRating = document.getElementById("rating-filter").value;

        if (selectedGenre !== "all") {
            filteredMovies = filteredMovies.filter(movie => movie.genre_ids.includes(parseInt(selectedGenre)));
        }

        if (selectedYear !== "all") {
            filteredMovies = filteredMovies.filter(movie => movie.release_date.startsWith(selectedYear));
        }

        if (selectedRating !== "all") {
            filteredMovies = filteredMovies.filter(movie => movie.vote_average >= parseFloat(selectedRating));
        }

        displayMovies(filteredMovies, "top-rated-list");
    });

    fetchTopRatedMovies();
</script>


</body>
</html>
