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
$name = $password = $confirmPassword = '';
$nameError = $passwordError = $confirmPasswordError = '';

// Get admin email from session
$admin_email = $_SESSION['email'];
$successMsg = $_GET['success_message'] ?? '';

$query = $con->prepare("SELECT * FROM administrator WHERE email=?");
$query->execute([$admin_email]);

$admin = $query->fetch();


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the name and the password and confirm password
    $name = validate($_POST['name']);
    $password = validate($_POST['password']);
    $confirmPassword = validate($_POST['confirm_password']);

    // Check if password is empty
    if (empty($name)) {
        $nameError = 'الرجاء إدخال الاسم';
    } elseif (strlen($name) < 3) {
        $nameError = 'الاسم لا يمكن ان يقل عن 3 حروف';
    } elseif (empty($password)) {
        $passwordError = 'الرجاء أدخال كلمة المرور';
    } // Check if password length is less than 8 characters
    elseif (strlen($password) < 8) {
        $passwordError = 'كلمة المرور يجب أن تكون أكثر من 8 حروف';
    } // Check if password meets complexity requirements
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).*$/', $password)) {
        $passwordError = 'يجب أن تحتوي كلمة المرور على حرف واحد على الأقل، رقم واحد على الأقل، ورمز واحد على الأقل';
    } elseif (empty($confirmPassword)) {
        $confirmPasswordError = 'الرجاء أدخال تأكيد كلمة المرور';
    } elseif ($password !== $confirmPassword) {
        $confirmPasswordError = 'كلمة السر غير مطابقة';
    } else {
        // Update admin data in the database
        $stmt = $con->prepare("UPDATE administrator SET name=?,password=? WHERE email=?");
        $stmt->execute([$name, $password, $admin_email]);

        // Set success message
        $successMsg = 'تم تعديل بيانات المدير بنجاح';

        // Redirect to show_supervisor.php page with success message
        header("Location:edit_profile.php?success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection
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
    تعديل بيانات الملف الشخصي للمدير

</h1>

<span class="success"><?php
    echo $successMsg ?></span>

<br><br>

<a type="button" href="../logout.php">تسجيل الخروج</a>

<br><br>

<a href="dashboard.php">الصفحة الرئيسة</a>

<br><br><br>


<form method="post"
      action="<?php
      echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">

    <label for="name">الاسم</label>
    <input type="text" id="name" name="name" value="<?php
    echo $admin['name'] ?>"/>
    <span class="error"> <?php
        echo $nameError ?></span>
    <br><br>
    <label for="password">كلمة المرور</label>
    <input type="password" id="password" name="password"/>
    <span class="error"> <?php
        echo $passwordError ?></span>

    <br><br>

    <label for="confirm_password">تأكيد كلمة المرور</label>
    <input type="password" id="confirm_password" name="confirm_password"/>
    <span class="error"> <?php
        echo $confirmPasswordError ?></span>

    <br><br>

    <button type="submit" name="editProfile">
        تعديل البيانات الشخصية
    </button>

</form>

</body>
</html>
