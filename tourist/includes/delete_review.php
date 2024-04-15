<?php
// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the database connection file
include "../../connection.php";

// Get the review ID from the URL, if it exists and is numeric
$review_id = isset($_GET['review_id']) && is_numeric($_GET['review_id']) ? intval($_GET['review_id']) : 0;

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the destination ID from the form
    $destination_id = $_POST['destination_id'];
    // Prepare a statement to delete the review from the database
    $stmt = $con->prepare("DELETE FROM review WHERE review_id=?");
    // Execute the statement with review_id as parameter to delete the review
    $stmt->execute([$review_id]);

    // Set success message for successful review deletion
    $successMsg = 'تم ازالة المراجعة بنجاح';

    // Redirect back to the destination page with the success message
    header("Location:../../destination_info.php?destination_id=" . $destination_id . "&success_message=" . urlencode
        ($successMsg));
    // Exit the script
    exit;
}

