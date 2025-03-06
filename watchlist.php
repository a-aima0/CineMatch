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
    <title>Watchlist - CineMatch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include_once('header.php'); ?>

    <section class="watchlist">
        <h2>Your Watchlist</h2>
        <div class="movie-grid" id="watchlist"></div>
    </section>

    <script src="script.js"></script>
    <script>
        function loadWatchlist() {
            let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
            const watchlistContainer = document.getElementById("watchlist");
            watchlistContainer.innerHTML = "";

            if (watchlist.length === 0) {
                watchlistContainer.innerHTML = "<p>No movies added to your Watchlist yet.</p>";
                return;
            }

            watchlist.forEach(movie => {
                const movieCard = document.createElement("div");
                movieCard.classList.add("movie-card");
                movieCard.innerHTML = `
                    <img src="${movie.image}" alt="${movie.title}">
                    <h4>${movie.title}</h4>
                    <button class="remove-btn" onclick="removeFromWatchlist(${movie.id})">❌ Remove</button>
                `;
                watchlistContainer.appendChild(movieCard);
            });
        }

        function removeFromWatchlist(id) {
            let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
            watchlist = watchlist.filter(movie => movie.id !== id);
            localStorage.setItem("watchlist", JSON.stringify(watchlist));
            loadWatchlist(); // Refresh watchlist
        }

        loadWatchlist(); // Load watchlist when page opens
    </script>

</body>
</html>
