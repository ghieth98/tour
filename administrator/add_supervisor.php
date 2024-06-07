<?php
// Start session to maintain user's session data
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../validate.php";
include "../connection.php";

// Initialize variables for form fields and error messages
$name = $email = $password = '';
$nameError = $emailError = $passwordError = '';
$successMsg = '';

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate form inputs
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = generateRandomPassword(8);

    // Check if email already exists in database
    $email_query = $con->prepare("SELECT * FROM supervisor WHERE email=?");
    $email_query->execute([$email]);
    $supervisors_emails = $email_query->fetchAll();
    $emails_count = $email_query->rowCount();

    // Validate name
    if (empty($name)) {
        $nameError = 'الرجاء أدخال اسم المشرف';
    } // Validate email
    elseif (empty($email)) {
        $emailError = 'الرجاء أدخال البريد الإلكتروني';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'صيغة البريد الإلكتروني غير صحيحة';
    } elseif ($emails_count > 0) { // Check if email already exists
        foreach ($supervisors_emails as $supervisors_email) {
            if ($supervisors_email['email'] == $email) {
                $emailError = 'هذا البريد الإلكتروني موجود مسبقا';
            }
        }
    } else {
        // If all validations pass, insert new supervisor into the database
        $stmt = $con->prepare("INSERT INTO supervisor(name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        $successMsg = 'تم إضافة مسؤول جديد بنجاح';

        require "../Mail/phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';

        $mail->Username = ''; //gmail here
        $mail->Password = ''; // password here

        $mail->setFrom('', 'Supervisor Credentials');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Supervisor Credentials";
        $mail->Body = "<p>عزيزي المشرف/ " . $name . ", </p>
 <h3>كلمة المرور الخاصة بك هى $password <br>
 </h3>
                <br><br>
                <p>مع اطيب التمنيات</p>
                <b>منصة التوصية بالجولات</b>";

        if ($mail->send()) {

            // Redirect to show_supervisor.php page with success message
            header("Location:show_supervisor.php?success_message=" . urlencode($successMsg));
            exit; // Exit to prevent further execution after redirection
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

        .success {
            color: green;
        }
    </style>

</head>
<body>

<h1>
    إضافة مشرف جديد
</h1>


<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">

    <label for="name">اسم المشرف</label>
    <input type="text" id="name" name="name" value="<?php echo $name ?>"/>
    <span class="error"> <?php echo $nameError ?></span>

    <br><br>

    <label for="email">البريد الإلكتروني</label>
    <input type="email" id="email" name="email" value="<?php echo $email ?>"/>
    <span class="error"> <?php echo $emailError ?></span>

    <br><br>


    <br><br>

    <button type="submit" name="addSupervisor">
        إضافة
    </button>

</form>

</body>
</html>
