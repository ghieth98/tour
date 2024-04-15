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

$reply_id = isset($_GET['reply_id']) && is_numeric($_GET['reply_id']) ? intval($_GET['reply_id']) : 0;
$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval($_GET['destination_id'])
    : 0;


// Prepare a statement to select the old reply based on reply_id and tourist_id
$stmt = $con->prepare("SELECT * FROM reply WHERE reply_id=? AND tourist_id=?");
// Execute the statement with reply_id and tourist_id as parameters
$stmt->execute([$reply_id, $tourist_id]);
// Fetch the old reply data
$old_reply = $stmt->fetch();
// Get the review ID from the old reply
$review_id = $old_reply['review_id'];

// Initialize variables for body, body error, and success message
$body = $body_error = '';
$successMsg = '';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the body of the reply
    $body = validate($_POST['body']);

    // Check if body is empty
    if (empty($body)) {
        // Set an error message if body is empty
        $body_error = 'برجاء أدخال نص الرد';
    } else {
        // Prepare a statement to update the reply in the database
        $stmt = $con->prepare("UPDATE reply SET body=?, date=NOW() WHERE review_id=? AND  tourist_id= ?");
        // Execute the statement with body, review_id, and tourist_id as parameters to update the reply
        $stmt->execute([$body, $review_id, $tourist_id]);
        // Set success message for successful reply update
        $successMsg = 'تم تعديل بيانات الرد بنجاح';
        // Redirect back to the destination page with the success message
        header("Location:../../destination_info.php?destination_id=".$destination_id."&success_message=".urlencode
            ($successMsg));
        // Exit the script
        exit;
    }
}
?>

<!doctype html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600&family=El+Messiri:wght@400;500;600;700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&display=swap"
        rel="stylesheet">

    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/owl.carousel.min.css" rel="stylesheet">
    <link href="../../assets/css/owl.theme.default.min.css" rel="stylesheet">
    <link href="../../assets/css/jquery.fancybox.min.css" rel="stylesheet">
    <link href="fonts/icomoon/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="fonts/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="../../assets/css/daterangepicker.css" rel="stylesheet">
    <link href="../../assets/css/aos.css" rel="stylesheet">
    <link href="../../assets/css/style111243.css" rel="stylesheet">

    <title>توصية بالجولات</title>
    <style>
        .success {
            color: green;
        }
    </style>
</head>
<body>

<div>
    <h1 class="text-center">
        تعديل بيانات المراجعة
    </h1>

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">الرد على التعليق</h5>
            </div>
            <div class="modal-body">
                <form action="<?php
                echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>"
                      method="post">
                    <div class="form-group">
                        <label for="body"></label>
                        <textarea class="form-control" cols="30" id="body" name="body" rows="4"><?php echo
                            $old_reply['body']
                            ?></textarea>
                        <span><?php
                            echo $body_error ?></span>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-primary" type="submit">اضف الرد</button>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">اغلاق</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div id="overlayer"></div>
<div class="loader">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<script src="../../assets/js/jquery-3.4.1.min.js"></script>
<script src="../../assets/js/popper.min.js"></script>
<script src="../../assets/js/bootstrap.min.js"></script>
<script src="../../assets/js/owl.carousel.min.js"></script>
<script src="../../assets/js/jquery.animateNumber.min.js"></script>
<script src="../../assets/js/jquery.waypoints.min.js"></script>
<script src="../../assets/js/jquery.fancybox.min.js"></script>
<script src="../../assets/js/aos.js"></script>
<script src="../../assets/js/moment.min.js"></script>
<script src="../../assets/js/daterangepicker.js"></script>

<script src="../../assets/js/typed.js"></script>

<script src="../../assets/js/custom.js"></script>

</body>
</html>