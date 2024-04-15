<?php
//// Start session to maintain user's session data
//session_start();
//if (!(isset($_SESSION['email']))) {
//    header('Location../login.php');
//}
//// Include necessary files for validation and database connection
//include "../validate.php";
//include "../connection.php";
//
//// Retrieve supervisor ID from GET parameter or set to 0 if not provided
//$supervisor_id = isset($_GET['supervisor_id']) && is_numeric($_GET['supervisor_id']) ? intval($_GET['supervisor_id']) : 0;
//
//// Prepare and execute SQL query to retrieve supervisor data based on ID
//$query = $con->prepare("SELECT * FROM supervisor WHERE supervisor_id=?");
//$query->execute([$supervisor_id]);
//
//// Fetch supervisor data
//$supervisor = $query->fetch();
//
//
//// Initialize variables for form inputs and error messages
//$name = '';
//$nameError = $passwordError = '';
//$successMsg = '';
//
//// Check if form is submitted via POST method
//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//    // Retrieve and validate form inputs
//    $name = validate($_POST['name']);
//    $password = validate($_POST['password']);
//
//    // Validate name input
//    if (empty($name)) {
//        $nameError = 'الرجاء أدخال اسم المشرف';
//    } else {
//        // If inputs are valid, prepare and execute SQL query to update supervisor data
//        $stmt = $con->prepare("UPDATE supervisor SET name=? WHERE supervisor_id=?");
//        $stmt->execute([$name, $supervisor_id]);
//        $successMsg = 'تم تعديل بيانات المشرف بنجاح';
//
//        // Redirect to show_supervisor.php page with success message
//        header("Location:show_supervisor.php?success_message=" . urlencode($successMsg));
//        exit; // Exit to prevent further execution after redirection
//    }
//}
//?>
<!---->
<!--<html lang="ar" dir="rtl">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport"-->
<!--          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">-->
<!--    <meta http-equiv="X-UA-Compatible" content="ie=edge">-->
<!--    <title>تصوية بالجولات</title>-->
<!---->
<!--    <style>-->
<!--        .error {-->
<!--            color: red-->
<!--        }-->
<!---->
<!--        .success {-->
<!--            color: green;-->
<!--        }-->
<!--    </style>-->
<!---->
<!--</head>-->
<!--<body>-->
<!--<h1>-->
<!--    تعديل بيانات المشرف-->
<!--</h1>-->
<!---->
<!---->
<!--<form method="POST"-->
<!--      action="--><?php //echo htmlspecialchars($_SERVER['REQUEST_URI']) ?><!--">-->
<!---->
<!--    <label for="name">اسم المشرف</label>-->
<!--    <input type="text" id="name" name="name"-->
<!--           value="--><?php //echo $supervisor['name'] ?><!--"/>-->
<!--    <span class="error"> --><?php //echo $nameError ?><!--</span>-->
<!---->
<!--    <br><br>-->
<!---->
<!---->
<!--    <br><br>-->
<!--    <button type="submit" name="editSupervisor">-->
<!--        تعديل-->
<!--    </button>-->
<!---->
<!--</form>-->
<!---->
<!---->
<!--</body>-->
<!--</html>-->
