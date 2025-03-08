<header>

    <div class="logo"><img src="assets/cinematch_logo.png" alt="CineMatch Logo">
        <span>CineMatch</span>
    </div>
    <nav>
        <ul>
            <li><a href="home.php" onclick="fetchTrendingMovies()">Home</a></li>
            <li class="dropdown">
                <a href="#">Genre ▼</a>
                <ul class="dropdown-menu" id="genre-list"></ul>
            </li>
            <li><a href="top_rated.php">Top Rated</a></li>
            <li><a href="tv_shows.php">TV Shows</a></li>
            <li><a href="watchlist.php">Watchlist <span id="watchlist-count">(0)</span></a></li>
            <li><a href="#" id="random-button">🎲 Random</a></li>



        </ul>
    </nav>
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Enter keywords...">
        <button id="search-button"><i class="material-icons">search</i></button>

    </div>
    <div class="logout-button"  style="float:right;">
        <a href="logout.php">Logout</a>
    </div>
</header>