<?php

// Start the session
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include validation functions and database connection
include "../validate.php";
include "../connection.php";

// Initialize variables
$password = $name = $confirmPassword = '';
$passwordError = $nameError = $confirmPasswordError = '';
$successMsg = $_GET['success_message'] ?? '';

// Get supervisor email from session
$supervisorEmail = $_SESSION['email'];

// Query the supervisor data from the database based on email
$query = $con->prepare("SELECT * FROM supervisor WHERE email=?");
$query->execute([$supervisorEmail]);

// Fetch the supervisor data
$supervisor = $query->fetch();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and encode the password
    $name = validate($_POST['name']);
    $password = validate($_POST['password']);
    $confirmPassword = validate($_POST['confirmPassword']);

    // Check if name is empty
    if (empty($name)) {
        $nameError = 'برجاء أدخال اسم المستخدم';
    } // Check if password is empty
    elseif (empty($password)) {
        $passwordError = 'الرجاء أدخال كلمة المرور';
    } // Check if password length is less than 8 characters
    elseif (strlen($password) < 8) {
        $passwordError = 'كلمة المرور يجب أن تكون أكثر من 8 حروف';
    } // Check if password meets complexity requirements
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).*$/', $password)) {
        $passwordError = 'يجب أن تحتوي كلمة المرور على حرف واحد على الأقل، رقم واحد على الأقل، ورمز واحد على الأقل';
    } elseif (empty($confirmPassword)) {
        $confirmPasswordError = 'الرجاء تأكيد كلمة المرور';
    } elseif ($password !== $confirmPassword) {
        $confirmPasswordError = 'كلمة السر غير مطابقة';
    } else {
        // Update password and name in the database
        $stmt = $con->prepare("UPDATE supervisor SET name=?, password=? WHERE email=?");
        $stmt->execute([$name, $password, $supervisorEmail]);

        // Set success message and redirect
        $successMsg = 'تم تعديل بيانات الملف الشخصي بنجاح';
        header("Location: edit_profile.php?success_message=" . urlencode($successMsg));
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
    الملف الشخصي المشرف

</h1>

<a type="button" href="../logout.php">تسجيل الخروج</a>

<br><br>

<a href=dashboard.php>الصفحة الرئيسية</a>

<br><br><br>


<span class="success"><?php
    echo $successMsg ?></span>

<br><br>

<form method="post" action="<?php
echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

    <label for="name">اسم المسئول</label>
    <input type="text" id="name" name="name" value="<?php
    echo $supervisor['name'] ?>"/>
    <span class="error"> <?php
        echo $nameError ?></span>

    <br><br>

    <label for="password">كلمة المرور</label>
    <input type="password" id="password" name="password"/>
    <span class="error"> <?php
        echo $passwordError ?></span>

    <br><br>
    <label for="confirmPassword">تأكيد كلمة المرور</label>
    <input type="password" id="confirmPassword" name="confirmPassword"/>
    <span class="error"> <?php
        echo $confirmPasswordError ?></span>

    <br><br>

    <button type="submit" name="editProfile">
        تعديل الملف الشخصي
    </button>

</form>

</body>
</html>