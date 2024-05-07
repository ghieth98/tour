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

$touristQuery = $con->query("SELECT * FROM tourist");
$touristCount = $touristQuery->rowCount();

$destinationQuery = $con->query("SELECT * FROM destination");
$destinationCount = $destinationQuery->rowCount();


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
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600&family=El+Messiri:wght@400;500;600;700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&display=swap"
        rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet">
    <link href="assets/css/jquery.fancybox.min.css" rel="stylesheet">
    <link href="assets/fonts/icomoon/style.css" rel="stylesheet">
    <link href="assets/fonts/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/daterangepicker.css" rel="stylesheet">
    <link href="assets/css/aos.css" rel="stylesheet">
    <link href="assets/css/style111243.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <title>توصية بالجولات</title>
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
            <a class="logo m-0 float-right" >
                <img src="assets/images/logo.PNG" alt="" style="height: 160px; padding-bottom: 50px">
                <span class="text-primary">
                </span>
            </a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left" style="margin-left:
            40px; font-size: 40px">



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

<!--Start Hero Section-->
<div class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="intro-wrap">
                    <h1 class="mb-5 text-right"><span class="d-block">لنستمتع بجولتنا في  <span
                                class="typed-words"></span></span></h1>
                    <!--                                Start Search Section-->
                    <div class="row">
                        <div class="col-12">
                            <form class="form">
                                <div class="row mb-2">
                                    <div class="col-sm-12 col-md-6  mb-lg-0 col-lg-12">
                                        <div class="d-flex justify-content-center align-content-center" >
                                            <a class="btn btn-primary btn-lg ml-4" style="font-size: 16px"
                                               href="signup.php">تسجيل جديد</a>
                                            <a class="btn btn-primary btn-lg" style="font-size: 16px" href="login.php">تسجيل الدخول</a>
                                        </div>
                                    </div>
                                    <div class="mx-auto col-lg-10  mt-1 justify-content-center shadow rounded"
                                         id="showSearch">
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="slides">
                    <img alt="Image" class="img-fluid active" src="assets/images/edge.jpg">
                    <img alt="Image" class="img-fluid" src="assets/images/village.jpg">
                    <img alt="Image" class="img-fluid" src="assets/images/village2.jpg">
                    <img alt="Image" class="img-fluid" src="assets/images/village3.jpg">
                    <img alt="Image" class="img-fluid" src="assets/images/old%20jada.jpg">
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<!--Start Our Services Section-->
<div class="untree_co-section">
    <div class="container">
        <div class="row mb-5 justify-content-center">
            <div class="col-lg-6 text-center">
                <h2 class="section-title text-center mb-3">خدماتنا</h2>
                <p>تتميز المملكة العربية السُّعُودية بتنوعها الطبيعي والثقافي. تعد مدينة العلا بمعمارها الفريد والتاريخ
                    العريق واحدة من المعالم البارزة. في مكة المكرمة، يأخذ زوار المملكة رحلة إلى المدينة القديمة في
                    الدرعية لاستكشاف الأسواق التقليدية والبنية العمرانية التاريخية. يجمع التوازن بين التراث والحداثة في
                    المملكة ليقدم للسياح تجرِبة فريدة في قلب العالم العربي.</p>
            </div>
        </div>

        <div class="row align-items-stretch text-right">
            <div class="col-lg-4 order-lg-1">
                <div class="h-100">
                    <div class="frame h-100">
                        <div class="feature-img-bg h-100"
                             style="background-image: url('assets/images/village3.jpg');"></div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-6 col-lg-4 feature-1-wrap d-md-flex flex-md-column order-lg-1">

                <div class="feature-1 d-md-flex">
                    <div class="align-self-center">
                        <span class="flaticon-house display-4 text-primary"></span>
                        <h3>تعزيز تجرِبة سياحية فاخرة</h3>
                        <p class="mb-0">السعي لتحسين الجودة العامة لتجربة السياح في المملكة.
                        </p>
                    </div>
                </div>

                <div class="feature-1 ">
                    <div class="align-self-center">
                        <i class="fa-solid fa-bullseye display-4 text-primary"></i>
                        <h3>توصيات مُخَصَّصَة</h3>
                        <p class="mb-0">نظام خبير يقدم توصيات تتناسب مع معايير المستخدم الفريدة.
                        </p>
                    </div>
                </div>

            </div>

            <div class="col-6 col-sm-6 col-lg-4 feature-1-wrap d-md-flex flex-md-column order-lg-3">

                <div class="feature-1 d-md-flex">
                    <div class="align-self-center">
                        <i class="fa-solid fa-cubes-stacked display-4 text-primary"></i>
                        <h3>تحديثات دورية وتوسع مستمر</h3>
                        <p class="mb-0">الالتزام بتحديث المعلومات بشكل دوري ومتابعة آخر التطورات في الوجهات السياحية.
                        </p>
                    </div>
                </div>

                <div class="feature-1 d-md-flex">
                    <div class="align-self-center">
                        <i class="fa-regular fa-compass display-4 text-primary"></i>
                        <h3>رحلة استكشاف شاملة</h3>
                        <p class="mb-0">نعمل على سد الفجوة في الوعي السياحي من خلال تقديم رؤى مفصلة حول الجوانب المختلفة
                            في جميع أنحاء البلاد.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<!--End Our Services Section-->

<!--Start Counter Section-->
<div class="untree_co-section count-numbers py-5 ">
    <div class="container">
        <div class="row">
            <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                <div class="counter-wrap">
                    <div class="counter">
                        <span  data-number="<?php
                        echo $touristCount ?>">0</span>
                    </div>
                    <span class="caption">عدد العملاء</span>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                <div class="counter-wrap">
                    <div class="counter">
                        <span class="" data-number="<?php
                        echo $destinationCount ?>">0</span>
                    </div>
                    <span class="caption">عدد الوجهات</span>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                <div class="counter-wrap">
                    <div class="counter">
                        <span class="" data-number="98">0</span>
                        <span>%</span>
                    </div>
                    <span class="caption"> فعالية التوصيات</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Counter Section-->

<!--Start About us Section-->
<div class="untree_co-section testimonial-section mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <h2 class="section-title text-center mb-5">من نحن</h2>

                <div class="row mb-5 justify-content-center">
                    <div class="col-lg-12 text-center" style="font-size: 19px">
                        <p>
                            مرحبًا بك في مبادرتنا السياحية في المملكة العربية السُّعُودية. نهدف إلى تعزيز تجرِبة السفر
                            من
                            خلال منصة ويب تقدم معلومات شاملة حول وجهات السياحة. بفضل رؤية 2030 والتفاني في تطوير
                            السياحة، أصبحت المملكة واحدة من الوجهات المميزة. تحديات مثل نقص الوعي بالوجهات وتنوع
                            احتياجات الفئات العمرية تجد حلاً في نظامنا الخبير عبر الويب، الذي يُقدم جولات مخصصة. انضم
                            إلينا في هذه الرحلة لاستكشاف المملكة بكل روعة وسهولة.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!--End About Us Section-->

<!--Start Popular Destination Section-->
<div class="untree_co-section">
    <div class="container">
        <div class="row text-center justify-content-center mb-5">
            <div class="col-lg-7"><h2 class="section-title text-center">الوجهات المشهورة</h2></div>
        </div>

        <div class="owl-carousel owl-3-slider">

            <?php
            foreach ($destinations as $destination): ?>
                <div class="item">
                    <a class="media-thumb" data-fancybox="gallery" href="assets/images/edge.jpg">
                        <div class="media-text">
                            <h3><?php
                                echo $destination['name'] ?></h3>
                            <span class="location"><?php
                                echo $destination['city_name'] ?></span>
                        </div>
                        <img alt="Image" class="img-fluid" src="uploads/<?php
                        echo $destination['destination_image'] ?>"
                             style="height:500px">
                    </a>
                </div>
            <?php
            endforeach; ?>


        </div>

    </div>
</div>
<!--End Popular Destination Section-->

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
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/jquery.animateNumber.min.js"></script>
<script src="assets/js/jquery.waypoints.min.js"></script>
<script src="assets/js/jquery.fancybox.min.js"></script>
<script src="assets/js/aos.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/daterangepicker.js"></script>

<script src="assets/js/typed.js"></script>
<script>
    $(function () {
        let slides = $('.slides'),
            images = slides.find('img')

        images.each(function (i) {
            $(this).attr('data-id', i + 1)
        })

        const typed = new Typed('.typed-words', {
            strings: ['الرياض .', ' مكة المكرمة.', 'المدينة المنورة.', ' الباحة.', ' الدمام.'],
            typeSpeed: 80,
            backSpeed: 80,
            backDelay: 4000,
            startDelay: 1000,
            loop: true,
            showCursor: true,
            preStringTyped: (arrayPos, self) => {
                arrayPos++
                console.log(arrayPos)
                $('.slides img').removeClass('active')
                $('.slides img[data-id="' + arrayPos + '"]').addClass('active')
            }

        })
    })


    $(document).ready(function () {
        // Store the original HTML content of the table body
        const originalTableContent = $("#showSearch").html();

        $('#search').on('keyup', function () {
            let search = $(this).val().trim(); // Trim the search string to handle empty space
            if (search !== '') {
                $.ajax({
                    method: 'POST',
                    url: 'tourist/includes/search_destination.php',
                    data: {name: search},
                    success: function (response) {
                        $("#showSearch").html(response);
                    }
                });
            } else {
                // If search is empty, display the original table content
                $("#showSearch").html(originalTableContent);
            }
        });

    });
</script>

<script src="assets/js/custom.js"></script>

</body>

</html>
