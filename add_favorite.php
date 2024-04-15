<?php

// Start the session to access session variables
session_start();

if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the database connection file
include "connection.php";

// Get the tourist ID from the session
$tourist_id = $_SESSION['tourist_id'];

// Get the destination ID from the URL, if it exists and is numeric
$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval(
    $_GET['destination_id']
) : 0;

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $city_id = $_POST['city_id'];
    // Prepare a statement to check if the destination is already in favorites
    $stmt = $con->prepare("SELECT destination_id FROM favorite WHERE destination_id=?");
    $stmt->execute([$destination_id]);
    // Get the count of rows returned
    $destination_count = $stmt->rowCount();

    // If the destination is already in favorites, remove it; otherwise, add it
    if ($destination_count > 0) {
        // Prepare a statement to delete the destination from favorites
        $stmt = $con->prepare("DELETE FROM favorite WHERE destination_id=?");
        $stmt->execute([$destination_id]);
        // Set success message for removal
        $successMsg = 'تم ألإزالة من المفضلة';
    } else {
        // Prepare a statement to insert the destination into favorites
        $stmt = $con->prepare("INSERT INTO favorite (date, tourist_id, destination_id) VALUES (NOW(),?,?)");
        $stmt->execute([$tourist_id, $destination_id]);
        // Set success message for addition
        $successMsg = 'تم ألإضافة إلى المفضلة';
    }

    // Determine the page to redirect based on the referer
    $referer = $_SERVER['HTTP_REFERER'];
    if (str_contains($referer, 'destination.php')) {
        $redirectPage = 'destination.php';
        header("Location:".$redirectPage."?success_message=".urlencode($successMsg));
    } elseif (str_contains($referer, 'dashboard.php')) {
        $redirectPage = 'tourist/dashboard.php';
        header("Location:".$redirectPage."?success_message=".urlencode($successMsg));

    } elseif(str_contains($referer, 'city_destination')) {
        $redirectPage = 'city_destination.php';
        header("Location: $redirectPage?city_id=".$city_id."&success_message=".urlencode($successMsg));
    }
}


