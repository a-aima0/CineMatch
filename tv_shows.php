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

    <!-- TV Shows Section -->
    <section class="movie-grid">
        <h2>Popular TV Shows</h2>
        <div class="grid" id="tv-show-list"></div>
    </section>

<script src="script.js"></script>
<script>
    let allTVShows = [];
    let tvGenres = {}; //  Store TV Show genres

    async function fetchTVShows() {
        try {
            // const res = await fetch(`${BASE_URL}/tv/popular?api_key=${API_KEY}`);
            const res = await fetch(`${BASE_URL}/discover/tv?api_key=${API_KEY}&language=en-US&vote_count.gte=300`);

            const data = await res.json();

            allTVShows = data.results;
            displayMovies(allTVShows, "tv-show-list");

            await fetchGenres(); //  Ensure genres are fetched before filtering
            populateYears();
        } catch (error) {
            console.error("Error fetching TV Shows:", error);
        }
    }

    async function fetchGenres() {
        try {
            const res = await fetch(`${BASE_URL}/genre/tv/list?api_key=${API_KEY}&language=en-US`);
            const data = await res.json();

            const genreDropdown = document.getElementById("genre-filter");
            genreDropdown.innerHTML = `<option value="all">All</option>`;

            tvGenres = {}; //  Reset the genre mapping

            data.genres.forEach(genre => {
                tvGenres[genre.id] = genre.name; // Store genre names by ID
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
        let filteredShows = allTVShows;

        const selectedGenre = document.getElementById("genre-filter").value;
        const selectedYear = document.getElementById("year-filter").value;
        const selectedRating = document.getElementById("rating-filter").value;

        if (selectedGenre !== "all") {
            filteredShows = filteredShows.filter(show => show.genre_ids.includes(Number(selectedGenre))); // Ensure genre ID is a number
        }

        if (selectedYear !== "all") {
            filteredShows = filteredShows.filter(show => show.first_air_date && show.first_air_date.startsWith(selectedYear));
        }

        if (selectedRating !== "all") {
            filteredShows = filteredShows.filter(show => show.vote_average >= parseFloat(selectedRating));
        }

        displayMovies(filteredShows, "tv-show-list");
    });

    fetchTVShows();
</script>


</body>
</html>
