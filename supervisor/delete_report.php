<?php
// Start session to maintain user's session data
session_start();

if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the file containing database connection details
include "../connection.php";

// Retrieve report ID from GET parameter or set to 0 if not provided
$report_id = isset($_GET['report_id']) && is_numeric($_GET['report_id']) ? intval($_GET['report_id']) : 0;

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If inputs are valid, prepare and execute SQL query to delete report data
    $stmt = $con->prepare("DELETE FROM report WHERE report_id=?");
    $stmt->execute([$report_id]);

    // Set success message
    $successMsg = 'تم ازالة البلاغ بنجاح';

    // Redirect to show_report.php page with success message
    header("Location:show_reports.php?success_message=" . urlencode($successMsg));
    exit; // Exit to prevent further execution after redirection

}
