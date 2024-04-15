<?php
//// Start session to maintain user's session data
//session_start();
//if (!(isset($_SESSION['email']))) {
//    header('Location../login.php');
//}
//// Include the file containing database connection details
//include "../connection.php";
//
//// Retrieve supervisor ID from GET parameter or set to 0 if not provided
//$supervisor_id = isset($_GET['supervisor_id']) && is_numeric($_GET['supervisor_id']) ? intval($_GET['supervisor_id']) : 0;
//
//// Check if form is submitted via POST method
//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//
//    // If inputs are valid, prepare and execute SQL query to delete supervisor data
//    $stmt = $con->prepare("DELETE FROM supervisor WHERE supervisor_id=?");
//    $stmt->execute([$supervisor_id]);
//
//    // Set success message
//    $successMsg = 'تم ازالة  المشرف بنجاح';
//
//    // Redirect to show_supervisor.php page with success message
//    header("Location:show_supervisor.php?success_message=" . urlencode($successMsg));
//    exit; // Exit to prevent further execution after redirection
//
//}
