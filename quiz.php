<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
    <title>CineMatch - Quiz</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

</head>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<body>

    <?php include_once('header.php'); ?>

    <div id="sliders">
        <label for="genre_slider">Genre Importance:</label>
        <input type="range" id="genre_slider" name="genre_weight" min="0" max="1" step="0.1" value="0.5"><br>

        <label for="rating_slider">Rating Importance:</label>
        <input type="range" id="rating_slider" name="rating_weight" min="0" max="1" step="0.1" value="0.5"><br>

        <label for="recency_slider">Recency Importance:</label>
        <input type="range" id="recency_slider" name="recency_weight" min="0" max="1" step="0.1" value="0.5"><br>

        <label for="popularity_slider">Popularity Importance:</label>
        <input type="range" id="popularity_slider" name="popularity_weight" min="0" max="1" step="0.1" value="0.5"><br>

        <label for="keywords_slider">Keyword Relevance:</label>
        <input type="range" id="keywords_slider" name="keyword_weight" min="0" max="1" step="0.1" value="0.5"><br>

        <label for="collaborative_slider">Collaborative Filtering Weight:</label>
        <input type="range" id="collaborative_slider" name="collaborative_weight" min="0" max="1" step="0.1" value="0.5"><br>
    </div>

    <div id="quiz">
        <label for="genre_question">What's your favourite genre?</label>
        <select id="genre_question" name="genre">
            <option value="action">Action</option>
            <option value="comedy">Comedy</option>
            <option value="drama">Drama</option>
            <option value="horror">Horror</option>
            <option value="romance">Romance</option>
            <option value="thriller">Thriller</option>
        </select><br>

        <label for="weather_question">What is the weather like today?</label>
        <select id="weather_question" name="weather">
            <option value="sunny">Sunny</option>
            <option value="rainy">Rainy</option>
            <option value="cloudy">Cloudy</option>
            <option value="snowy">Snowy</option>
        </select><br>

        <label for="mood_question">How are you feeling today?</label>
        <select id="mood_question" name="mood">
            <option value="happy">Happy</option>
            <option value="sad">Sad</option>
            <option value="excited">Excited</option>
            <option value="relaxed">Relaxed</option>
        </select><br>

        <label for="company_question">Who are you watching the movie with?</label>
        <select id="company_question" name="company">
            <option value="alone">Alone</option>
            <option value="family">Family</option>
            <option value="friends">Friends</option>
            <option value="partner">Partner</option>
        </select><br>

        <button type="submit" id="submitQuiz">Get Recommendations</button>
    </div>

    <div id="results-container"></div>


    <script src="script.js"></script>
</body>
</html>

