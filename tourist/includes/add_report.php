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


// Initialize variables for body, body error, and success message
$successMsg = '';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the body of the report
    $body = validate($_POST['body']);
    // Get the reply ID from the URL, if it exists and is numeric
    $reply_id = $_POST['reply_id'];

// Get the review ID from the URL, if it exists and is numeric
    $review_id = $_POST['review_id'];

// Get the destination ID from the URL, if it exists and is numeric
    $destination_id = $_POST['destination_id'];

// Get the type from the URL, if it exists and is a string
    $type = $_POST['type'];

    // Check the type of report (review or reply)
    if ($type === 'review') {
        // Prepare a statement to insert the report for a review into the database
        $stmt = $con->prepare("INSERT INTO report ( date, review_id, reply_id, tourist_id) VALUES ( NOW(), ?, ?, ?)");
        // Execute the statement with body, review_id, reply_id (null for review), and tourist_id as parameters
        $stmt->execute([$review_id, null, $tourist_id]);
        // Set success message for successful report submission
        $successMsg = 'تم إضافة البلاغ بنجاح';
        // Redirect back to the destination page with the success message
        header("Location:../destination_info.php?destination_id=".$destination_id."&success_message=".urlencode
            ($successMsg));
        // Exit the script
        exit;
    } elseif ($type === 'reply') {
        // Prepare a statement to insert the report for a reply into the database
        $stmt = $con->prepare("INSERT INTO report ( date, review_id, reply_id, tourist_id) VALUES ( NOW(), ?, ?, ?)");
        // Execute the statement with body, review_id (null for reply), reply_id, and tourist_id as parameters
        $stmt->execute([null, $reply_id, $tourist_id]);
        // Set success message for successful report submission
        $successMsg = 'تم إضافة البلاغ بنجاح';
        // Redirect back to the destination page with the success message
        header("Location:../destination_info.php?destination_id=".$destination_id."&success_message=".urlencode
            ($successMsg));
        // Exit the script
        exit;
    }
}

