<?php

// Start the session
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include validation functions and database connection
include "../validate.php";
include "../connection.php";

$successMsg = $_GET['success_message'] ?? '';

// Get supervisor email from session
$supervisorEmail = $_SESSION['email'];


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
    الملف الشخصي المشرف

</h1>

<a type="button" href="../logout.php">تسجيل الخروج</a>

<br><br>

<a href="show_comments.php">عرض ريفيو</a>



<br><br><br>

<a href="show_reports.php">عرض البلاغات</a>

<br><br><br>

<a href="show_cities.php">عرض المدن</a>

<br><br><br>


<a href="show_tourists.php">أدارة السياح</a>

<br><br><br>
<a href="edit_profile.php">تعديل بيانات الملف الشخصي</a>

<br><br>


</body>
</html>