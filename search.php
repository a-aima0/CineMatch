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
<section class="related-movies">
    <h2>Recommended Movies:</h2>
    <div class="movie-grid" id="related-movies"></div>
</section>

<!-- Load JavaScript -->
<script src="script.js"></script>
<script>
    //  Function to Fetch Search Results
    async function fetchSearchResults() {
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get("query");

        if (!query) return;

        document.getElementById("search-query").innerText = query;

        try {
            const res = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&query=${query}`);
            const data = await res.json();

            if (data.results.length > 0) {
                displayMovies(data.results, "search-results");
                fetchRelatedMovies(data.results);
            } else {
                document.getElementById("search-results").innerHTML = "<p>No results found.</p>";
            }
        } catch (error) {
            console.error("Error fetching search results:", error);
        }
    }

    // Function to Fetch Related Movies
    async function fetchRelatedMovies(searchResults) {
        const searchMovieIds = new Set(searchResults.map(movie => movie.id));

        try {
            const res = await fetch(`${BASE_URL}/movie/${searchResults[0].id}/recommendations?api_key=${API_KEY}&vote_count.gte=300`);
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


    fetchSearchResults();
</script>

</body>
</html>