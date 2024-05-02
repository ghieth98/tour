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
$name  = '';
$nameError  = '';

// Get admin email from session
$admin_email = $_SESSION['email'];
$successMsg = $_GET['success_message'] ?? '';

$query = $con->prepare("SELECT * FROM administrator WHERE email=?");
$query->execute([$admin_email]);

$admin = $query->fetch();


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the name and the password and confirm password
    $name = validate($_POST['name']);


    // Check if password is empty
    if (empty($name)) {
        $nameError = 'الرجاء إدخال الاسم';
    } elseif (strlen($name) < 3) {
        $nameError = 'الاسم لا يمكن ان يقل عن 3 حروف';
    } else {
        // Update admin data in the database
        $stmt = $con->prepare("UPDATE administrator SET name=? WHERE email=?");
        $stmt->execute([$name, $admin_email]);

        // Set success message
        $successMsg = 'تم تعديل بيانات المدير بنجاح';

        // Redirect to  page with success message
        header("Location:edit_profile.php?success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection
    }
}

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
                <li class=""><a href="add_api_url.php">اضافة رابط الربط</a></li>
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

<div class="justify-content-center d-flex text-center center-div bg-white p-5  rounded shadow" style="margin-top:
120px">
    <form method="post"
          action="<?php
          echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">

        <div class="mb-3">
            <label for="name" class="form-label">الاسم</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php
            echo $admin['name'] ?>"/>
            <span class="error"> <?php
                echo $nameError ?></span>
        </div>


        <button type="submit" class="btn py-2 px-4 btn-primary" name="editProfile">
            تعديل البيانات الشخصية
        </button>

    </form>
</div>
<!--End Our Supervisor Section-->


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

<script>
    $(document).ready(function () {
        // Store the original HTML content of the table body
        const originalTableContent = $('#showSearch').html();

        $('#search').on('keyup', function () {
            let search = $(this).val().trim(); // Trim the search string to handle empty space
            if (search !== '') {
                $.ajax({
                    method: 'POST',
                    url: 'search-supervisor.php',
                    data: {name: search},
                    success: function (response) {
                        $('#showSearch').html(response);
                    }
                });
            } else {
                // If search is empty, display the original table content
                $('#showSearch').html(originalTableContent);
            }
        });

    });
</script>

</body>

</html>
