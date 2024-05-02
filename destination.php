<?php

// Start the session to allow session variables usage
session_start();

if (isset($_SESSION['tourist_id'])) {
    $tourist_id = $_SESSION['tourist_id'];
}

if (isset($_SESSION['supervisor_id'])) {
    $supervisor_id = $_SESSION['supervisor_id'];
}

if (isset($_SESSION['administrator_id'])) {
    $administrator_id = $_SESSION['administrator_id'];
}

// Include necessary files for validation and database connection
include "connection.php"; // Assuming this file contains the database connection logic



$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ?
    intval($_GET['city_id']) : 0;

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
   GROUP BY d.destination_id

"
);

$query->execute();
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
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600&family=El+Messiri:wght@400;500;600;700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&display=swap"
          rel="stylesheet">

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet">
    <link href="assets/css/jquery.fancybox.min.css" rel="stylesheet">
    <link href="fonts/icomoon/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="fonts/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="assets/css/daterangepicker.css" rel="stylesheet">
    <link href="assets/css/aos.css" rel="stylesheet">
    <link href="assets/css/style111243.css" rel="stylesheet">

    <title>توصية بالجولات</title>
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
            <a class="logo m-0 float-right" href="index.php">توصية بالجولات <span class="text-primary"></span></a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left">
                <li class="active"><a href="index.php">الرئيسية</a></li>
                <li class="active"><a href="show_cities.php">المدن</a></li>
                <li><a href="destination.php">الوجهات</a></li>

                <?php if (isset($tourist_id)): ?>
                    <li><a href="logout.php">تسجيل الخروج</a></li>
                    <li><a class="fa-solid fa-user" href="tourist/dashboard.php"></a></li>
                <?php elseif(isset($supervisor_id)):  ?>
                    <li><a href="logout.php">تسجيل الخروج</a></li>
                    <li><a class="fa-solid fa-user" href="supervisor/dashboard.php"></a></li>
                <?php elseif(isset($administrator_id)):  ?>
                    <li><a href="logout.php">تسجيل الخروج</a></li>
                    <li><a class="fa-solid fa-user" href="administrator/dashboard.php"></a></li>
                <?php else:  ?>
                    <li><a href="signup.php">تسجيل جديد</a></li>
                    <li><a href="login.php">تسجيل الدخول</a></li>
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
    background: url('assets/images/mohammed-alorabi-_ABfNIGGsZk-unsplash.jpg')  center ;
    background-size: cover;
    position: relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">الوجهات</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<!--Start Destination Section-->
<div class="untree_co-section">




    <div class="row row-cols-1 row-cols-md-3 g-4 p-5 text-right" data-aos="fade-up" data-aos-delay="500">

        <div class="col-lg-12 text-center">
            <h2 class="section-title text-center mb-3">الوجهات</h2>
            <?php if ($successMsg):  ?>
            <div class="d-flex justify-content-center">
                <div class="alert alert-success w-25 text-center" role="alert">
                    <?php echo $successMsg ?>
                </div>
            </div>
            <?php endif;  ?>
        </div>

        <?php foreach ($destinations as $destination): ?>
            <!--Start Card-->
            <div class="col-lg-3 rounded col-md-6 mb-3 ">
                <div class="card h-100 shadow">
                    <a href="destination_info.php?destination_id=<?php echo $destination['destination_id'] ?>">
                        <img alt="صورة الوجهة" class="card-img-top" style="height: 300px"
                             src="uploads/<?php echo $destination['destination_image'] ?>">
                    </a>
                    <div class="card-body">
                        <a href="destination_info.php?destination_id=<?php echo $destination['destination_id'] ?>">
                            <h5 class="card-title"><?php echo $destination['name'] ?></h5>
                        </a>
                        <p class="card-text"><?php echo substr($destination['description'], 0, 300)  ?>...</p>
                        <p class="text-muted"> أوقات العمل :  من: <?php
                            echo date("H:i A", strtotime($destination['start_date']));
                            ?> إلى:
                            <?php  echo date("H:i A", strtotime($destination['end_date'])); ?> </p>                        <p class="text-muted"> اسم المدينة : <?php echo $destination['city_name'] ?> </p>
                        <p class="text-muted">رقم الهاتف: <?php echo $destination['phone_number'] ?></p>


                                <form method="post" action="add_favorite.php?destination_id=<?php
                                echo $destination['destination_id'] ?>">

                                    <input type="hidden" name="city_id" value="<?php
                                    echo $destination['city_id'] ?>">
                                    <?php $favorite = $destination['favorite_count'] > 0  ?>
                                    <?php if (isset($tourist_id)) : ?>
                                    <?php if ($favorite):  ?>
                                    <button style="background: none; border: none; padding: 0; cursor: pointer;">
                                    <i class="fa-solid fa-xl fa-heart pl-3 favorite"
                                       type="submit"></i>
                                    </button>
                                    <?php else:  ?>
                                        <button style="background: none; border: none; padding: 0; cursor: pointer;">
                                            <i class="fa-regular fa-xl fa-heart pl-3 " style="cursor: pointer;"
                                               type="submit"></i>
                                        </button>
                                    <?php endif;  ?>
                                        <?php endif; ?>
                                <i class="fa-solid fa-star " style="color: #f3f31c"></i> <?php echo $destination['stars'] ?>
                                </form>



                    </div>
                </div>
            </div>
            <!--End Card-->
        <?php endforeach; ?>
    </div>
</div>
<!--End Destination Section-->


<!--Start Footer Section-->
<div class="site-footer">
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

<script src="assets/js/jquery-3.4.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.waypoints.min.js"></script>
<script src="assets/js/jquery.fancybox.min.js"></script>
<script src="assets/js/aos.js"></script>

<script src="assets/js/custom.js"></script>

</body>

</html>
