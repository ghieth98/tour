<?php
// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the database connection file
include "../../connection.php";

// Get the tourist ID from the session
$tourist_id = $_SESSION['tourist_id'];

// Get the destination ID from the URL, if it exists and is numeric
$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval($_GET['destination_id']) : 0;

$query = $con->prepare("SELECT * FROM rate LEFT JOIN tours.tourist t on t.tourist_id = rate.tourist_id WHERE destination_id=?");
$query->execute([$destination_id]);
$ratings = $query->fetchAll();



// Initialize variables for stars and error message
$stars = '';
$stars_error = '';

// Initialize variable for success message
$successMsg = '';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the value of stars from the form
    $stars = $_POST['star'];
    $city_id = $_POST['city_id'];

    // Check if stars is empty
    if (empty($stars)) {
        // Set an error message if stars is empty
        $stars_error = 'برجاء أدخال التقييم';
        // Redirect back to the destination page with the error message
        header("Location:../../destination_info.php?destination_id=" . $destination_id . "&success_message=" . urlencode
            ($stars_error));
    }
    foreach ($ratings as $rating) {
        if ($rating['tourist_id'] === $tourist_id) {
            $successMsg = 'تم اضافة تقييم بالفعل';
            // Redirect back to the destination page with the success message
            header("Location:../../destination_info.php?destination_id=" . $destination_id . "&success_message=" . urlencode
                ($successMsg));
            // Exit the script
            exit;
        }

    }
        // Prepare a statement to insert the rating into the database
        $stmt = $con->prepare("INSERT INTO rate (stars, tourist_id, destination_id, city_id) VALUES (?,?,?, ?)");
        // Execute the statement with stars, tourist_id, and destination_id as parameters
        $stmt->execute([$stars, $tourist_id, $destination_id, $city_id]);
        // Set success message for successful rating submission
        $successMsg = 'تم إضافة التقييم بنجاح';
//         Redirect back to the destination page with the success message
        header("Location:../../destination_info.php?destination_id=" . $destination_id . "&success_message=" . urlencode
            ($successMsg));
        // Exit the script
        exit;

}

