const API_KEY = "ff024c8b4942e7ebf52baf82685c5249";  
const BASE_URL = "https://api.themoviedb.org/3";
const IMG_PATH = "https://image.tmdb.org/t/p/w500";
const IMG_PATH_HIGH_QUALITY = "https://image.tmdb.org/t/p/original";


//  Function to Display Movies in a Specific Container
function displayMovies(movies, containerId) {
    const movieGrid = document.getElementById(containerId);
    if (!movieGrid) return; // If container doesn't exist, do nothing

    movieGrid.innerHTML = ""; // Clear previous content

    movies.slice(0, 32).forEach(movie => {
        const movieCard = document.createElement("div");
        movieCard.classList.add("movie-card");
        movieCard.innerHTML = `
            <img src="${IMG_PATH + movie.poster_path}" alt="${movie.title || movie.name}">
            <h4>${movie.title || movie.name}</h4>
            <button class="watchlist-btn" onclick="addToWatchlist(${movie.id}, '${movie.title || movie.name}', '${IMG_PATH + movie.poster_path}')">⭐ Add to Watchlist</button>
        `;
        movieGrid.appendChild(movieCard);
    });
}

// Fetch Recommended Movies (Only on Home Page)
async function fetchRecommendedMovies() {
    if (!document.getElementById("movie-list")) return; // Prevent errors on other pages
    const res = await fetch(`${BASE_URL}/movie/popular?api_key=${API_KEY}`);
    const data = await res.json();
    displayMovies(data.results, "movie-list");
}

//  Fetch Trending Movies for Home Page Carousel
async function fetchTrendingMovies() {
    if (!document.getElementById("trending-movies")) return; // Only run if on home page
    const res = await fetch(`${BASE_URL}/trending/movie/week?api_key=${API_KEY}`);
    const data = await res.json();
    displayTrendingMovies(data.results);
}

function displayTrendingMovies(movies) {
    const trendingContainer = document.getElementById("trending-movies");
    trendingContainer.innerHTML = ""; // Clear previous content

    movies.forEach(movie => {
        const img = document.createElement("img");
        img.src = IMG_PATH + movie.poster_path;
        img.alt = movie.title;
        trendingContainer.appendChild(img);
    });

    autoSlide();
}

//  Fetch TV Shows (Only on TV Shows Page)
async function fetchTVShows() {
    if (!document.getElementById("tv-show-list")) return;
    const res = await fetch(`${BASE_URL}/tv/popular?api_key=${API_KEY}`);
    const data = await res.json();
    displayMovies(data.results, "tv-show-list");
}

// Fetch Top Rated Movies (Only on Top Rated Page)
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
    const movies = data.results.slice(0, 5); // Get the top 5 trending movies

    const genresRes = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}`);
    const genreData = await genresRes.json();

    const featuredMoviesContainer = document.getElementById("featured-movies");
    featuredMoviesContainer.innerHTML = ""; // Clear existing content

    movies.forEach(movie => {
        const movieGenres = movie.genre_ids.map(id => genreData.genres.find(g => g.id === id)?.name).join(", ");

        const movieHTML = `
            <div class="hero-movie-card" style="background-image: url('${IMG_PATH_HIGH_QUALITY + movie.backdrop_path}')">
                <h1>${movie.title}</h1>
                <div class="info">
                    <span class="hero-badge">HD</span>
                    <span>Release: ${movie.release_date || "N/A"}</span>
                    <span>IMDB: ${movie.vote_average}</span>
                    <span>Genre: ${movieGenres}</span>
                </div>
                <p>${movie.overview}</p>
            </div>
        `;
        featuredMoviesContainer.innerHTML += movieHTML;
    });
}



//  Watchlist Functionality
function addToWatchlist(id, title, image) {
    let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
    if (!watchlist.some(movie => movie.id === id)) {
        watchlist.push({ id, title, image });
        localStorage.setItem("watchlist", JSON.stringify(watchlist));
        alert("Added to Watchlist! ");
    } else {
        alert("This movie is already in your Watchlist! ");
    }
}

function loadWatchlist() {
    let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
    const watchlistContainer = document.getElementById("watchlist");
    if (!watchlistContainer) return;

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
    loadWatchlist();
}

loadWatchlist();

//  Update Watchlist Count in Header
function updateWatchlistCount() {
    let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
    document.getElementById("watchlist-count").innerText = `(${watchlist.length})`;
}
updateWatchlistCount();

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

//  Run Functions Only on Their Respective Pages
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


//  Function to Display Movies in a Grid
function displayMovies(movies, containerId) {
    const movieGrid = document.getElementById(containerId);
    if (!movieGrid) return;

    movieGrid.innerHTML = ""; // Clear old results

    movies.slice(0, 16).forEach(movie => { // show 16 movies
        const movieCard = document.createElement("div");
        movieCard.classList.add("movie-card");
        movieCard.innerHTML = `
            <img src="${IMG_PATH + movie.poster_path}" alt="${movie.title || movie.name}">
            <h4>${movie.title || movie.name}</h4>
            <button class="watchlist-btn" onclick="addToWatchlist(${movie.id}, '${movie.title || movie.name}', '${IMG_PATH + movie.poster_path}')">⭐ Add to Watchlist</button>
        `;
        movieGrid.appendChild(movieCard);
    });
}



//  Run search function only on `search.php`
if (document.getElementById("search-results")) {
    fetchSearchResults();
}

//  slider preferences
document.getElementById('submitQuiz').addEventListener('click', function(e) {
    e.preventDefault();
    // const formData = {
    //     genre: document.getElementById('genre_question').value,
    //     weather: document.getElementById('weather_question').value,
    //     mood: document.getElementById('mood_question').value,
    //     company: document.getElementById('company_question').value,
    //     genre_weight: document.getElementById('genre_slider').value,
    //     rating_weight: document.getElementById('rating_slider').value,
    //     recency_weight: document.getElementById('recency_slider').value,
    //     popularity_weight: document.getElementById('popularity_slider').value,
    //     keyword_weight: document.getElementById('keywords_slider').value,
    //     collaborative_weight: document.getElementById('collaborative_slider').value,
    // };

    const genre = document.getElementById('genre_question').value;
    const weather = document.getElementById('weather_question').value;
    const mood = document.getElementById('mood_question').value;
    const company = document.getElementById('company_question').value;
    const genre_weight=  document.getElementById('genre_slider').value;
    const rating_weight=  document.getElementById('rating_slider').value;
    const recency_weight=  document.getElementById('recency_slider').value;
    const popularity_weight=  document.getElementById('popularity_slider').value;
    const keyword_weight=  document.getElementById('keywords_slider').value;
    const collaborative_weight=  document.getElementById('collaborative_slider').value;

    // Log the data to the console before sending
    // console.log('Sending data to backend:', formData);
    fetch('process_quiz.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            genre: genre,
            weather: weather,
            mood: mood,
            company: company,
            genre_weight: genre_weight,
            rating_weight: rating_weight,
            recency_weight: recency_weight,
            popularity_weight: popularity_weight,
            keyword_weight: keyword_weight,
            collaborative_weight: collaborative_weight

        })
    })
        .then(response => response.json())
        .then(data => {
            console.log('Data received from backend:', data);

            // Check if results exist
            if (data.results && data.results.length > 0) {
                const resultsContainer = document.getElementById('results-container');
                resultsContainer.innerHTML = ''; // Clear any previous results

                data.results.forEach(quizMovie => {
                    const quizMovieElement = document.createElement('div');
                    quizMovieElement.classList.add('quiz_movie');

                    // Movie title
                    const titleElement = document.createElement('h2');
                    titleElement.textContent = quizMovie.title || 'No title available';

                    // Movie overview
                    const overviewElement = document.createElement('p');
                    overviewElement.textContent = quizMovie.overview || 'No description available';

                    // Movie poster image
                    const posterElement = document.createElement('img');
                    if (quizMovie.poster_path) {
                        posterElement.src = `https://image.tmdb.org/t/p/w500${quizMovie.poster_path}`;
                        posterElement.alt = `Poster of ${quizMovie.title}`;
                    } else {
                        posterElement.src = 'default_poster.jpg'; // Use a default poster if no poster path is available
                        posterElement.alt = 'No poster available';
                    }

                    // Add movie details to the quiz movie element
                    quizMovieElement.appendChild(titleElement);
                    quizMovieElement.appendChild(overviewElement);
                    quizMovieElement.appendChild(posterElement);

                    // Append the quiz movie element to the results container
                    resultsContainer.appendChild(quizMovieElement);
                });
            } else {
                console.log('No movie results found.');
            }
        })
        .catch(error => {
            console.error('Error fetching movie data:', error);
        });


});
