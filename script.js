const API_KEY = "ff024c8b4942e7ebf52baf82685c5249";  
const BASE_URL = "https://api.themoviedb.org/3";
const IMG_PATH = "https://image.tmdb.org/t/p/w500";
const IMG_PATH_HIGH_QUALITY = "https://image.tmdb.org/t/p/original";

// click to go to movie page
document.addEventListener("DOMContentLoaded", () => {
    const movieGrid = document.querySelector(".movie-grid");

    if (movieGrid) {
        movieGrid.addEventListener("click", function(event) {
            console.log("Clicked element:", event.target);

            let movieLink = event.target.closest(".movie-link");

            if (movieLink) {
                event.preventDefault();
                console.log(`Redirecting to: ${movieLink.href}`);
                window.location.href = movieLink.href;
            } else {
                console.log("No valid movie link detected!");
            }
        });
    } else {
        console.error("Movie grid not found!");
    }

});


// display movies function - works for popular movies, tvshow, toprated and search
// does not work for hero, tredning, genre, watchlist, random
function displayMovies(movies, containerId) {
    const movieGrid = document.getElementById(containerId);
    if (!movieGrid) return;

    movieGrid.innerHTML = "";

    movies.slice(0, 32).forEach(movie => {
        const movieCard = document.createElement("div");
        movieCard.classList.add("movie-card");

        movieCard.innerHTML = `
            <a href="movie.php?movie_id=${movie.id}" class="movie-link">
                <img src="${IMG_PATH + movie.poster_path}" alt="${movie.title || movie.name}">
                <h4>${movie.title || movie.name}</h4>
            </a>
<button class="watchlist-btn" onclick="addToWatchlist(${movie.id}, '${movie.title || movie.name}', '${IMG_PATH + movie.poster_path}')">⭐ Add to Watchlist</button>
        `;

        console.log(`Created link: movie.php?movie_id=${movie.id}`);
        movieGrid.appendChild(movieCard);
    });
}


// Fetch Popular Movies (Only on Home Page)
async function fetchRecommendedMovies() {
    if (!document.getElementById("movie-list")) return; // Prevent errors on other pages
    const res = await fetch(`${BASE_URL}/movie/popular?api_key=${API_KEY}&vote_count.gte=300&language=en-US&sort_by=popularity.desc`);

    const data = await res.json();
    displayMovies(data.results, "movie-list");
}

//  Fetch Trending Movies for Home Page Carousel
async function fetchTrendingMovies() {
    if (!document.getElementById("trending-movies")) return; // Only run if on home page
    const res = await fetch(`${BASE_URL}/trending/movie/week?api_key=${API_KEY}&vote_count.gte=300`);
    const data = await res.json();
    displayTrendingMovies(data.results);
}

function displayTrendingMovies(movies) {
    const trendingContainer = document.getElementById("trending-movies");
    if (!trendingContainer) return;

    trendingContainer.innerHTML = ""; // Clear previous content

    movies.forEach(movie => {
        const movieLink = document.createElement("a");
        movieLink.href = `movie.php?movie_id=${movie.id}`;
        movieLink.classList.add("movie-link");

        const img = document.createElement("img");
        img.src = IMG_PATH + movie.poster_path;
        img.alt = movie.title;


        movieLink.appendChild(img);
        trendingContainer.appendChild(movieLink);
    });

    autoSlide(); // keep this if the slider is working :)
}


//  Fetch TV Shows
async function fetchTVShows() {
    if (!document.getElementById("tv-show-list")) return;
    const res = await fetch(`${BASE_URL}/tv/popular?api_key=${API_KEY}`);
    const data = await res.json();
    displayMovies(data.results, "tv-show-list");
}

// Fetch Top Rated Movies
async function fetchTopRatedMovies() {
    if (!document.getElementById("top-rated-list")) return;
    const res = await fetch(`${BASE_URL}/movie/top_rated?api_key=${API_KEY}`);
    const data = await res.json();
    displayMovies(data.results, "top-rated-list");
}

// Fetch Random Featured Movies for Hero Section
async function fetchFeaturedMovies() {
    if (!document.querySelector(".hero")) return;

    const res = await fetch(`${BASE_URL}/trending/movie/week?api_key=${API_KEY}`);
    const data = await res.json();
    const movies = data.results.slice(0, 5); // Get the top 5 trending movies - can change this

    const genresRes = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}`);
    const genreData = await genresRes.json();

    const featuredMoviesContainer = document.getElementById("featured-movies");
    featuredMoviesContainer.innerHTML = "";

    movies.forEach(movie => {
        const movieGenres = movie.genre_ids.map(id => genreData.genres.find(g => g.id === id)?.name).join(", ");

        const movieHTML = `
            <a href="movie.php?movie_id=${movie.id}" class="hero-movie-link">
                <div class="hero-movie-card" style="background-image: url('${IMG_PATH_HIGH_QUALITY + movie.backdrop_path}')">
                    <h1><b>${movie.title}</b></h1>
                    <div class="info">
                        <span class="hero-badge">HD</span>
                        <span><b>Release:</b> ${movie.release_date || "N/A"}</span>
                        <span><b>IMDB:</b> ${movie.vote_average}</span>
                        <span><b>Genre:</b> ${movieGenres}</span>
                    </div>
                    <p>${movie.overview}</p>
                </div>
            </a>
        `;
        featuredMoviesContainer.innerHTML += movieHTML;
    });
}


// Function to load the user's watchlist
function loadWatchlist() {
    fetch('watchlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=load'
    })
        .then(response => response.json())
        .then(watchlist => {
            console.log(watchlist); // Log for debugging

            const watchlistContainer = document.getElementById("watchlist");

            if (!watchlistContainer) {
                console.warn("Skipping watchlist update: 'watchlist' container not found in DOM.");
                return;
            }

            watchlistContainer.innerHTML = "";

            if (watchlist.length === 0) {
                watchlistContainer.innerHTML = "<p>No movies added to your Watchlist yet.</p>";
                return;
            }

            watchlist.forEach(movie => {
                const movieCard = document.createElement("div");
                movieCard.classList.add("movie-card");

                // Make the whole movie card a clickable link
                movieCard.innerHTML = `
                <a href="movie.php?movie_id=${movie.movie_id}" class="movie-link">
                    <img src="https://image.tmdb.org/t/p/w500/${movie.movie_image}" alt="${movie.movie_title}">
                    <h4>${movie.movie_title}</h4>
                </a>
                <button class="remove-btn" onclick="removeFromWatchlist(${movie.movie_id})">❌ Remove</button>
            `;

                watchlistContainer.appendChild(movieCard);
            });
        })
        .catch(error => {
            console.error("Failed to load watchlist:", error);
        });
}

// Function to add a movie to the watchlist
function addToWatchlist(id, title, image) {
    fetch('watchlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&movie_id=${id}&title=${encodeURIComponent(title)}&image=${encodeURIComponent(image)}`
    })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);

            // Only refresh watchlist if we are on watchlist.php
            if (window.location.pathname.includes("watchlist.php")) {
                loadWatchlist();
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("Failed to add movie. Please try again.");
        });
}

// Function to remove a movie from the watchlist
function removeFromWatchlist(id) {
    fetch('watchlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&movie_id=${id}`
    })
        .then(response => response.json())
        .then(data => {
            alert(data.success || data.error);
            loadWatchlist();
        });
}

// Function to update the watchlist count in the header
function updateWatchlistCount() {
    fetch('watchlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=load'
    })
        .then(response => response.json())
        .then(watchlist => {
            const countElement = document.getElementById("watchlist-count");
            if (countElement) {
                countElement.innerText = `(${watchlist.length})`;
            }
        })
        .catch(error => console.error("Failed to update watchlist count:", error));
}

// Load the watchlist and update the count when the page loads
document.addEventListener("DOMContentLoaded", function() {
    loadWatchlist();
    updateWatchlistCount();
});



//  Search Functionality
document.getElementById("search-button").addEventListener("click", function () {
    const query = document.getElementById("search-input").value;
    if (query) {
        window.location.href = `search.php?query=${query}`;
    }
});

//  Handle "Random" Button Click
document.getElementById("random-button").addEventListener("click", function (event) {
    event.preventDefault();
    window.location.href = "random.php";
});

//  Run Functions Only on Their  Pages
fetchRecommendedMovies(); // Home Page
fetchTrendingMovies(); // Home Page
fetchTVShows(); // TV Shows Page
fetchTopRatedMovies(); // Top Rated Page
fetchFeaturedMovies(); // Home Page

// Fetch and Display Genres in Dropdown
async function fetchGenres() {
    try {
        const res = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}&language=en-US`);
        const data = await res.json();
        const genreList = document.getElementById("genre-list");

        genreList.innerHTML = ""; // Clear previous list

        data.genres.forEach(genre => {
            const li = document.createElement("li");
            li.innerHTML = `<a href="#" class="genre-link" data-genre-id="${genre.id}">${genre.name}</a>`;
            genreList.appendChild(li);
        });

        //  Add event listeners to genres after they load
        document.querySelectorAll(".genre-link").forEach(link => {
            link.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent default link behavior
                const genreId = this.getAttribute("data-genre-id");
                window.location.href = `genre.php?genre_id=${genreId}`; // Redirect to genre page
            });
        });

    } catch (error) {
        console.error("Error fetching genres:", error);
    }
}

//Load genres when the page loads
fetchGenres();



//  Attach event listener to search button
document.getElementById("search-button").addEventListener("click", function () {
    const query = document.getElementById("search-input").value.trim();
    if (query) {
        window.location.href = `search.php?query=${encodeURIComponent(query)}`;
    }
});

//  Fetch and Display Search Results in `search.php`
async function fetchSearchResults() {
    const urlParams = new URLSearchParams(window.location.search);
    const query = urlParams.get("query");

    if (!query) return; // Stop if no search query

    //  Set search title
    document.getElementById("search-title").innerText = `Search results for "${query}"`;

    try {
        //  Fetch search results
        const res = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&query=${query}`);
        const data = await res.json();

        if (data.results.length === 0) {
            document.getElementById("search-results").innerHTML = "<p>No results found.</p>";
        } else {
            displayMovies(data.results, "search-results"); //  Display search results
        }

        //  Fetch related movies if at least one result is found
        if (data.results.length > 0) {
            const firstMovieId = data.results[0].id;
            fetchRelatedMovies(firstMovieId);
        }
    } catch (error) {
        console.error("Error fetching search results:", error);
    }
}

//  Fetch and Display Related Movies
async function fetchRelatedMovies(movieId) {
    try {
        const res = await fetch(`${BASE_URL}/movie/${movieId}/recommendations?api_key=${API_KEY}`);
        const data = await res.json();
        if (data.results.length > 0) {
            displayMovies(data.results, "related-movies"); //  Show related movies
        }
    } catch (error) {
        console.error("Error fetching related movies:", error);
    }
}



//  Run search function only on `search.php`
if (document.getElementById("search-results")) {
    fetchSearchResults();
}

// //  slider preferences
// document.getElementById('submitQuiz').addEventListener('click', function(e) {
//     e.preventDefault();
//     // const formData = {
//     //     genre: document.getElementById('genre_question').value,
//     //     weather: document.getElementById('weather_question').value,
//     //     mood: document.getElementById('mood_question').value,
//     //     company: document.getElementById('company_question').value,
//     //     genre_weight: document.getElementById('genre_slider').value,
//     //     rating_weight: document.getElementById('rating_slider').value,
//     //     recency_weight: document.getElementById('recency_slider').value,
//     //     popularity_weight: document.getElementById('popularity_slider').value,
//     //     keyword_weight: document.getElementById('keywords_slider').value,
//     //     collaborative_weight: document.getElementById('collaborative_slider').value,
//     // };
//
//     const genre = document.getElementById('genre_question').value;
//     const weather = document.getElementById('weather_question').value;
//     const mood = document.getElementById('mood_question').value;
//     const company = document.getElementById('company_question').value;
//     const genre_weight=  document.getElementById('genre_slider').value;
//     const rating_weight=  document.getElementById('rating_slider').value;
//     const recency_weight=  document.getElementById('recency_slider').value;
//     const popularity_weight=  document.getElementById('popularity_slider').value;
//     const keyword_weight=  document.getElementById('keywords_slider').value;
//     const collaborative_weight=  document.getElementById('collaborative_slider').value;
//
//     // Log the data to the console before sending
//     // console.log('Sending data to backend:', formData);
//     fetch('process_quiz.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({
//             genre: genre,
//             weather: weather,
//             mood: mood,
//             company: company,
//             genre_weight: genre_weight,
//             rating_weight: rating_weight,
//             recency_weight: recency_weight,
//             popularity_weight: popularity_weight,
//             keyword_weight: keyword_weight,
//             collaborative_weight: collaborative_weight
//
//         })
//     })
//         .then(response => response.json())
//         .then(data => {
//             console.log('Data received from backend:', data);
//
//             // Check if results exist
//             if (data.results && data.results.length > 0) {
//                 const resultsContainer = document.getElementById('results-container');
//                 resultsContainer.innerHTML = ''; // Clear any previous results
//
//                 data.results.forEach(quizMovie => {
//                     const quizMovieElement = document.createElement('div');
//                     quizMovieElement.classList.add('quiz_movie');
//
//                     // Movie title
//                     const titleElement = document.createElement('h2');
//                     titleElement.textContent = quizMovie.title || 'No title available';
//
//                     // Movie overview
//                     const overviewElement = document.createElement('p');
//                     overviewElement.textContent = quizMovie.overview || 'No description available';
//
//                     // Movie poster image
//                     const posterElement = document.createElement('img');
//                     if (quizMovie.poster_path) {
//                         posterElement.src = `https://image.tmdb.org/t/p/w500${quizMovie.poster_path}`;
//                         posterElement.alt = `Poster of ${quizMovie.title}`;
//                     } else {
//                         posterElement.src = 'default_poster.jpg'; // Use a default poster if no poster path is available
//                         posterElement.alt = 'No poster available';
//                     }
//
//                     // Add movie details to the quiz movie element
//                     quizMovieElement.appendChild(titleElement);
//                     quizMovieElement.appendChild(overviewElement);
//                     quizMovieElement.appendChild(posterElement);
//
//                     // Append the quiz movie element to the results container
//                     resultsContainer.appendChild(quizMovieElement);
//                 });
//             } else {
//                 console.log('No movie results found.');
//             }
//         })
//         .catch(error => {
//             console.error('Error fetching movie data:', error);
//         });
//
//
// });
