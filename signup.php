<?php
include "validate.php"; // Include the file for input validation functions
include "connection.php"; // Include the file for database connection

// Initialize form variables and set to empty values
$name = $email = $password = $confirmPassword = '';
$nameError = $emailError = $passwordError = $confirmPasswordError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate form variables using the validate function
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']); // Hash password
    $confirmPassword = validate($_POST['confirm_password']); // Hash confirm password

    // Query database for tourist emails
    $email_query = $con->prepare("SELECT * FROM tourist WHERE email=?");
    $email_query->execute([$email]);
    $tourists_emails = $email_query->fetchAll();
    $emails_count = $email_query->rowCount();

    // Validation Conditions
    if (empty($name)) {

        $nameError = 'الرجاء أدخال اسم المستخدم';

    } elseif (empty($email)) {

        $emailError = 'الرجاء أدخال البريد الإلكتروني';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $emailError = 'صيغة البريد الإلكتروني غير صحيحة';

    } elseif (empty($password)) {

        $passwordError = 'الرجاء أدخال كلمة المرور';

    } elseif (strlen($password) <= 8) {

        $passwordError = 'كلمة المرور يجب أن تكون أكثر من 8 حروف';

    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).*$/', $password)) {

        $passwordError = 'يجب أن تحتوي كلمة المرور على حرف واحد على الأقل، رقم واحد على الأقل، ورمز واحد على الأقل';

    } elseif (empty($confirmPassword)) {

        $confirmPasswordError = 'الرجاء أدخال تأكيد كلمة المرور';

    } elseif ($password !== $confirmPassword) {

        $confirmPasswordError = 'كلمة السر غير مطابقة';

    } elseif ($emails_count > 0) {

        foreach ($tourists_emails as $tourist_email) {

            if ($tourist_email['email'] == $email) {

                $emailError = 'هذا البريد الإلكتروني موجود مسبقا';

            }
        }
    } else {
        // Insert data into tourist table
        $stmt = $con->prepare("INSERT INTO tourist (name, email, password) VALUES (?, ?, ?)");  // Prepared statement

        $stmt->execute([$name, $email, $password]);

        $success_message = 'تم التسجيل بنجاح';
        header("Location: login.php?message=" . urlencode($success_message)); // Redirect to login page with success
        // message
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

<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">


    <label for="name">الاسم</label>
    <input type="text" id="name" name="name" value="<?php echo $name ?>"/>
    <span class="error"> <?php echo $nameError ?></span>

    <br><br>

    <label for="email">البريد الإلكتروني</label>
    <input type="email" id="email" name="email" value="<?php echo $email ?>"/>
    <span class="error"> <?php echo $emailError ?></span>

    <br><br>

    <label for="password">كلمة المرور</label>
    <input type="password" id="password" name="password"/>
    <span class="error"> <?php echo $passwordError ?></span>

    <br><br>

    <label for="confirm_password">تأكيد كلمة المرور</label>
    <input type="password" id="confirm_password" name="confirm_password"/>
    <span class="error"> <?php echo $confirmPasswordError ?></span>

    <br><br>

    <button type="submit" name="register">
        تسجيل جديد
    </button>

</form>

</body>
</html>