<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");  // Redirect to login if not logged in
    exit();
}

require_once('connection.php'); // Include your database connection

// Get the movie_id from the URL query string
$movie_id = isset($_GET['movie_id']) ? $_GET['movie_id'] : null;

if ($movie_id) {
    // Use the $movie_id to fetch the movie details from your API
    $api_key = 'ff024c8b4942e7ebf52baf82685c5249'; // Replace with your TMDb API key
    $url = "https://api.themoviedb.org/3/movie/{$movie_id}?api_key={$api_key}&language=en-US";

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response
    $movie_data = json_decode($response, true);



    // Check if the data exists and assign variables
    if ($movie_data && isset($movie_data['title'], $movie_data['overview'], $movie_data['poster_path'], $movie_data['release_date'], $movie_data['vote_average'], $movie_data['tagline'])) {
        $title = $movie_data['title'];
        $overview = $movie_data['overview'];
        $poster_path = "https://image.tmdb.org/t/p/w500" . $movie_data['poster_path'];
        $release_date = $movie_data['release_date'];
        $genres = $movie_data['genres'];
        $vote_average = $movie_data['vote_average'];
        $tagline = $movie_data['tagline'];
    } else {
        $title = "Movie not found";
        $overview = "Details are not available.";
        $poster_path = "";
        $release_date = "";
        $vote_average = "";
        $tagline = "";
    }
} else {
    // If no movie_id is provided
    $title = "No movie selected";
    $overview = "Please select a movie to view details.";
    $poster_path = "";
    $release_date = "";
    $vote_average = "";
    $tagline = "";
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineMatch - <?php echo $title; ?></title>
    <link rel="stylesheet" href="moviepage.css">
    <link rel="shortcut icon" href="assets/cinematch_logo.png" type="image/x-icon">
    <script src="https://kit.fontawesome.com/c8e4d183c2.js" crossorigin="anonymous"></script>
    <div class = "center">
</head>
<body>
<?php include_once('header.php'); ?>
<div id="movie-details">
    <section id="main">
        <h1 class="showcase-heading"><?php echo $title; ?></h1>
        <?php echo $tagline; ?>
        <div class = "info">
            <div class="showcase-box">
                <img src="<?php echo $poster_path; ?>" alt="<?php echo $title; ?>" id="img">
                <button class="watchlist-btn"
                        onclick="addToWatchlist(<?php echo $movie_id; ?>, '<?php echo addslashes($title); ?>', '<?php echo $poster_path; ?>')">
                    ⭐ Add to Watchlist
                </button>
            </div>
            <article>
                <p><strong>Release Date:</strong> <?php echo $release_date; ?></p>
                <p><strong>Overview:</strong> <?php echo $overview; ?></p>
                <p><strong>Average Vote:</strong> <?php echo $vote_average; ?></p>
            </article>
        </div>
    </section>



</div>

<script src="script.js"></script>

</body>
</html>
