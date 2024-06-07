<?php
// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the database connection file
include "../connection.php";

// Get the reply ID from the URL, if it exists and is numeric
$reply_id = isset($_GET['reply_id']) && is_numeric($_GET['reply_id']) ? intval($_GET['reply_id']) : 0;

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the destination ID from the form
    $destination_id = $_POST['destination_id'];
    // Prepare a statement to delete the reply from the database
    $stmt = $con->prepare("DELETE FROM reply WHERE reply_id=?");
    // Execute the statement with reply_id as parameter to delete the reply
    $stmt->execute([$reply_id]);

    // Set success message for successful reply deletion
    $successMsg = 'تم ازالة الرد بنجاح';

    // Redirect back to the destination page with the success message
    header("Location:show_destination.php?destination_id=" . $destination_id . "&success_message=" . urlencode($successMsg));
    // Exit the script
    exit;
}


