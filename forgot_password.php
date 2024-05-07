<?php

session_start(); // Start the session for user authentication
include "validate.php"; // Include the file for input validation functions
include "connection.php"; // Include the file for database connection
require "Mail/phpmailer/PHPMailerAutoload.php"; // Include PHPMailer library

// Initialize form variables and set to empty values
$email = '';
$emailError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form variables using the validate function (assuming it's defined in validate.php)
    $email = validate($_POST['email']);
    $password = generateRandomPassword(8);

    // Query tourist table for email and password
    $tourist_query = $con->prepare("SELECT * FROM tourist WHERE email=? ");
    $tourist_query->execute(array($email));
    $tourist = $tourist_query->fetchAll();
    $tourist_count = $tourist_query->rowCount();

    // Validation Conditions
    if (empty($email)) {
        $emailError = 'برجاء أدخال البريد الإلكتروني';
    } elseif ($tourist_count > 0) {
        foreach ($tourist as $item) {
            if ($item['ban'] === 'banned' || $item['ban'] === 'temporary') {
                $emailError = 'تم الحظر لا يمكن الدخول إلي الحساب';
            } elseif ($item['email'] === $email) {

                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = "base64";
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';

                $mail->Username = ''; //gmail here
                $mail->Password = ''; // password here

                $mail->setFrom('', 'Supervisor Credentials'); //Insert gmail here
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Supervisor Credentials";
                $mail->Body = "<p>عزيزي المستخدم/ </p>
                 <h3>كلمة المرور الجديدة الخاصة بك هى $password <br>
                 </h3>
                <br><br>
                <p>مع اطيب التمنيات</p>
                <b>منصة التوصية بالجولات</b>";

                if ($mail->send()) {
                    // Redirect to login.php page with success message
                    $stmt = $con->prepare("UPDATE tourist SET password=? WHERE email=?");
                    $stmt->execute([$password, $email]);
                    $successMsg = 'الرجاء التأكد من البريد الإلكتروني لمعرفة الرقم السري الجديد';
                    header("Location:login.php?message=".urlencode($successMsg));
                    exit; // Exit to prevent further execution after redirection
                }
            }
        }
    } else {
        $emailError = 'الرجاء التحقق من البيانات';
    }
}
?>


<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600&family=El+Messiri:wght@400;500;600;700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
    <link rel="stylesheet" href="assets/css/daterangepicker.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/style111243.css">

    <title>توصية بالجولات</title>

    <style>
        .error {
            color: red;
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
            <a class="logo m-0 float-right" >
                <img src="assets/images/logo.PNG" alt="" style="height: 160px; padding-bottom: 50px">
                <span class="text-primary">
                </span>
            </a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left" style="margin-left:
            40px; margin-top: 10px; font-size: 40px">

                <li class="active"><a href="index.php">الرئيسية</a></li>
                <li class="active"><a href="signup.php">تسجيل جديد</a></li>

                <?php if (isset($tourist_id)): ?>
                    <li><a class="fa-solid fa-user" href="tourist/dashboard.php"></a></li>
                    <li><a href="logout.php">تسجيل الخروج</a></li>

                <?php elseif(isset($supervisor_id)):  ?>

                    <li><a class="fa-solid fa-user" href="supervisor/dashboard.php"></a></li>
                    <li><a href="logout.php">تسجيل الخروج</a></li>

                <?php elseif(isset($administrator_id)):  ?>

                    <li><a class="fa-solid fa-user" href="administrator/dashboard.php"></a></li>
                    <li><a href="logout.php">تسجيل الخروج</a></li>


                <?php endif;  ?>

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
    background: url('assets/images/m-k-R1gC_gJaJ14-unsplash.jpg') center;
    background-size: cover;
    position: relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">تسجيل الدخول</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<!--Start Login Section-->
<div class="untree_co-section">
    <div class="container h-100"
    ">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-lg-6 col-xl-6 ">
            <div class="card text-black shadow-lg" style="border-radius: 25px;">
                <div class="card-body p-md-5 ">
                    <div class="row justify-content-center ">
                        <div class="col-md-10 col-lg-10 col-xl-12 order-2 order-lg-1">

                            <h2 class="text-center mb-5">نسيت كلمة المرور</h2>

                            <form method="post" action="<?php
                            echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" class="mx-1 mx-md-4">

                                <div class="d-flex flex-row align-items-center mb-4">
                                    <div class="form-outline flex-fill mb-0 text-right">
                                        <label class="form-label" for="email">البريد الإلكتروني</label>
                                        <input type="email" id="email" name="email" value="<?php
                                        echo $email ?>"
                                               class="form-control"/>
                                        <span class="error"><?php
                                            echo $emailError ?></span>

                                    </div>
                                </div>


                                <div class="d-flex justify-content-center mx-4 mb-2 mb-lg-2">
                                    <button type="submit" name="register" class="btn btn-primary btn-lg">
                                        استعادة كلمة المرور
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


<div class="site-footer fixed-bottom">
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
<script src="assets/js/jquery.waypoints.min.js"></script>
<script src="assets/js/jquery.fancybox.min.js"></script>
<script src="assets/js/aos.js"></script>

<script src="assets/js/custom.js"></script>

</body>

</html>
