<?php

session_start(); // Start the session for user authentication
include "validate.php"; // Include the file for input validation functions
include "connection.php"; // Include the file for database connection
$success_message = '';

if (isset($_GET['message'])) {
    $success_message = 'تم إضافة الحساب بنجاح'; // Set success message if present in the URL
}

// Initialize form variables and set to empty values
$email = $password = '';
$emailError = $passwordError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form variables using the validate function (assuming it's defined in validate.php)
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    // Query administrator table for email and password
    $administrator_query = $con->prepare("SELECT * FROM administrator WHERE email=? AND password=?");
    $admin_result = $administrator_query->execute(array($email, $password));
    $administrator = $administrator_query->fetchAll();
    $administrator_count = $administrator_query->rowCount();

    // Query supervisor table for email and password
    $supervisor_query = $con->prepare("SELECT * FROM supervisor WHERE email=? AND password=?");
    $supervisor_result = $supervisor_query->execute(array($email, $password));
    $supervisor = $supervisor_query->fetchAll();
    $supervisor_count = $supervisor_query->rowCount();

    // Query tourist table for email and password
    $tourist_query = $con->prepare("SELECT * FROM tourist WHERE email=? AND password=?");
    $tourist_result = $tourist_query->execute(array($email, $password));
    $tourist = $tourist_query->fetchAll();
    $tourist_count = $tourist_query->rowCount();

    // Validation Conditions
    if (empty($email)) {
        $emailError = 'برجاء أدخال البريد الإلكتروني';
    } elseif (empty($password)) {
        $passwordError = 'برجاء أدخال كلمة المرور';
    } else {
        // Check if the user is an administrator
        if ($administrator_count === 1) {
            foreach ($administrator as $item) {
                if ($item['email'] === $email && $item['password'] === $password) {
                    $_SESSION['administrator_id'] = $item['administrator_id'];
                    $_SESSION['email'] = $item['email'];
                    $_SESSION['password'] = $item['password'];
                    header("Location: administrator/dashboard.php"); // Redirect to the administrator dashboard
                }
            }
            $con = null;
        } // Check if the user is a supervisor
        elseif ($supervisor_count > 0) {
            foreach ($supervisor as $item) {
                if ($item['ban'] === 'banned' || $item['ban'] === 'temporary') {
                    $emailError = 'تم الحظر لا يمكن الدخول إلي الحساب';
                } elseif ($item['email'] === $email && $item['password'] === $password) {
                    $_SESSION['supervisor_id'] = $item['supervisor_id'];
                    $_SESSION['name'] = $item['name'];
                    $_SESSION['email'] = $item['email'];
                    $_SESSION['password'] = $item['password'];
                    header("Location: supervisor/dashboard.php"); // Redirect to the supervisor dashboard
                }
            }
            $con = null;
        } // Check if the user is a tourist
        elseif ($tourist_count > 0) {
            foreach ($tourist as $item) {
                if ($item['ban'] === 'banned' || $item['ban'] === 'temporary') {
                    $emailError = 'تم الحظر لا يمكن الدخول إلي الحساب';
                } elseif ($item['email'] === $email && $item['password'] === $password) {
                    $_SESSION['tourist_id'] = $item['tourist_id'];
                    $_SESSION['email'] = $item['email'];
                    $_SESSION['password'] = $item['password'];
                    header("Location: tourist/dashboard.php"); // Redirect to the tourist dashboard
                }
            }
            $con = null;
        } else {
            $emailError = 'برجاء أدخال البيانات مره أخرى'; // Display error if no matching user is found

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
    <title>تصوية بالجولات</title>
    <style>
        .error {
            color: red
        }
    </style>
</head>
<body>

<div>
    <?php
    echo $success_message ?>
</div>

<form method="post" action="<?php
echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">


    <label for="email">البريد الإلكتروني</label>
    <input type="email" id="email" name="email" value="<?php
    echo $email ?>"/>
    <span class="error"> <?php
        echo $emailError ?></span>

    <br><br>

    <label for="password">كلمة المرور</label>
    <input type="password" id="password" name="password"/>
    <span class="error"> <?php
        echo $passwordError ?></span>

    <br><br>

    <button type="submit" name="register">
        تسجيل الدخول
    </button>


</form>


</body>
</html>