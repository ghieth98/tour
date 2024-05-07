<?php
// Start session to maintain user's session data
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the file containing database connection details
include "../connection.php";

// Retrieve review ID from GET parameter or set to 0 if not provided
$comment_id = isset($_GET['review_id']) && is_numeric($_GET['review_id']) ? intval($_GET['review_id']) : 0;

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination_id = $_POST['destination_id'];
    // If inputs are valid, prepare and execute SQL query to delete review data
    $stmt = $con->prepare("DELETE FROM review WHERE review_id=?");
    $stmt->execute([$comment_id]);

    // Set success message
    $successMsg = 'تم ازالة التعليق بنجاح';

    // Redirect to show_review.php page with success message
    header("Location:show_comments.php?destination_id=" . $destination_id . "&success_message=" .
        urlencode($successMsg));
    exit; // Exit to prevent further execution after redirection

}

