<?php

// set the content type to JSON
header('Content-Type: application/json');

$api_key = 'ff024c8b4942e7ebf52baf82685c5249';
$base_url = 'https://api.themoviedb.org/3/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_data = file_get_contents("php://input");
    $quiz_data = json_decode($input_data, true);

    if ($quiz_data === null) {
        echo json_encode(["error" => "Invalid JSON data received."]);
        exit;
    }

    // Extract the quiz data
    $genre = $quiz_data['genre'] ?: '18';
    $weather = $quiz_data['weather'] ?: 'sunny';
    $mood = $quiz_data['mood'] ?: 'happy';
    $company = $quiz_data['company'] ?: 'alone';

    $genre_weight = $quiz_data['genre_weight'];
    $rating_weight = $quiz_data['rating_weight'];
    $recency_weight = $quiz_data['recency_weight'];
    $popularity_weight = $quiz_data['popularity_weight'];
    $keyword_weight = $quiz_data['keyword_weight'];
    $collaborative_weight = $quiz_data['collaborative_weight'];


    $genre_ids = [
        'action' => 28,
        'comedy' => 35,
        'drama' => 18,
        'romance' => 10749,
        'thriller' => 53,
        'horror' => 27,
    ];


    // URL to fetch movies based on genre
    $genre_id = isset($genre_ids[strtolower($genre)]) ? $genre_ids[strtolower($genre)] : 28; // Default to action genre if not found
    $url = $base_url . 'discover/movie?api_key=' . $api_key . '&with_genres=' . $genre_id . '&language=en-US&include_adult=false&page=1';

    $url .= "&sort_by=vote_average.desc";

    $minVoteCount = 400;
    $url .= "&vote_count.gte=$minVoteCount";

    $url .= "&vote_average.gte=6.5";


    $response = file_get_contents($url);


    if ($response === false) {
        echo json_encode(["error" => "Error fetching data from TMDb."]);
        exit;
    }

    $movie_data = json_decode($response, true);

    if (isset($movie_data['results']) && count($movie_data['results']) > 0) {
        $movies = [];
        foreach ($movie_data['results'] as $movie) {
            $movies[] = [
                'title' => $movie['title'],
                'overview' => $movie['overview'],
                'release_date' => $movie['release_date'],
                'poster_path' => 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'],
                'vote_average' => $movie['vote_average'],
            ];
        }

        // return the list of recommended movies as JSON
        echo json_encode(["results" => $movies]);
    } else {
        echo json_encode(["results" => [], "message" => "No recommendations found."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method. Please send a POST request."]);
}
?>
