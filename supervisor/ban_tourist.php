<?php
// Start session to maintain user's session data
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the database connection file
include "../connection.php";

// Initialize variable to store success message
$successMsg = '';
// Retrieve tourist ID from GET parameter or set to 0 if not provided
$tourist_id = isset($_GET['tourist_id']) && is_numeric($_GET['tourist_id']) ? intval($_GET['tourist_id']) : 0;

$status = $_POST['ban'];
$ban = '';


// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the action from the form or set to empty string if not provided

    // Perform action based on the value received from the form
    if ($status === 'banned') {
        // Prepare and execute SQL statement to ban the tourist
        $stmt = $con->prepare("UPDATE tourist SET ban=? WHERE tourist_id=?");
        $stmt->execute([$status, $tourist_id]);
        // Set success message and redirect to show_reports.php
        $successMsg = 'تم حظر السائح بنجاح';
        header("Location: show_reports.php?success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection

    } elseif ($status === 'temporary') {
        $ban_expiration_date = date('Y-m-d-H-i-s', strtotime('+5 hours'));
        $stmt = $con->prepare("UPDATE tourist SET ban=?, ban_expiration_date=? WHERE tourist_id=?");
        $stmt->execute([$status, $ban_expiration_date, $tourist_id]);
        $successMsg = 'تم حظر السائح مؤقتا بنجاح';
        header("Location: show_reports.php?success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection
    } elseif ($status === 'unbanned') {
        // Prepare and execute SQL statement to unban the tourist
        $stmt = $con->prepare("UPDATE tourist SET ban=?, ban_expiration_date=null WHERE tourist_id=?");
        $stmt->execute([$status, $tourist_id]);

        // Set success message and redirect to show_reports.php
        $successMsg = 'تم رفع حظر عن السائح بنجاح';
        header("Location: show_reports.php?success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection
    }
}

