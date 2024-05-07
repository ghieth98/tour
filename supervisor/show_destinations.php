<?php

// Start the session to allow session variables usage
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../connection.php";

// Retrieve supervisor ID from session
$supervisor_id = $_SESSION['supervisor_id'];

$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ?
    intval($_GET['city_id']) : 0;

// Fetch destinations and their first associated image for the supervisor from the database
$query = $con->prepare(
    "
    SELECT d.*, di.image AS destination_image 
    FROM destination AS d 
    JOIN (
        SELECT destination_id, MIN(destination_image_id) AS first_image_id 
        FROM tours.destination_images 
        GROUP BY destination_id
    ) AS first_images ON d.destination_id = first_images.destination_id 
    JOIN tours.destination_images AS di ON first_images.first_image_id = di.destination_image_id
    WHERE supervisor_id=? 
"
);
$query->execute([$supervisor_id]);
$destinations = $query->fetchAll();

// Check if there's a success message passed via GET parameter, if not, set it to an empty string
$successMsg = $_GET['success_message'] ?? '';
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
    <link href="../assets/css/date rangepicker.css" rel="stylesheet">
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
                <img src="../assets/images/logo.PNG" alt="" style="height: 120px; width: 100px; font-weight: bold; color: white;">
                <span class="text-primary"></span>
            </a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left align-items-center" style="font-weight:
            bold; font-size: 24px;">
                <li class=""><a href="dashboard.php">الصفحة الرئيسية</a></li>
                <li class=""><a href="edit_profile.php">تعديل بيانات الملف الشخصي</a></li>
                <li class=""><a href="show_destinations.php">عرض الوجهات</a></li>
                <li class=""><a href="show_reports.php">عرض البلاغات</a></li>
<!--                <li class=""><a href="show_tourists.php">إدارة السياح</a></li>-->
                <li><a href="../logout.php">تسجيل الخروج</a></li>
            </ul>

            <a class="burger ml-auto float-right site-menu-toggle js-menu-toggle d-inline-block d-lg-none light"
               data-target="#main-navbar" data-toggle="collapse" href="../index.php">
                <span></span>
            </a>

        </div>
    </div>
</nav>
<!--End navbar Section-->

<!--Start Hero Section-->
<div class="hero hero-inner" style="background: url('../assets/images/edge.jpg') ;
 background-size: cover;
 position:relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">عرض الوجهات</h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<div class="px-5 py-5 ">

    <div class="d-flex align-items-center py-3">

        <div>
            <a class="px-4 btn py-2 btn-primary " href="add_destination.php">
                إضافة وجهة جديدة
            </a>
        </div>

    </div>


    <?php
    if ($successMsg): ?>
        <div id="successMessage" class="d-flex justify-content-center py-3">
            <div class="alert alert-success w-25 text-center" role="alert">
                <?php
                echo $successMsg ?>
            </div>
        </div>
    <?php
    endif; ?>

    <table class="table align-middle mb-0 " style="font-size: 16px;">
        <thead class="text-center">
        <tr>
            <th scope="col">الصورة</th>
            <th scope="col">الاسم</th>
            <th scope="col">وصف الوجهة</th>
            <th scope="col">أوقات العمل</th>
            <th scope="col">رقم الهاتف</th>
            <th scope="col">ألإجراءات</th>
        </tr>
        </thead>
        <tbody class="text-center" id="showSearch">
        <?php
        foreach ($destinations as $destination): ?>
            <tr>
                <td>
                    <img src="../uploads/<?php
                    echo $destination['destination_image'] ?>"
                         alt="destination image"
                         style="height: 80px">
                </td>
                <td>
                    <a href="show_comments.php?destination_id=<?php echo $destination['destination_id'] ?>">
                        <?php
                        echo $destination['name'] ?>
                    </a>

                </td>

                <td>
                    <p>
                        <?= substr($destination['description'], 0, 300).'...' ?>
                        <span class="more" style="display: none;">
                            <?= substr($destination['description'], 300) ?>
                        </span>
                        <a class="show-more link-light" onclick="showMore(this)">عرض المزيد </a>
                    </p>
                </td>
                <td> من: <?php
                    echo date("H:i A", strtotime($destination['start_date']));
                    ?> إلى:
                    <?php  echo date("H:i A", strtotime($destination['end_date'])); ?></td>

                <td><?php
                    echo $destination['phone_number'] ?></td>

                <td>
                    <div class="d-flex">
                        <a href="edit_destination.php?destination_id=<?php
                        echo $destination['destination_id'] ?>"
                           class="btn btn-primary px-4 py-2 ml-3"
                           type="button">تعديل</a>

                        <form action="delete_destination.php?destination_id=<?php
                        echo $destination['destination_id'] ?>"
                              method="post"
                              onsubmit="return confirm('هل تريد حذف هذه الوجهة ؟');"
                        >
                            <input type="hidden" name="city_id" value="<?php
                            echo $city_id ?>">
                            <button class="btn btn-primary px-4 py-2"
                                    type="submit">حذف
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
</div>


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

<script>
    function showMore(button) {
        // Get the parent <p> element
        var paragraph = button.parentNode;

        // Find the <span> containing the hidden text within the parent <p>
        var moreText = paragraph.querySelector(".more");

        // Toggle the display of the hidden portion
        if (moreText.style.display === "none" || moreText.style.display === "") {
            moreText.style.display = "inline"; // Or "block" depending on your layout
            button.innerHTML = "إظهار أقل"; // Change button text to "إظهار أقل" (Show Less)
        } else {
            moreText.style.display = "none";
            button.innerHTML = "عرض المزيد"; // Change button text back to "عرض المزيد" (Show More)
        }
    }</script>

</body>

</html>