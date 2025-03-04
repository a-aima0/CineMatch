<?php
/** @var string $servername */
/** @var string $username */
/** @var string $password */
/** @var string $dbname */

// Import database credentials from config.php
require_once('config.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else {

}


?>
