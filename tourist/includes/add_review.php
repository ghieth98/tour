<?php
// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the validate function file
include "../../validate.php";

// Include the database connection file
include "../../connection.php";

// Get the tourist ID from the session
$tourist_id = $_SESSION['tourist_id'];

// Get the destination ID from the URL, if it exists and is numeric
$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval($_GET['destination_id']) : 0;

// Initialize variables for body, body error, and success message
$body = $body_error = '';
$successMsg = '';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the body of the review
    $body = validate($_POST['body']);

    // Check if body is empty
    if (empty($body)) {
        // Set an error message if body is empty
        $body_error = 'برجاء أدخال نص المراجعة';
    } else {
        // Prepare a statement to insert the review into the database
        $stmt = $con->prepare("INSERT INTO review (body, tourist_id, destination_id, date) VALUES (?,?,?,NOW())");
        // Execute the statement with body, tourist_id, and destination_id as parameters
        $stmt->execute([$body, $tourist_id, $destination_id]);
        // Set success message for successful review submission
        $successMsg = 'تم إضافة المراجعة بنجاح';
        // Redirect back to the destination page with the success message
        header("Location:../../destination_info.php?success_message=" . urlencode($successMsg));
        // Exit the script
        exit;
    }
}



