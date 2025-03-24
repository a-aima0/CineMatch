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
    <title>Movie Quiz</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include_once('header.php'); ?>
<h2 id="quiz-main">Movie Recommendation Quiz</h2>

<div id="genre_questions">

    <label for="genre_question">Select Genre:</label>
    <select id="genre_question">
        <option value="action">Action</option>
        <option value="adventure">Adventure</option>
        <option value="animation">Animation</option>
        <option value="comedy">Comedy</option>
        <option value="crime">Crime</option>
        <option value="documentary">Documentary</option>
        <option value="drama">Drama</option>
        <option value="family">Family</option>
        <option value="fantasy">Fantasy</option>
        <option value="horror">Horror</option>
        <option value="mystery">Mystery</option>
        <option value="romance">Romance</option>
        <option value="science fiction">Science Fiction</option>
        <option value="thriller">Thriller</option>
        <option value="war">War</option>

    </select>

    <label for="weather_question">Weather:</label>
    <select id="weather_question">
        <option value="sunny">Sunny</option>
        <option value="rainy">Rainy</option>
        <option value="snowy">Snowy</option>
    </select>

    <label for="mood_question">Mood:</label>
    <select id="mood_question">
        <option value="happy">Happy</option>
        <option value="sad">Sad</option>
        <option value="excited">Excited</option>
    </select>

    <label for="company_question">Watching With:</label>
    <select id="company_question">
        <option value="alone">Alone</option>
        <option value="friends">Friends</option>
        <option value="family">Family</option>
    </select>

</div>

<div id="sliders">
    <h3>Adjust Weighting</h3>
    <!--<label>Genre Weight: <input type="range" id="genre_slider" min="0" max="1" step="0.1" value="0.5"></label><br>-->
    <label>Rating Weight: <input class="slider" type="range" id="rating_slider" min="0" max="1" step="0.1" value="0.5"></label><br>
    <label>Recency Weight: <input class="slider" type="range" id="recency_slider" min="0" max="1" step="0.1" value="0.5"></label><br>
    <label>Popularity Weight: <input class="slider" type="range" id="popularity_slider" min="0" max="1" step="0.1" value="0.5"></label><br>
    <label>Keywords Weight: <input class="slider" type="range" id="keywords_slider" min="0" max="1" step="0.1" value="0.5"></label><br>
    <!--<label>Collaborative Weight: <input type="range" id="collaborative_slider" min="0" max="1" step="0.1" value="0.5"></label><br>-->
</div>


<button id="get_recs">Get Recommendations</button>

<div id="recommendations">
    <h3>Recommended Movies:</h3>


</div>

<script>
    $(document).ready(function() {
        const genreMap = {
            "action": 28,
            "adventure": 12,
            "animation": 16,
            "comedy": 35,
            "crime": 80,
            "documentary": 99,
            "drama": 18,
            "family": 10751,
            "fantasy": 14,
            "history": 36,
            "horror": 27,
            "music": 10402,
            "mystery": 9648,
            "romance": 10749,
            "science fiction": 878,
            "tv movie": 10770,
            "thriller": 53,
            "war": 10752,
            "western": 37
        };

        $("#get_recs").click(function() {
            let genreText = $("#genre_question").val().toLowerCase();
            let genreID = genreMap[genreText] || "";

            let quizAnswers = {
                genre: genreID,
                weather: $("#weather_question").val(),
                mood: $("#mood_question").val(),
                company: $("#company_question").val(),
                weights: {
                    genre: $("#genre_slider").val(),
                    rating: $("#rating_slider").val(),
                    recency: $("#recency_slider").val(),
                    popularity: $("#popularity_slider").val(),
                    keywords: $("#keywords_slider").val(),
                    collaborative: $("#collaborative_slider").val()
                }
            };

            $.ajax({
                url: "process_quiz.php",
                type: "POST",
                data: JSON.stringify(quizAnswers),
                contentType: "application/json",
                dataType: "json",
                success: function(response) {
                    $("#recommendations").empty();
                    if (response.error) {
                        $("#recommendations").append(`<div class="movie-item">Error: ${response.error}</div>`);
                        return;
                    }

                    response.movies.forEach(movie => {
                        let posterUrl = movie.poster_path ? `https://image.tmdb.org/t/p/w500${movie.poster_path}` : "https://via.placeholder.com/150"; // Ensure full URL for posters

                        $("#recommendations").append(`
                            <div class="movie-item">
                                <p id="quiz-title"><b>${movie.title}</b></p>
                                <img id="quiz-img" src="${posterUrl}" alt="${movie.title}">
                                <p id="quiz-details"><b>IMDB:</b> ${movie.rating}<b> Release:</b> (${movie.release_date})</p>
                                <p id="quiz-genre"><b>Genres:</b> ${movie.genres}</p>
                                <p id="quiz-overview">${movie.overview}</p>
                            </img>
                        `);
                    });
                },

                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                }
            });
        });
    });
</script>
</body>
</html>