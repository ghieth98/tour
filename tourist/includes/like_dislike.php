<?php

// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the database connection file
include "../../connection.php";

// Retrieve the tourist ID from the session
$tourist_id = $_SESSION['tourist_id'];

// Get the review ID and reply ID from the URL, if they exist and are numeric
$review_id = isset($_GET['review_id']) && is_numeric($_GET['review_id']) ? intval($_GET['review_id']) : 0;
$reply_id = isset($_GET['reply_id']) && is_numeric($_GET['reply_id']) ? intval($_GET['reply_id']) : 0;

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the action and type from the POST data
    $action = $_POST['action'] ?? '';
    $type = $_POST['type'] ?? '';
    $destination_id = $_POST['destination_id'];
//
    // Check if the tourist has already liked or disliked the review or reply
    $stmt = $con->prepare("SELECT * FROM `like` WHERE tourist_id = ? AND ((review_id = ? AND type = 'liked') OR (reply_id = ? AND type = 'liked'))");
    $stmt->execute([$tourist_id, $review_id, $reply_id]);
    $alreadyLiked = $stmt->rowCount() > 0;


    // If the type is 'review'
    if ($type === 'review') {
        // If the action is 'like'
        if ($action === 'like' &&  !$alreadyLiked) {
            // Insert a new record indicating that the tourist liked the review
            $stmt = $con->prepare("INSERT INTO `like` (type, review_id, reply_id, tourist_id) VALUES (?,?,?,?)");
            $stmt->execute(['liked', $review_id, null, $tourist_id]);
            $successMsg = 'تم إضافة الإعجاب';
        } else {
            $stmt = $con->prepare("UPDATE `like` SET type=? WHERE review_id=? AND tourist_id=?");
            $stmt->execute(['disliked', $review_id, $tourist_id]);
            $successMsg = 'تم ازالة الإعجاب';
        }
    } // If the type is 'reply'
    elseif ($type === 'reply') {
        // If the action is 'like'
        if ($action === 'like' &&  !$alreadyLiked) {
            // Insert a new record indicating that the tourist liked the reply
            $stmt = $con->prepare("INSERT INTO `like` (type, review_id, reply_id, tourist_id) VALUES (?,?,?,?)");
            $stmt->execute(['liked', null, $reply_id, $tourist_id]);
            $successMsg = 'تم إضافة الإعجاب';
        } else {
            $stmt = $con->prepare("UPDATE `like` SET type=? WHERE reply_id=? AND tourist_id=?");
            $stmt->execute(['disliked', $reply_id, $tourist_id]);
            $successMsg = 'تم ازالة الإعجاب';
        }
    }

    // Redirect back to the destination page with the success message
    header("Location:../../destination_info.php?destination_id=".$destination_id."&success_message=".urlencode
        ($successMsg));
    exit;
}

