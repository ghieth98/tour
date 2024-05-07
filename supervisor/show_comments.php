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

$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ?
    intval($_GET['destination_id']) : 0;

// Fetch all reviews from the database, along with associated destination and tourist names
$query = $con->prepare("
    SELECT 
        review.*, 
        d.name AS destination_name, 
        t.name AS tourist_name 
    FROM 
        review 
    JOIN 
        tours.destination d 
    ON 
        d.destination_id = review.destination_id 
    JOIN 
        tours.tourist t 
    ON 
        t.tourist_id = review.tourist_id
    WHERE review.destination_id = ?
    
    
");
$query->execute([$destination_id]);
$comments = $query->fetchAll();

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
                <img src="../assets/images/logo.PNG" alt=""
                     style="height: 120px; width: 100px; font-weight: bold; color: white;">
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
                    <h1 class="mb-0">عرض التعليقات</h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<div class="px-5 py-5 ">

    <?php if ($successMsg):  ?>
        <div id="successMessage" class="d-flex justify-content-center py-3">
            <div class="alert alert-success w-25 text-center"  role="alert">
                <?php echo $successMsg ?>
            </div>
        </div>
    <?php endif;  ?>


    <table class="table align-middle mb-0 " style="font-size: 17px;">
        <thead class="text-center">
        <tr>
            <th scope="col">اسم السائح</th>
            <th scope="col">اسم الوجهة</th>
            <th scope="col">نص التعليق</th>
            <th scope="col">تاريخ التعليق</th>
        </tr>
        </thead>
        <tbody class="text-center" id="showSearch">
        <?php foreach ($comments as $comment): ?>

            <tr>

                <td> <?php echo $comment['tourist_name'] ?> </td>

                <td> <?php echo $comment['destination_name'] ?> </td>
                <td> <?php echo $comment['body'] ?> </td>
                <td> <?php echo $comment['date'] ?> </td>
                <td>
                    <div style="display: inline;">

                        <form action="delete_comment.php?review_id=<?php echo $comment['review_id'] ?>"
                              method="post"  onsubmit="return confirm('هل تريد حذف ' +
                               'هذا التعليق ؟');"
                              style="display: inline;">
                            <input type="hidden" name="destination_id" value="<?php echo $comment['destination_id'] ?>">
                            <button class="btn btn-primary px-4 py-2" type="submit">
                                حذف
                            </button>
                        </form>

                    </div>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>
    </table>
</div>




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

