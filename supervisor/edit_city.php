<?php

// Start session to maintain user's session data
session_start();

if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}

// Include necessary files for validation and database connection
include "../validate.php";
include "../connection.php";

$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ?
    intval($_GET['city_id']) : 0;

$query = $con->prepare("SELECT * FROM city WHERE city_id=?");
$query->execute([$city_id]);
$city = $query->fetch();

// Initialize variables for form fields and error messages
$name = $day = $weather = '';
$nameError = $dayError = $weatherError = '';
$successMsg = '';

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate form inputs
    $name = validate($_POST['name']);
    $day = validate($_POST['day']);
    $weather = validate($_POST['weather']);

    // Validate name
    if (empty($name)) {
        $nameError = 'الرجاء أدخال اسم المدينة';
    } elseif (empty($day)) {
        $dayError = 'الرجاء أدخال تاريخ اليوم';
    } elseif (empty($weather)) {
        $weatherError = 'الرجاء أدخال الطقس';
    } else {
        // If all validations pass, update city in the database
        $stmt = $con->prepare("UPDATE city SET name=?, day=?, weather=? WHERE city_id=?");
        $stmt->execute([
            $name, $day,
            $weather,
            $city_id
        ]);
        $successMsg = 'تم تعديل المدينة بنجاح';
        header("Location:dashboard.php?success_message=".urlencode($successMsg));
        exit;
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
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/owl.carousel.min.css" rel="stylesheet">
    <link href="../assets/css/owl.theme.default.min.css" rel="stylesheet">
    <link href="../assets/css/jquery.fancybox.min.css" rel="stylesheet">
    <link href="../assets/fonts/icomoon/style.css" rel="stylesheet">
    <link href="../assets/fonts/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/daterangepicker.css" rel="stylesheet">
    <link href="../assets/css/aos.css" rel="stylesheet">
    <link href="../assets/css/style111243.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

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

<!--Start mobile toggle section-->
<div class="site-mobile-menu site-navbar-target">
    <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close">
            <span class="icofont-close js-menu-toggle"></span>
        </div>
    </div>
    <div class="site-mobile-menu-body"></div>
</div>
<!--End mobile toggle section-->

<!--Start Navbar Section-->
<nav class="site-nav">
    <div class="container">
        <div class="site-navigation">
            <a class="logo m-0 float-right" href="../index.php">توصية بالجولات <span class="text-primary"></span></a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left">
                <li class=""><a href="dashboard.php">الصفحة الرئيسية</a></li>
                <li class=""><a href="edit_profile.php">تعديل بيانات الملف الشخصي</a></li>
                <li class=""><a href="show_comments.php">عرض ريفيو</a></li>
                <li class=""><a href="show_reports.php">عرض البلاغات</a></li>
                <li class=""><a href="show_tourists.php">أدارة السياح</a></li>
                <li><a href="../logout.php">تسجيل الخروج</a></li>
            </ul>

            <a class="burger ml-auto float-left site-menu-toggle js-menu-toggle d-inline-block d-lg-none light"
               data-target="#main-navbar"
               data-toggle="collapse" href="../index.php">
                <span></span>
            </a>

        </div>
    </div>
</nav>
<!--End navbar Section-->

<!--Start Hero Section-->
<div class="hero hero-inner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">تعديل المدينة </h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<!--Start add city Section-->

<div class="justify-content-center d-flex text-center center-div bg-white p-5 rounded shadow">
    <form method="post" action="<?php
    echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

        <div class="mb-3">
            <label class="form-label" for="name">اسم المدينة</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="<?php
                   echo $city['name'] ?>"/>
            <span class="error"> <?php
                echo $nameError ?></span>
        </div>

        <div class="mb-3">
            <label class="form-label" for="day">تاريخ اليوم</label>
            <input type="date" class="form-control" id="day" name="day" value="<?php
            echo $city['day'] ?>"/>
            <span class="error"> <?php
                echo $dayError ?></span>

        </div>
        <div class="mb-3">
            <label class="form-label" for="weather">الطقس</label>
            <input type="text" class="form-control" id="weather" name="weather"
                   value="<?php
                   echo $city['weather'] ?>"/>
            <span class="error"> <?php
                echo $weatherError ?></span>
        </div>


        <button type="submit" class="btn py-2 px-4 btn-primary">
            إضافة
        </button>


    </form>


</div>
<!--End add city Section-->


<!--Start Footer Section-->
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


<div id="overlayer"></div>
<div class="loader">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<script src="../assets/js/jquery-3.4.1.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/owl.carousel.min.js"></script>
<script src="../assets/js/jquery.animateNumber.min.js"></script>
<script src="../assets/js/jquery.waypoints.min.js"></script>
<script src="../assets/js/jquery.fancybox.min.js"></script>
<script src="../assets/js/aos.js"></script>
<script src="../assets/js/moment.min.js"></script>
<script src="../assets/js/daterangepicker.js"></script>

<script src="../assets/js/typed.js"></script>
<script src="../assets/js/custom.js"></script>


</body>

</html>

