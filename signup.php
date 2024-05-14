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
    $confirmPassword = validate($_POST['confirmPassword']);

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
    } elseif ($emails_count > 0) {
        foreach ($tourists_emails as $tourists_email) {
            if ($tourists_email['email'] == $email) {
                $emailError = 'هذا البريد الإلكتروني موجود مسبقا';
            }
        }
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
        header("Location: login.php?message=".urlencode($success_message)); // Redirect to login page with success
        // message
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

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet">
    <link href="assets/css/jquery.fancybox.min.css" rel="stylesheet">
    <link href="fonts/icomoon/style.css" rel="stylesheet">
    <link href="fonts/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="assets/css/daterangepicker.css" rel="stylesheet">
    <link href="assets/css/aos.css" rel="stylesheet">
    <link href="assets/css/style111243.css" rel="stylesheet">

    <title>توصية بالجولات</title>

    <style>
        .error {
            color: red
        }
    </style>
</head>

<body>
<div class="site-mobile-menu site-navbar-target">
    <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close">
            <span class="icofont-close js-menu-toggle"></span>
        </div>
    </div>
    <div class="site-mobile-menu-body"></div>
</div>

<!--Start Navbar Section-->
<nav class="site-nav">
    <div class="container">
        <div class="site-navigation">
            <a class="logo m-0 float-right">
                <img src="assets/images/logo.PNG" alt="" style="height: 160px; padding-bottom: 50px">
                <span class="text-primary">
                </span>
            </a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left" style="margin-left:
            40px; margin-top: 10px; font-size: 40px">

                <li class="active"><a href="index.php">الرئيسية</a></li>
                <li class="active"><a href="signup.php">تسجيل جديد</a></li>

                <?php
                if (isset($tourist_id)): ?>
                    <li><a class="fa-solid fa-user" href="tourist/dashboard.php"></a></li>
                    <li><a href="logout.php">تسجيل الخروج</a></li>

                <?php
                elseif (isset($supervisor_id)): ?>

                    <li><a class="fa-solid fa-user" href="supervisor/dashboard.php"></a></li>
                    <li><a href="logout.php">تسجيل الخروج</a></li>

                <?php
                elseif (isset($administrator_id)): ?>

                    <li><a class="fa-solid fa-user" href="administrator/dashboard.php"></a></li>
                    <li><a href="logout.php">تسجيل الخروج</a></li>


                <?php
                endif; ?>

            </ul>

            <a class="burger ml-auto float-left site-menu-toggle js-menu-toggle d-inline-block d-lg-none light"
               data-target="#main-navbar"
               data-toggle="collapse" href="index.php">
                <span></span>
            </a>

        </div>
    </div>
</nav>
<!--End navbar Section-->

<!-- Start Hero Section-->
<div class="hero hero-inner" style="
    background: url('assets/images/mohammed-alorabi-_ABfNIGGsZk-unsplash.jpg')  center ;
    background-size: cover;
    position: relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="intro-wrap">

            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<!--Start Signup Section-->
<div class="untree_co-section">
    <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-12 col-xl-8 ">
                <div class="card text-black shadow-lg" style="border-radius: 25px;">
                    <div class="card-body p-md-5 ">
                        <div class="row justify-content-center ">
                            <div class="col-md-10 col-lg-12 col-xl-12 order-2 order-lg-1">
                                <h2 class="text-center mb-5">تسجيل حساب جديد</h2>
                                <form class="mx-1 mx-md-4" id="registerForm" method="post" action="<?php
                                echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" style="font-size: 17px">


                                    <div class="d-flex flex-row align-items-center mb-4">
                                        <div class="form-outline flex-fill mb-0 text-right">
                                            <label class="form-label " for="name">الاسم</label>
                                            <input class="form-control" id="name" name="name"
                                                   value="<?php
                                                   echo $name ?>"
                                                   type="text"/>
                                            <span class="error"><?php
                                                echo $nameError ?></span>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-row align-items-center mb-4">
                                        <div class="form-outline flex-fill mb-0 text-right">
                                            <label class="form-label" for="email">البريد الإلكتروني</label>
                                            <input class="form-control" id="email" value="<?php
                                            echo $email ?>"
                                                   name="email"
                                                   type="email"/>
                                            <span class="error"><?php
                                                echo $emailError ?></span>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-row align-items-center mb-4">
                                        <div class="form-outline flex-fill mb-0 text-right">
                                            <label class="form-label" for="password">كلمة المرور</label>
                                            <input class="form-control" id="password" name="password" type="password"/>
                                            <span class="error"><?php
                                                echo $passwordError ?></span>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-row align-items-center mb-4">
                                        <div class="form-outline flex-fill mb-0 text-right">
                                            <label class="form-label" for="confirmPassword">تأكيد كلمة المرور</label>
                                            <input class="form-control" id="confirmPassword" name="confirmPassword"
                                                   type="password"/>
                                            <span class="error"><?php
                                                echo $confirmPasswordError ?></span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center mx-4 mb-2 mb-lg-2">
                                        <button class="btn btn-primary btn-lg" style="font-size: 17px" name="register"
                                                type="submit">تسجيل
                                            جديد
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End SignUp Section-->

<!--Start Footer Section-->
<div class="site-footer ">
    <div class="inner first">
        <div class="inner dark">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-8  mb-md-0 mx-auto">
                        <p>
                            جميع الحقوق محفوظة للتوصية بالجولات @ 2024
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Footer Section-->

<!--<div id="overlayer"></div>-->
<!--<div class="loader">-->
<!--    <div class="spinner-border" role="status">-->
<!--        <span class="sr-only">Loading...</span>-->
<!--    </div>-->
<!--</div>-->

<script src="assets/js/jquery-3.4.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/jquery.animateNumber.min.js"></script>
<script src="assets/js/jquery.waypoints.min.js"></script>
<script src="assets/js/jquery.fancybox.min.js"></script>
<script src="assets/js/aos.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/daterangepicker.js"></script>

<script src="assets/js/typed.js"></script>

<script src="assets/js/custom.js"></script>

</body>

</html>
