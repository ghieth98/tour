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
// Retrieve supervisor ID from GET parameter or set to 0 if not provided
$supervisor_id = isset($_GET['supervisor_id']) && is_numeric($_GET['supervisor_id']) ? intval($_GET['supervisor_id'])
    : 0;

$status = $_POST['ban'];
$ban = '';


// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the action from the form or set to empty string if not provided

    // Perform action based on the value received from the form
    if ($status === 'banned') {
        // Prepare and execute SQL statement to ban the supervisor
        $stmt = $con->prepare("UPDATE supervisor SET ban=? WHERE supervisor_id=?");
        $stmt->execute([$status, $supervisor_id]);
        // Set success message and redirect to dashboard.php
        $successMsg = 'تم حظر المشرف بنجاح';
        header("Location: dashboard.php?success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection

    } elseif ($status === 'temporary') {
        $ban_expiration_date = date('Y-m-d H:i:s', strtotime('+1 month'));
        $stmt = $con->prepare("UPDATE supervisor SET ban=?, ban_expiration_date=? WHERE supervisor_id=?");
        $stmt->execute([$status, $ban_expiration_date, $supervisor_id]);
        $successMsg = 'تم حظر  المشرف مؤقتا بنجاح';
        header("Location: dashboard.php?success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection

    } elseif ($status === 'unbanned') {
        // Prepare and execute SQL statement to unban the supervisor
        $stmt = $con->prepare("UPDATE supervisor SET ban=?, ban_expiration_date=null WHERE supervisor_id=?");
        $stmt->execute([$status, $supervisor_id]);

        // Set success message and redirect to dashboard.php
        $successMsg = 'تم رفع حظر عن المشرف بنجاح';
        header("Location: dashboard.php?success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection
    }
}




