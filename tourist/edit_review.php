<?php
// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the database connection file
include "../connection.php";

// Include the validate function file
include "../validate.php";

// Get the tourist ID from the session
$tourist_id = $_SESSION['tourist_id'];

// Get the review ID from the URL, if it exists and is numeric
$review_id = isset($_GET['review_id']) && is_numeric($_GET['review_id']) ? intval($_GET['review_id']) : 0;

// Prepare a statement to select the destination ID based on the review ID
$query = $con->prepare("SELECT destination_id FROM review WHERE review_id = ?");
// Execute the statement with review_id as parameter
$query->execute([$review_id]);
// Fetch the destination ID
$destination = $query->fetch();
$destination_id = $destination['destination_id'];


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
        // Prepare a statement to update the review in the database
        $stmt = $con->prepare("UPDATE review SET body=?, date=NOW() WHERE tourist_id=? AND review_id=?");
        // Execute the statement with body, tourist_id, and review_id as parameters to update the review
        $stmt->execute([$body, $tourist_id, $review_id]);
        // Set success message for successful review update
        $successMsg = 'تم تعديل بيانات المراجعة بنجاح';
        // Redirect back to the destination page with the success message
        header("Location:show_destination.php?destination_id=" . $destination_id . "&success_message=" . urlencode($successMsg));
        // Exit the script
        exit;
    }
}
?>


<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>توصية بالجولات</title>

    <style>
        .error {
            color: red
        }

        .success {
            color: green;
        }
    </style>

</head>
<body>
<h1>
    تعديل بيانات المراجعة
</h1>


<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="POST">
    <div class="form-group">
        <label for="body"></label>
        <textarea class="form-control" cols="30" id="body" name="body" rows="4"></textarea>
        <span><?php echo $body_error ?></span>
    </div>

    <div class="form-group ">
        <button type="submit">
            اضف المراجعة
        </button>
    </div>
</form>


</body>
</html>
