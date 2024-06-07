<?php
// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the validate function file
include "../validate.php";

// Include the database connection file
include "../connection.php";

// Get the tourist ID from the session
$tourist_id = $_SESSION['tourist_id'];

// Get the reply ID from the URL, if it exists and is numeric
$reply_id = isset($_GET['reply_id']) && is_numeric($_GET['reply_id']) ? intval($_GET['reply_id']) : 0;

// Get the review ID from the URL, if it exists and is numeric
$review_id = isset($_GET['review_id']) && is_numeric($_GET['review_id']) ? intval($_GET['review_id']) : 0;

// Get the destination ID from the URL, if it exists and is numeric
$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval($_GET['destination_id']) : 0;

// Get the type from the URL, if it exists and is a string
$type = isset($_GET['type']) && is_string($_GET['type']) ? ($_GET['type']) : '';

// Initialize variables for body, body error, and success message
$body = $body_error = '';
$successMsg = '';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the body of the report
    $body = validate($_POST['body']);

    // Check the type of report (review or reply)
    if ($type === 'review') {
        // Check if body is empty for a review report
        if (empty($body)) {
            // Set an error message if body is empty
            $body_error = 'برجاء أدخال نص البلاغ';
        } else {
            // Prepare a statement to insert the report for a review into the database
            $stmt = $con->prepare("INSERT INTO report (body, date, review_id, reply_id, tourist_id) VALUES (?, NOW(), ?, ?, ?)");
            // Execute the statement with body, review_id, reply_id (null for review), and tourist_id as parameters
            $stmt->execute([$body, $review_id, null, $tourist_id]);
            // Set success message for successful report submission
            $successMsg = 'تم إضافة البلاغ بنجاح';
            // Redirect back to the destination page with the success message
            header("Location:show_destination.php?destination_id=" . $destination_id . "&success_message=" . urlencode($successMsg));
            // Exit the script
            exit;
        }
    } elseif ($type === 'reply') {
        // Check if body is empty for a reply report
        if (empty($body)) {
            // Set an error message if body is empty
            $body_error = 'برجاء أدخال نص البلاغ';
        } else {
            // Prepare a statement to insert the report for a reply into the database
            $stmt = $con->prepare("INSERT INTO report (body, date, review_id, reply_id, tourist_id) VALUES (?, NOW(), ?, ?, ?)");
            // Execute the statement with body, review_id (null for reply), reply_id, and tourist_id as parameters
            $stmt->execute([$body, null, $reply_id, $tourist_id]);
            // Set success message for successful report submission
            $successMsg = 'تم إضافة البلاغ بنجاح';
            // Redirect back to the destination page with the success message
            header("Location:show_destination.php?destination_id=" . $destination_id . "&success_message=" . urlencode($successMsg));
            // Exit the script
            exit;
        }
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
    رفع بلاغ
</h1>


<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="POST">
    <div class="form-group">
        <label for="body"></label>
        <textarea class="form-control" cols="30" id="body" name="body" rows="4"></textarea>
        <span><?php echo $body_error ?></span>
    </div>

    <div class="form-group ">
        <button type="submit">
            رفع البلاغ
        </button>
    </div>
</form>


</body>
</html>
