<?php

session_start(); // Only call session_start() once at the top of the file

// Check if username is provided
if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
} else {
    echo "Username not provided.";
}

if (isset($_POST['table'])) {
    $_SESSION['table'] = $_POST['table'];
} elseif (!isset($_SESSION['table'])) {
    die("Error: Table name is not specified.");
}

// get the table name from the session
$table_name = $_SESSION['table'];

if (empty($table_name)) {
    die("Error: Table name is not specified.");
}

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$userName = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
//$encrypted = password_hash($password, PASSWORD_DEFAULT);

$command = "INSERT INTO 
                $table_name (firstName, lastName, username, email, password) 
                VALUES ('$firstName', '$lastName', '$userName', '$email', '$password')";

include("config.php");
include("connection.php");

if ($conn->query($command) === TRUE) {
    $response = [
        "success" => true,
        "message" => $firstName . " " . $lastName . " has been added to the system"
    ];
} else {
    $response = [
        "success" => false,
        "message" => "Error: " . $conn->error
    ];
}

$_SESSION["response"] = $response;
$conn->close();
header("Location: register.php");
exit;


//session_start();
//
//if (isset($_POST['username'])) {
//    // Update the session variable with the new username
//    $_SESSION['username'] = $_POST['username'];
//} else {
//    echo "Username not provided.";
//}
//
//////    var_dump($_SESSION);
////$table_name = $_SESSION['table'];
////$_SESSION['table'] = "users";
//
//// Ensure 'table' key exists in $_POST before accessing it
//if (isset($_POST['table'])) {
//    $_SESSION['table'] = $_POST['table'];
//} else {
//    die("Error: Table name is not specified.");
//}
//
//// Set the table name for later use
//$table_name = $_SESSION['table'];
//$_SESSION['table'] = "users";
//
//if (empty($table_name)) {
//    die("Error: Table name is not specified.");
//}
//
//$firstName = $_POST['firstName'];
//$lastName = $_POST['lastName'];
//$userName = $_POST['username'];
//$email = $_POST['email'];
//$password = $_POST['password'];
////    var_dump($password);
//$encrypted = password_hash($password, PASSWORD_DEFAULT);
//
//$command="INSERT INTO
//                $table_name (firstName, lastName, username, email, password)
//                VALUES ('$firstName', '$lastName', '$userName', '$email', '$encrypted')";
//
//include("config.php");
//include("connection.php");
//
//
//if ($conn->query($command) === TRUE) {
//    $response = [
//        "success" => true,
//        "message" => $firstName . " " . $lastName . " has been added to the system"
//    ];
//} else {
//    // Capture and store the error message in the session
//    $response = [
//        "success" => false,
//        "message" => "Error: " . $conn->error
//    ];
//}
//
//$_SESSION["response"] = $response; // Store the response in the session
//$conn->close(); // Close the connection
//header("Location: register.php"); // Redirect to the form page
//exit;
//?>

<!---->
<!--//session_start();-->
<!--//-->
<!--//if (isset($_POST['username'])) {-->
<!--//    $_SESSION['username'] = $_POST['username'];-->
<!--//} else {-->
<!--//    echo "Username not provided.";-->
<!--//}-->
<!--//-->
<!--//$_SESSION['table'] = "users";-->
<!--//$table_name = $_SESSION['table'];-->
<!--//-->
<!--//-->
<!--//-->
<!--//if (empty($table_name)) {-->
<!--//    die("Error: Table name is not specified.");-->
<!--//}-->
<!--//-->
<!--//$firstName = $_POST['firstName'];-->
<!--//$lastName = $_POST['lastName'];-->
<!--//$userName = $_POST['username'];-->
<!--//$email = $_POST['email'];-->
<!--//$password = $_POST['password'];-->
<!--//$encrypted = password_hash($password, PASSWORD_DEFAULT);-->
<!--//-->
<!--//$command = "INSERT INTO $table_name (firstName, lastName, username, email, password)-->
<!--//            VALUES ('$firstName', '$lastName', '$userName', '$email', '$encrypted')";-->
<!--//-->
<!--//-->
<!--//include("config.php");-->
<!--//include("connection.php");-->
<!--//-->
<!--//-->
<!--//if ($conn->query($command) === TRUE) {-->
<!--//    $response = [-->
<!--//        "success" => true,-->
<!--//        "message" => $firstName . " " . $lastName . " has been added to the system"-->
<!--//    ];-->
<!--//} else {-->
<!--//    $response = [-->
<!--//        "success" => false,-->
<!--//        "message" => "Error: " . $conn->error-->
<!--//    ];-->
<!--//}-->
<!--//-->
<!--//$_SESSION["response"] = $response;-->
<!--//$conn->close();-->
<!--//header("Location: register.php");-->
<!--//exit;-->
<!--//?>-->
