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
<section class="search-results">
    <h2>Search Results for: <span id="search-query"></span></h2>
    <div class="movie-grid" id="search-results"></div>
</section>
<section class="related-movies">
    <h2>Recommended Movies:</h2>
    <div class="movie-grid" id="related-movies"></div>
</section>

<!-- Load JavaScript -->
<script src="script.js"></script>
<script>
    let allSearchResults = [];

    // Function to Fetch Related Movies
    async function fetchRelatedMovies(searchResults) {
        if (!searchResults.length) return;

        const searchMovieIds = new Set(searchResults.map(movie => movie.id));

        try {
            const res = await fetch(`${BASE_URL}/movie/${searchResults[0].id}/recommendations?api_key=${API_KEY}`);
            const data = await res.json();

            if (data.results.length > 0) {
                // Filter out movies that exist in the search results
                const filteredResults = data.results.filter(movie => !searchMovieIds.has(movie.id));

                if (filteredResults.length > 0) {
                    displayMovies(filteredResults, "related-movies");
                } else {
                    document.getElementById("related-movies").innerHTML = "<p>No related movies found.</p>";
                }
            } else {
                document.getElementById("related-movies").innerHTML = "<p>No related movies found.</p>";
            }
        } catch (error) {
            console.error("Error fetching related movies:", error);
        }
    }

    // Modify fetchSearchResults to pass full searchResults array
    async function fetchSearchResults() {
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get("query");

        if (!query) return;

        document.getElementById("search-query").innerText = query;

        try {
            const res = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&query=${query}`);
            const data = await res.json();

            if (data.results.length > 0) {
                allSearchResults = data.results;
                displayMovies(allSearchResults, "search-results");
                fetchRelatedMovies(allSearchResults); // Pass full search results
            } else {
                document.getElementById("search-results").innerHTML = "<p>No results found.</p>";
            }
            fetchGenres();
            populateYears();
        } catch (error) {
            console.error("Error fetching search results:", error);
        }
    }

    // Run search function when page loads
    fetchSearchResults();


    // Function to Fetch Genres from TMDB API
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

    // Function to Populate Years in Dropdown
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

    // Filtering Function
    document.getElementById("apply-filters").addEventListener("click", function () {
        let filteredMovies = allSearchResults;

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

        displayMovies(filteredMovies, "search-results");
    });

    // Run search function when page loads
    fetchSearchResults();
</script>

</body>
</html>