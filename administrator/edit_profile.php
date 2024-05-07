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
$name = $password = $confirmPassword = '';
$nameError = $passwordError = $confirmPasswordError = '';

// Get admin email from session
$admin_email = $_SESSION['email'];
$successMsg = $_GET['success_message'] ?? '';

$query = $con->prepare("SELECT * FROM administrator WHERE email=?");
$query->execute([$admin_email]);

$admin = $query->fetch();


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and encode the password
    $name = validate($_POST['name']);
    $password = validate($_POST['password']);
    $oldPassword = validate($_POST['oldPassword']);
    $confirmPassword = validate($_POST['confirmPassword']);
    // Check if name is empty
    // Check if name is empty
    if (empty($name)) {
        $nameError = 'برجاء أدخال اسم المستخدم';
    } elseif (strlen($name) < 3) {
        $nameError = 'الاسم لا يمكن ان يقل عن 3 حروف';
    }

    // Check if password is being updated
    if (!empty($password)) {
        // Check if old password matches
        if ($password === $oldPassword) {
            $passwordError = 'كلمة المرور المدخلة موجوده مسبقا';
        } elseif (empty($oldPassword)) {
            $passwordError = 'الرجاء إدخال كلمة المرور القديمة';
        } elseif (strlen($password) < 8) {
            $passwordError = 'كلمة المرور يجب أن تكون أكثر من 8 حروف';
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).*$/', $password)) {
            $passwordError = 'يجب أن تحتوي كلمة المرور على حرف واحد على الأقل، رقم واحد على الأقل، ورمز واحد على الأقل';
        } elseif (empty($confirmPassword)) {
            $confirmPasswordError = 'الرجاء تأكيد كلمة المرور';
        } elseif ($password !== $confirmPassword) {
            $confirmPasswordError = 'كلمة السر غير مطابقة';
        }
    }

    // If there are no errors, update the database
    if (empty($nameError) && empty($passwordError) && empty($confirmPasswordError)) {
        if (!empty($password)) {
            // Update both name and password
            $stmt = $con->prepare("UPDATE administrator SET name=?, password=? WHERE email=?");
            $stmt->execute([$name, $password, $admin_email]);
        } else {
            // Update only name
            $stmt = $con->prepare("UPDATE administrator SET name=? WHERE email=?");
            $stmt->execute([$name, $admin_email]);
        }

        // Set success message and redirect
        $successMsg = 'تم تعديل بيانات الملف الشخصي بنجاح';
        header("Location: edit_profile.php?success_message=".urlencode($successMsg));
        exit;
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

<!-- Start Navbar Section -->
<nav class="site-nav">
    <div class="container">
        <div class="site-navigation d-flex justify-content-between align-items-center">
            <a class="m-0 float-right" href="../index.php">
                <img src="../assets/images/logo.PNG" alt="" style="height: 120px; width: 100px; font-weight: bold; color: white;">
                <span class="text-primary"></span>
            </a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left align-items-center" style="font-weight: bold; font-size: 24px;">
                <li><a href="dashboard.php">الصفحة الرئيسية</a></li>
                <li><a href="edit_profile.php">تعديل بيانات الملف الشخصي</a></li>
                <!-- <li><a href="add_api_url.php">أضافة رابط الربط</a></li> -->
                <li><a href="../logout.php">تسجيل الخروج</a></li>
            </ul>

            <a class="burger ml-auto float-right site-menu-toggle js-menu-toggle d-inline-block d-lg-none light" data-target="#main-navbar" data-toggle="collapse" href="../index.php">
                <span></span>
            </a>
        </div>
    </div>
</nav>
<!-- End Navbar Section -->

<!-- Start Hero Section -->
<div class="hero hero-inner" style="background: url('../assets/images/hala-alghanim-f5ZB0LHwi0s-unsplash.jpg'); background-size: cover; position: relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">تعديل بيانات الملف الشخصي</h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->

<!-- Start Our Supervisor Section -->
<div class="px-5 py-5" style="min-height: 80vh;">
    <?php if ($successMsg): ?>
        <div id="successMessage" class="d-flex justify-content-center py-3">
            <div class="alert alert-success w-50 text-center" role="alert">
                <?php echo $successMsg; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <div  class="bg-white rounded shadow p-5 text-center">
                <form  method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="font-size:
                 15px">

                    <div dir="rtl" class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $admin['name']; ?>">
                        <span class="error"><?php echo $nameError; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="oldPassword">كلمة المرور</label>
                        <input type="text" class="form-control" readonly id="oldPassword" name="oldPassword" value="<?php echo $admin['password']; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <span class="error"><?php echo $passwordError ?></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="confirmPassword">تأكيد كلمة المرور</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                        <span class="error"><?php echo $confirmPasswordError ?></span>

                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-2" name="editProfile">تعديل البيانات
                        الشخصية</button>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Our Supervisor Section -->

<!-- Start Footer Section -->
<div class="site-footer ">
    <div class="inner first">
        <div class="inner dark">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-8 mb-md-0 mx-auto">
                        <p>جميع الحقوق محفوظة للتوصية بالجولات @ 2024</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Footer Section -->

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
