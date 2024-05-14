<?php

// Start the session
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include validation functions and database connection
include "../validate.php";
include "../connection.php";


$successMsg = $_GET['success_message'] ?? '';


$tourist_id = $_SESSION['tourist_id'];

$query = $con->prepare(
    "
   SELECT d.*, r.stars, di.image AS destination_image, C.name AS city_name,       
    COUNT(DISTINCT f.favorite_id) AS favorite_count 
    FROM destination AS d 
    JOIN (
        SELECT destination_id, MIN(destination_image_id) AS first_image_id 
        FROM tours.destination_images 
        GROUP BY destination_id
    ) AS first_images ON d.destination_id = first_images.destination_id 
    JOIN tours.destination_images AS di ON first_images.first_image_id = di.destination_image_id
    LEFT JOIN tours.favorite f on d.destination_id = f.destination_id
    LEFT JOIN tours.rate r on d.destination_id = r.destination_id
    LEFT JOIN tours.city c on c.city_id = d.city_id
    WHERE f.tourist_id=?
   GROUP BY d.destination_id

"
);


$query->execute([$tourist_id]);

$favorites = $query->fetchAll();

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
                <img src="../assets/images/logo.PNG" alt=""
                     style="height: 120px; width: 100px; font-weight: bold; color: white;">
                <span class="text-primary"></span>
            </a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left align-items-center" style="font-weight:
            bold; font-size: 24px;">
                <li class=""><a href="dashboard.php">الصفحة الرئيسية</a></li>
                <li class=""><a href="../destination.php">الوجهات</a></li>
                <li class=""><a href="edit_profile.php">الملف الشخصي</a></li>
                <li class=""><a href="test.php">الاختبار</a></li>
                <li><a href="../logout.php" onclick="return confirm('هل تريد تسجيل الخروج؟')">تسجيل الخروج</a></li>
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
<div class="hero hero-inner"
     style="background: url('../assets/images/m-k-R1gC_gJaJ14-unsplash.jpg')  ;
 background-size: cover;
 position:relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">لوحة تحكم السائح</h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<div class="untree_co-section">


    <div class="row row-cols-1 row-cols-md-3 g-4 p-5 text-right" data-aos="fade-up" data-aos-delay="500">

        <div class="col-lg-12 text-center">
            <?php
            if ($successMsg): ?>
                <div class="d-flex justify-content-center">
                    <div class="alert alert-success w-25 text-center" role="alert">
                        <?php
                        echo $successMsg ?>
                    </div>
                </div>
            <?php
            endif; ?>
        </div>

        <?php
        foreach ($favorites as $favorite): ?>
            <!--Start Card-->
            <div class="col-lg-3 rounded col-md-6 mb-3 ">
                <div class="card h-100 shadow">
                    <a href="../destination_info.php?destination_id=<?php
                    echo $favorite['destination_id'] ?>">
                        <img alt="صورة الوجهة" class="card-img-top" style="height: 300px"
                             src="../uploads/<?php
                             echo $favorite['destination_image'] ?>">
                    </a>
                    <div class="card-body">
                        <a href="../destination_info.php?destination_id=<?php
                        echo $favorite['destination_id'] ?>">
                            <h5 class="card-title"><?php
                                echo $favorite['name'] ?></h5>
                        </a>
                        <p class="card-text"><?php
                            echo substr($favorite['description'], 0, 300) ?>...</p>
                        <p class="text-muted"> أوقات العمل : من: <?php
                            echo date("H:i A", strtotime($favorite['start_date']));
                            ?> إلى:
                            <?php
                            echo date("H:i A", strtotime($favorite['end_date'])); ?> </p>
                        <p class="text-muted"> اسم المدينة : <?php
                            echo $favorite['city_name'] ?> </p>
                        <p class="text-muted">رقم الهاتف: <?php
                            echo $favorite['phone_number'] ?></p>


                        <form method="post" action="../add_favorite.php?destination_id=<?php
                        echo $favorite['destination_id'] ?>">

                            <input type="hidden" name="city_id" value="<?php
                            echo $favorite['city_id'] ?>">
                            <?php
                            $favorite_count = $favorite['favorite_count'] > 0 ?>
                            <?php
                            if (isset($tourist_id)) : ?>
                                <?php
                                if ($favorite_count): ?>
                                    <button style="background: none; border: none; padding: 0; cursor: pointer;">
                                        <i class="fa-solid fa-xl fa-heart pl-3 favorite"
                                           type="submit"></i>
                                    </button>
                                <?php
                                else: ?>
                                    <button style="background: none; border: none; padding: 0; cursor: pointer;">
                                        <i class="fa-regular fa-xl fa-heart pl-3 " style="cursor: pointer;"
                                           type="submit"></i>
                                    </button>
                                <?php
                                endif; ?>
                            <?php
                            endif; ?>
                            <i class="fa-solid fa-star " style="color: #f3f31c"></i> <?php
                            echo $favorite['stars'] ?>
                        </form>


                    </div>
                </div>
            </div>
            <!--End Card-->
        <?php
        endforeach; ?>
    </div>
</div>


<!--Start Footer Section-->
<div class="site-footer ">
    <div class="inner first ">
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


