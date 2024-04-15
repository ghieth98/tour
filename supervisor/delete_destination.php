<?php

// Start session to maintain user's session data
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the file containing database connection details
include "../connection.php";

// Retrieve destination ID from GET parameter or set to 0 if not provided
$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval(
    $_GET['destination_id']
) : 0;

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $city_id = $_POST['city_id'];
    //  prepare and execute SQL query to delete destination data
    $stmt = $con->prepare("DELETE FROM destination WHERE destination_id=?");
    $stmt->execute([$destination_id]);

    // Set success message
    $successMsg = 'تم ازالة الوجهة بنجاح';

    // Redirect to show_destination.php page with success message
    header(
        "Location:show_destinations.php?city_id=" . $city_id . "&success_message=" .
        urlencode($successMsg)
    );
    exit; // Exit to prevent further execution after redirection

}

