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
<html lang = "en">
    <head>
        <link rel="stylesheet" href="index.css">
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="assets/cinematch_logo.png" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--using FontAwesome-->
        <script src="https://kit.fontawesome.com/c8e4d183c2.js" crossorigin="anonymous"></script>
        <title>CineMatch</title>
        <div class = "center">
    </head>
    <body>
    <?php include_once('header.php'); ?>

        <h1>CineMatch</h1>
        <p>Hello welcome to CineMatch I hope you find
            what you're looking for!
        </p>
        <section id="main">
            <!---showcase-->
            <!---heading-->
            <h1 class="showcase-heading">Film name</h1>
            <!----showcase-box-->
            <div class = "info">
                <div class="showcase-box">
                    <img src="assets/placeholder_img.jpg" id="img"/>
                </div>
                <article>
                    <p>Here is a description about the film. It includes
                        the plot of the film. May include characters, actors
                        and film duration.
                    </p>
                </article>
            </div>
        </section>
        <p>This section includes film ratings and statistics</p>
    </body>

</div>
</html>