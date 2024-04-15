<?php
// Start session to maintain user's session data
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the file containing database connection details
include "../connection.php";

// Retrieve review ID from GET parameter or set to 0 if not provided
$reply_id = isset($_GET['reply_id']) && is_numeric($_GET['reply_id']) ? intval($_GET['reply_id']) : 0;

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // If inputs are valid, prepare and execute SQL query to delete review data
    $stmt = $con->prepare("DELETE FROM reply WHERE reply_id=?");
    $stmt->execute([$reply_id]);

    // Set success message
    $successMsg = 'تم ازالة الرد بنجاح';

    // Redirect to show_review.php page with success message
    header("Location:show_reports.php?success_message=" . urlencode($successMsg));
    exit; // Exit to prevent further execution after redirection

}
