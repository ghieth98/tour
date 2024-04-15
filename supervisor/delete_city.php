<?php
// Start session to maintain user's session data
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the file containing database connection details
include "../connection.php";

// Retrieve city ID from GET parameter or set to 0 if not provided
$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ? intval($_GET['city_id']) : 0;

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If inputs are valid, prepare and execute SQL query to delete city data
    $stmt = $con->prepare("DELETE FROM city WHERE city_id=?");
    $stmt->execute([$city_id]);

    // Set success message
    $successMsg = 'تم ازالة المدينة بنجاح';

    // Redirect to show_city.php page with success message
    header("Location:dashboard.php?success_message=" . urlencode($successMsg));
    exit; // Exit to prevent further execution after redirection

}