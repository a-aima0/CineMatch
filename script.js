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

//  Fetch a Random Featured Movie for Hero Section
async function fetchFeaturedMovie() {
    if (!document.querySelector(".hero")) return;
    const res = await fetch(`${BASE_URL}/trending/movie/week?api_key=${API_KEY}`);
    const data = await res.json();
    if (data.results.length > 0) {
        const movie = data.results[0];

        document.getElementById("featured-title").innerText = movie.title;
        document.getElementById("duration").innerText = `Duration: ${movie.runtime || "N/A"} min`;
        document.getElementById("imdb-rating").innerText = `IMDB: ${movie.vote_average}`;
        document.getElementById("description").innerText = movie.overview;

        // Fetch genre names based on genre IDs
        const genres = await fetch(`${BASE_URL}/genre/movie/list?api_key=${API_KEY}`);
        const genreData = await genres.json();
        const movieGenres = movie.genre_ids.map(id => genreData.genres.find(g => g.id === id)?.name).join(", ");
        document.getElementById("genres").innerText = `Genre: ${movieGenres}`;

        // Set high-quality background image
        document.querySelector(".hero").style.backgroundImage = `url(${IMG_PATH_HIGH_QUALITY + movie.backdrop_path})`;
    }
}

//  Watchlist Functionality
function addToWatchlist(id, title, image) {
    let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
    if (!watchlist.some(movie => movie.id === id)) {
        watchlist.push({ id, title, image });
        localStorage.setItem("watchlist", JSON.stringify(watchlist));
        alert("Added to Watchlist! ✅");
    } else {
        alert("This movie is already in your Watchlist! 📝");
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
fetchFeaturedMovie(); // Home Page

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



// ✅ Attach event listener to search button
document.getElementById("search-button").addEventListener("click", function () {
    const query = document.getElementById("search-input").value.trim();
    if (query) {
        window.location.href = `search.php?query=${encodeURIComponent(query)}`;
    }
});

// ✅ Fetch and Display Search Results in `search.php`
async function fetchSearchResults() {
    const urlParams = new URLSearchParams(window.location.search);
    const query = urlParams.get("query");

    if (!query) return; // Stop if no search query

    // ✅ Set search title
    document.getElementById("search-title").innerText = `Search results for "${query}"`;

    try {
        // ✅ Fetch search results
        const res = await fetch(`${BASE_URL}/search/movie?api_key=${API_KEY}&query=${query}`);
        const data = await res.json();

        if (data.results.length === 0) {
            document.getElementById("search-results").innerHTML = "<p>No results found.</p>";
        } else {
            displayMovies(data.results, "search-results"); // ✅ Display search results
        }

        // ✅ Fetch related movies if at least one result is found
        if (data.results.length > 0) {
            const firstMovieId = data.results[0].id;
            fetchRelatedMovies(firstMovieId);
        }
    } catch (error) {
        console.error("Error fetching search results:", error);
    }
}

// ✅ Fetch and Display Related Movies
async function fetchRelatedMovies(movieId) {
    try {
        const res = await fetch(`${BASE_URL}/movie/${movieId}/similar?api_key=${API_KEY}`);
        const data = await res.json();
        if (data.results.length > 0) {
            displayMovies(data.results, "related-movies"); // ✅ Show related movies
        }
    } catch (error) {
        console.error("Error fetching related movies:", error);
    }
}

// ✅ Function to Display Movies in a Grid
function displayMovies(movies, containerId) {
    const movieGrid = document.getElementById(containerId);
    if (!movieGrid) return;

    movieGrid.innerHTML = ""; // Clear old results

    movies.slice(0, 16).forEach(movie => { // ✅ Show up to 16 movies
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

// ✅ Run search function only on `search.php`
if (document.getElementById("search-results")) {
    fetchSearchResults();
}
