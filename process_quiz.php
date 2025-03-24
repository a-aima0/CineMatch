<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

$tmdb_api_key = "ff024c8b4942e7ebf52baf82685c5249";
// Fetch the genre list from TMDB
$genre_url = "https://api.themoviedb.org/3/genre/movie/list?api_key=$tmdb_api_key&language=en-US";
$genre_response = file_get_contents($genre_url);
$genre_data = json_decode($genre_response, true);

// Create a genre map
$genre_map = [];
foreach ($genre_data["genres"] as $genre) {
    $genre_map[$genre["id"]] = $genre["name"];
}

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    die(json_encode(["error" => "Invalid JSON payload"]));
}

// Extract user input
$genre = $input["genre"] ?? "";
$weather = $input["weather"] ?? "";
$mood = $input["mood"] ?? "";
$company = $input["company"] ?? "";
$weights = $input["weights"] ?? [];

// Convert weights to float values
$weights = [
    "genre" => isset($weights["genre"]) ? (float)$weights["genre"] : 0.5,
    "rating" => isset($weights["rating"]) ? (float)$weights["rating"] : 0.5,
    "recency" => isset($weights["recency"]) ? (float)$weights["recency"] : 0.5,
    "popularity" => isset($weights["popularity"]) ? (float)$weights["popularity"] : 0.5,
    "keywords" => isset($weights["keywords"]) ? (float)$weights["keywords"] : 0.5,
    "collaborative" => isset($weights["collaborative"]) ? (float)$weights["collaborative"] : 0.5
];

// Validate genre ID
if (empty($genre)) {
    die(json_encode(["error" => "Invalid genre selection"]));
}

// Fetch multiple pages from TMDB API
$movies_data = [];
for ($page = 1; $page <= 10; $page++) {  // Fetch from 10 pages
    $tmdb_url = "https://api.themoviedb.org/3/discover/movie?api_key=$tmdb_api_key&with_genres=$genre&sort_by=vote_count.desc&page=$page";
    $response = file_get_contents($tmdb_url);
    if ($response) {
        $data = json_decode($response, true);
        if (!empty($data["results"])) {
            $movies_data = array_merge($movies_data, $data["results"]);
        }
    }
}

if (empty($movies_data)) {
    die(json_encode(["error" => "No movies found for genre ID $genre"]));
}

// Shuffle movies to add diversity
shuffle($movies_data);

// Dummy data for keyword-based matching (replace with a proper dataset)
$keyword_map = [
    "sunny" => ["adventure", "light-hearted", "feel-good"],
    "rainy" => ["mystery", "thriller", "drama"],
    "snowy" => ["fantasy", "holiday", "animation"],
    "happy" => ["comedy", "family", "romance"],
    "sad" => ["drama", "biopic", "historical"],
    "excited" => ["action", "thriller", "sci-fi"],
    "alone" => ["psychological", "indie", "mystery"],
    "friends" => ["comedy", "action", "horror"],
    "family" => ["animation", "adventure", "fantasy"]
];

// Compute movie scores
$movies = [];
foreach ($movies_data as $movie) {
    $score = 0;

    // Weighted scoring
//    $score += $weights["genre"] * (isset($movie["genre_ids"]) && in_array($genre, $movie["genre_ids"]) ? 1 : 0);
    $score += $weights["rating"] * ($movie["vote_average"] / 10);
    $score += $weights["recency"] * (1 / (2025 - (int)substr($movie["release_date"], 0, 4) + 1));
    $score += $weights["popularity"] * ($movie["popularity"] / 1000);

    // Keyword matching
    $matched_keywords = 0;
    if (!empty($keyword_map[$weather])) {
        foreach ($keyword_map[$weather] as $kw) {
            if (stripos($movie["overview"], $kw) !== false) {
                $matched_keywords++;
            }
        }
    }
    if (!empty($keyword_map[$mood])) {
        foreach ($keyword_map[$mood] as $kw) {
            if (stripos($movie["overview"], $kw) !== false) {
                $matched_keywords++;
            }
        }
    }
    if (!empty($keyword_map[$company])) {
        foreach ($keyword_map[$company] as $kw) {
            if (stripos($movie["overview"], $kw) !== false) {
                $matched_keywords++;
            }
        }
    }
    $score += $weights["keywords"] * ($matched_keywords / 3);

    $genre_priority = 1 - abs(10 - $weights["genre"]) / 10;  // Closer to 10, more weight
    // Store movie with score and poster path
    $movies[] = [
        "title" => $movie["title"],
        "release_date" => $movie["release_date"],
        "score" => $score + ($genre_priority * $weights["genre"] * (isset($movie["genre_ids"]) && in_array($genre, $movie["genre_ids"]) ? 1 : 0)),
        "poster_path" => $movie["poster_path"] ? "https://image.tmdb.org/t/p/w500" . $movie["poster_path"] : null,
        "rating" => $movie["vote_average"],
        "overview" => $movie["overview"],
        "genres" => array_map(function($genre_id) use ($genre_map) {
            return $genre_map[$genre_id] ?? 'Unknown';
        }, $movie["genre_ids"])

    ];


}

// Sort movies by score
usort($movies, function ($a, $b) {
    return $b["score"] <=> $a["score"];
});

// Return top 10 recommendations
echo json_encode(["movies" => array_slice($movies, 0, 15)]);
?>

