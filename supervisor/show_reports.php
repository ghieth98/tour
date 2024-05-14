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

// Fetch all reports from the database along with associated information
$query = $con->prepare("
    SELECT
        report.*,
        t.name AS tourist_name,
        r.body AS reply_body,
        rv.body AS review_body, 
        t.*
    FROM
        report
    JOIN
        tourist t
    ON
        t.tourist_id = report.tourist_id
    LEFT JOIN
        reply r
    ON
        report.reply_id = r.reply_id
    LEFT JOIN
        review rv
    ON
        report.review_id = rv.review_id
");
$query->execute();
$reports = $query->fetchAll();

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
                <li class=""><a href="edit_profile.php">الملف الشخصي</a></li>
                <li class=""><a href="show_destinations.php">عرض الوجهات</a></li>
                <li class=""><a href="show_reports.php">عرض البلاغات</a></li>
<!--                <li class=""><a href="show_tourists.php">إدارة السياح</a></li>-->
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
<div class="hero hero-inner" style="background: url('../assets/images/edge.jpg') ;
 background-size: cover;
 position:relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">عرض البلاغات</h1>

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


    <table class="table align-middle mb-0 " style="font-size: 16px;">
        <thead class="text-center">
        <tr>
            <th scope="col">الاسم السائح</th>
            <th scope="col">البريد الإلكتروني</th>
            <th scope="col">نص التعليق/الرد</th>
            <th scope="col">تاريخ التعليق</th>
            <th scope="col"> ألإجراءات</th>
        </tr>
        </thead>
        <tbody class="text-center" id="showSearch">
        <?php foreach ($reports as $report): ?>

            <tr>

                <td>
                    <?php echo $report['tourist_name'] ?>
                </td>
                <td>
                    <?php echo $report['email'] ?>
                </td>

                <td>
                    <?php echo $report['review_body'] ?? $report['reply_body']; ?>
                </td>

                <td> <?php echo $report['date'] ?> </td>
                <td>
                    <div style="display: inline;">

                        <form action="delete_report.php?report_id=<?php echo $report['report_id'] ?>" method="post" style="display: inline;">
                            <button class="btn btn-primary px-4 py-2 " type="submit">
                                تجاهل
                            </button>
                        </form>

                        <?php if ($report['review_id']) : ?>

                        <form action="delete_comment.php?review_id=<?php echo $report['review_id'] ?>"
                              method="post"  onsubmit="return confirm('هل تريد حذف ' +
                               'هذا التعليق ؟');"
                              style="display: inline;">
                            <button class="btn btn-primary px-4 py-2" type="submit">
                                حذف
                            </button>
                        </form>

                        <?php elseif($report['reply_id']): ?>

                            <form action="delete_reply.php?reply_id=<?php echo $report['reply_id'] ?>"
                                  method="post"  onsubmit="return confirm('هل تريد حذف ' +
                               'هذا الرد ؟');"
                                  style="display: inline;">
                                <button class="btn btn-primary px-4 py-2" type="submit">
                                    حذف
                                </button>
                            </form>
                        <?php endif;  ?>

                        <form action="ban_tourist.php?tourist_id=<?php echo $report['tourist_id'] ?>"
                              method="post" onsubmit=" <?php if ($report['ban'] === 'unbanned')  : ?>
                            return confirm('هل تريد حظر هذا السائح ؟');
                        <?php elseif ($report['ban'] === 'banned' || $report['ban'] === 'temporary'): ?>
                            return confirm('هل تريد رفع الحظر عن هذا السائح؟')
                        <?php endif; ?>"
                              style="display: inline;">
                            <?php if ($report['ban'] === 'unbanned') : ?>
<!--                                <select name="ban" id="ban">-->
<!--                                    <option name="banned" value="banned">-->
<!--                                        حظر-->
<!--                                    </option>-->
<!--                                    <option name="temporary" value="temporary">-->
<!--                                        حظر مؤقت-->
<!--                                    </option>-->
<!--                                </select>-->
                                <button type="submit" name="ban" value="banned" class="btn btn-primary px-4 py-2">
                                    حظر السائح
                                </button>
                                <button type="submit" name="ban" value="temporary" class="btn btn-primary px-4 py-2">
                                    حظر مؤقت للسائح
                                </button>
                            <?php elseif ($report['ban'] === 'banned' || $report['ban'] === 'temporary'): ?>
                                <button name="ban" class="btn btn-primary px-4 py-2" value="unbanned" type="submit">
                                    رفع الحظر
                                </button>
                            <?php endif; ?>
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

