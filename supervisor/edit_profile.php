<?php

// Start the session
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include validation functions and database connection
include "../validate.php";
include "../connection.php";

// Initialize variables
$name = '';
$nameError = '';
$successMsg = $_GET['success_message'] ?? '';

// Get supervisor email from session
$supervisorEmail = $_SESSION['email'];

// Query the supervisor data from the database based on email
$query = $con->prepare("SELECT * FROM supervisor WHERE email=?");
$query->execute([$supervisorEmail]);

// Fetch the supervisor data
$supervisor = $query->fetch();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and encode the password
    $name = validate($_POST['name']);

    // Check if name is empty
    if (empty($name)) {
        $nameError = 'برجاء أدخال اسم المستخدم';
    } else {
        // Update password and name in the database
        $stmt = $con->prepare("UPDATE supervisor SET name=? WHERE email=?");
        $stmt->execute([$name, $supervisorEmail]);

        // Set success message and redirect
        $successMsg = 'تم تعديل بيانات الملف الشخصي بنجاح';
        header("Location: edit_profile.php?success_message=".urlencode($successMsg));
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
                <li class=""><a href="edit_password.php">تعديل كلمة المرور</a></li>
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
                    <h1 class="mb-0">تعديل بيانات الملف الشخصي</h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->


<!--Start Our ٍSupervisor Section-->

<?php if ($successMsg):  ?>
    <div id="successMessage" class="d-flex justify-content-center py-3">
        <div class="alert alert-success w-25 text-center"  role="alert">
            <?php echo $successMsg ?>
        </div>
    </div>
<?php endif;  ?>
<div class="justify-content-center d-flex text-center center-div bg-white p-5 rounded shadow">
    <form method="post"
          action="<?php
          echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">

        <div class="mb-3">
            <label for="name" class="form-label">الاسم</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php
            echo $supervisor['name'] ?>"/>
            <span class="error"> <?php
                echo $nameError ?></span>
        </div>


        <button type="submit" class="btn py-2 px-4 btn-primary" name="editProfile">
            تعديل البيانات الشخصية
        </button>

    </form>
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