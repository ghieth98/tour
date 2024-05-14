<?php

// Start session to maintain user's session data
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit;
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
$name = $description = $recommendedDays = '';
$nameError = $recommendedDaysError = $regionError = $descriptionError = $weatherError = $imageError = '';
$successMsg = '';

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate form inputs
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $recommendedDays = validate($_POST['recommended_days']);
    $weather = $_POST['weather'] ?? [];
    $region = $_POST['region'] ?? [];

    $oldImage = $_POST['oldImage'];

    $image = $_FILES['image'];
    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);

    // Validate name, description, weather, and image
    if (empty($name)) {
        $nameError = 'الرجاء أدخال اسم المدينة';
    } elseif (empty($description)) {
        $descriptionError = 'الرجاء أدخال وصف المدينة';
    } elseif (empty($weather)) {
        $weatherError = 'الرجاء أدخال الطقس';
    } elseif (empty($recommendedDays)) {
        $recommendedDaysError = 'الرجاء أدخال عدد الأيام المقترحة';
    } elseif (empty($region)) {
        $regionError = 'الرجاء أدخال المنطقة';
    } else {
        $imageName = $image['name'];
        if (!empty($imageName)) {
            $uploadPath = '../uploads/'.$imageName;
            move_uploaded_file($image['tmp_name'], $uploadPath); // Use $image['tmp_name']
        } else {
            $imageName = $oldImage;
        }
        $weather = json_encode($weather);
        $region = json_encode($region);

        // If all validations pass, update city in the database
        $stmt = $con->prepare("UPDATE city SET name=?, weather=?, city_description=?, city_image=?, recommend_days=?, region=? WHERE city_id=?");

        $stmt->execute([
            $name,
            $weather,
            $description,
            $imageName, // Assuming city_image comes here
            $recommendedDays,
            $region,
            $city_id
        ]);
        $successMsg = 'تم تعديل المدينة بنجاح';
        header("Location: dashboard.php?success_message=".urlencode($successMsg));
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
        <div class="site-navigation d-flex justify-content-between align-items-center">
            <a class="m-0 float-right" href="../index.php">
                <img src="../assets/images/logo.PNG" alt=""
                     style="height: 120px; width: 100px; font-weight: bold; color: white;">
                <span class="text-primary"></span>
            </a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left align-items-center">
                <li class=""><a href="dashboard.php">الصفحة الرئيسية</a></li>
                <li class=""><a href="edit_profile.php">الملف الشخصي</a></li>
                <li class=""><a href="show_destinations.php">عرض الوجهات</a></li>
                <li class=""><a href="show_reports.php">عرض البلاغات</a></li>
                <!--                <li class=""><a href="show_tourists.php">إدارة السياح</a></li>-->
                <li><a href="../logout.php" onclick="return confirm('هل تريد تسجيل الخروج؟')">تسجيل الخروج</a></li>
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
<div class="hero hero-inner" style="background: url('../assets/images/edge.jpg'); background-size: cover; position:
relative;">
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
<div class="px-5 py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-5 bg-white rounded shadow p-5 text-center">

        <form method="post" style="font-size: 17px" action="<?php
        echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label" for="name">اسم المدينة</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?php
                       echo $city['name'] ?>"/>
                <span class="error"> <?php
                    echo $nameError ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="recommended_days">عدد الأيام المقترحة</label>
                <input type="number" class="form-control" id="recommended_days" name="recommended_days"
                       value="<?php
                       echo $city['recommend_days'] ?>"/>
                <span class="error"> <?php
                    echo $recommendedDaysError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label" for="description">وصف المدينة</label>
                <textarea name="description" id="description" class="form-control"><?php
                    echo $city['city_description'] ?></textarea>
                <span class="error"> <?php
                    echo $descriptionError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label" for="weather">الفصل:</label><br>
                <input type="checkbox" id="weatherSummer" name="weather[]" value="summer">
                <label class="form-check-label ml-1" for="weatherSummer">الصيف</label>
                <input type="checkbox" id="weatherWinter" name="weather[]" value="winter">
                <label class="form-check-label ml-1" for="weatherWinter">الشتاء</label>
                <input type="checkbox" id="weatherSpring" name="weather[]" value="spring">
                <label class="form-check-label ml-1" for="weatherSpring">الربيع</label>
                <input type="checkbox" id="weatherAutumn" name="weather[]" value="autumn">
                <label class="form-check-label ml-1" for="weatherAutumn">الخريف</label>
                <span class="error"> <?php
                    echo $weatherError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label" for="weather">المنطقة:</label><br>
                <input type="checkbox" id="centerRegion" name="region[]" value="center">
                <label class="form-check-label ml-1" for="centerRegion">المنطقة الوسطي</label>
                <input type="checkbox" id="eastRegion" name="region[]" value="east">
                <label class="form-check-label ml-1" for="eastRegion">المنطقة الشرقية</label>
                <input type="checkbox" id="westRegion" name="region[]" value="west">
                <label class="form-check-label ml-1" for="westRegion">المنطقة الغربية</label>
                <input type="checkbox" id="southRegion" name="region[]" value="south">
                <label class="form-check-label ml-1" for="southRegion">المنطقة الجنوبية</label>
                <input type="checkbox" id="northRegion" name="region[]" value="north">
                <label class="form-check-label ml-1" for="northRegion">المنطقة الشمالية</label>
                <span class="error"> <?php
                    echo $regionError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label" for="image">اختيار صورة المدينة</label>
                <div class="custom-file">
                    <input type="hidden" id="oldImage" name="oldImage" value="<?php
                    echo $city['city_image'] ?>">
                    <input type="file" class="custom-file-input" id="image" name="image">
                    <label class="custom-file-label" for="image">اختيار صورة المدينة</label>
                </div>
                <span class="error"><?php
                    echo $imageError ?></span>
            </div>

            <button type="submit" class="btn py-2 px-4 btn-primary" style="font-size: 14px; font-weight: bold">
                إضافة
            </button>


        </form>


    </div>

</div>

<!--End add city Section-->


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

