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

// Get tourist email from session
$touristEmail = $_SESSION['email'];


?>


<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>تصوية بالجولات</title>
    <style>
        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
<h1>
    الملف الشخصي للسائح
</h1>

<a type="button" href="../logout.php">تسجيل الخروج</a>

<br><br>

<a href="show_cities.php">عرض المدن</a>

<br><br><br>

<a href="test.php">الاختبار</a>

<br><br><br>

<a href="favorites.php">صفحة المفضلات</a>

<br><br><br>

<a href="edit_profile.php">تعديل بيانات الشخصية</a>

<br><br><br>


</body>
</html>