<?php
// Start session to maintain user's session data
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php'); // Corrected typo in header function
    exit; // Added exit to stop further execution
}

// Include necessary files for validation and database connection
include "../validate.php";
include "../connection.php";

// Retrieve destination ID from GET parameter or set to 0 if not provided
$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval($_GET['destination_id']) : 0;

$query = $con->prepare("SELECT * FROM city");
$query->execute();
$cities = $query->fetchAll();

$attraction_query = $con->prepare("SELECT * FROM attraction WHERE destination_id = ?");
$attraction_query->execute([$destination_id]);
$attraction = $attraction_query->fetch();

// Fetch destination data from the database based on the provided destination ID
$query = $con->prepare("SELECT * FROM destination  WHERE destination_id=?");
$query->execute([$destination_id]);
$destination = $query->fetch();

// Fetch destination image data from the database based on the provided destination id
$imageQuery = $con->prepare("SELECT * FROM destination_images WHERE destination_id=?");
$imageQuery->execute([$destination_id]);
$destination_image = $imageQuery->fetch();
$destination_image_id = $destination_image['destination_image_id'];

// Initialize variables for form fields and error messages
$name = $description = $workingHours = $phoneNumber = $days = $budget = $city = $range = '';
$age = $needs = $personality = $morningness = $noiseness = $adventurousness = $space = $environment = '';

$nameError = $descriptionError = $workingHoursError = $phoneNumberError = $daysError = $rangeError = '';
$budgetError = $cityError = $ageError = $needsError = $personalityError = $morningnessError = '';
$noisenessError = $adventurousnessError = $spaceError = $environmentError = '';

$imageError = ''; // Initialize imageError here
$successMsg = '';

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Start the transaction
        $con->beginTransaction();

        // Validate form inputs
        $name = validate($_POST['name']);
        $description = validate($_POST['description']);
        $workingHours = validate($_POST['working_hours']);
        $phoneNumber = validate($_POST['phone_number']);
        $city = validate($_POST['city']);
        $range = validate($_POST['range']);
        $age = validate($_POST['age']);
        $needs = validate($_POST['needs']);
        $budget = validate($_POST['budget']);
        $personality = validate($_POST['personality']);
        $morningness = validate($_POST['morningness']);
        $noiseness = validate($_POST['noiseness']);
        $adventurousness = validate($_POST['adventurousness']);
        $space = validate($_POST['space']);
        $environment = validate($_POST['environment']);
        $days = validate($_POST['days']);

        // Check for empty fields and set appropriate error messages
        if (empty($name)) {
            $nameError = 'الرجاء أدخال اسم الوجهة';
        } elseif (empty($description)) {
            $descriptionError = 'الرجاء أدخال وصف الوجهة';
        } elseif (empty($workingHours)) {
            $workingHoursError = 'الرجاء أدخال أوقات العمل';
        } elseif (empty($range)) {
            $rangeError = 'الرجاء أدخال المدى';
        } elseif (empty($phoneNumber)) {
            $phoneNumberError = 'الرجاء أدخال رقم الهاتف';
        } elseif (!preg_match('/^05\d{8}$/', $phoneNumber)) {
            $phoneNumberError = 'رقم الهاتف غير مسموح حيث يجب ان يبدأ ب 05 و أن يتكون من 10 أرقام';
        } elseif (empty($city)) {
            $cityError = 'الرجاء إضافة المدينة ';
        } elseif (empty($days)) {
            $daysError = 'الرجاء أدخال عدد الأيام';
        } elseif (empty($budget)) {
            $budgetError = 'الرجاء أدخال الميزانية';
        } elseif (empty($age)) {
            $ageError = 'الرجاء أدخال العمر';
        } elseif (empty($needs)) {
            $needsError = 'الرجاء أدخال الاحتياجات الخاصة';
        } elseif (empty($personality)) {
            $personalityError = 'الرجاء أدخال الشخصية';
        } elseif (empty($morningness)) {
            $morningnessError = 'الرجاء أدخال الفترة';
        } elseif (empty($noiseness)) {
            $noisenessError = 'الرجاء أدخال حالة الضجيج';
        } elseif (empty($adventurousness)) {
            $adventurousnessError = 'الرجاء أدخال حالة الضجيج';
        } elseif (empty($space)) {
            $spaceError = 'الرجاء أدخال حالة الفضاء';
        } elseif (empty($environment)) {
            $environmentError = 'الرجاء أدخال نوع البيئة';
        } else {
            // Update destination data in the database
            $stmt = $con->prepare("UPDATE destination SET name=?, description=?, working_hours=?, `range`=?, phone_number=?, city_id=? WHERE destination_id=?");
            $stmt->execute([$name, $description, $workingHours, $range, $phoneNumber, $city, $destination_id]);

            // Update attraction data in the database
            $stmt = $con->prepare("UPDATE attraction SET days=?, budget=?, age=?, needs=?, personality=?, morningness=?, noiseness=?, adventurousness=?, space=?, environment=? WHERE destination_id=?");
            $stmt->execute([
                $days,
                $budget,
                $age,
                $needs,
                $personality,
                $morningness,
                $noiseness,
                $adventurousness,
                $space,
                $environment,
                $destination_id
            ]);
            $successMsg = 'تم تعديل بيانات الوجهة بنجاح';

            // Handle image upload
            if (!empty($_FILES['images']['name'])) {
                $imageName = $_FILES['images']['name'];
                $tmpFilePath = $_FILES['images']['tmp_name'];
                $extension = pathinfo($imageName, PATHINFO_EXTENSION);

                if (!in_array($extension, ['jpeg', 'png', 'svg', 'jpg'])) {
                    $imageError = 'صيغة الملف غير مدعومة';
                } else {
                    $newFilePath = "../uploads/" . uniqid() . '_' . $imageName;
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $stmt = $con->prepare("UPDATE destination_images SET image=?, date=NOW() WHERE destination_image_id=? ");
                        $stmt->execute([$newFilePath, $destination_image_id]);
                        $successMsg = 'تم تعديل بيانات الوجهة بنجاح';
                    } else {
                        $imageError = 'تم تعديل بيانات الوجهة، ولكن حدث خطأ أثناء رفع الصور. يرجى المحاولة مرة أخرى.';
                    }
                }
            }

            // Commit the transaction
            $con->commit();

            // Redirect to show_destinations.php with success message
            header("Location: show_destinations.php?city_id=" . $city . "&success_message=" . urlencode($successMsg));
            exit;
        }
    } catch (PDOException $e) {
        // Rollback the transaction in case of any errors
        $con->rollback();
        echo "Error: " . $e->getMessage();
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
                <li class=""><a href="edit_profile.php">تعديل بيانات الملف الشخصي</a></li>
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
                    <h1 class="mb-0">اضافة مدينة جديد</h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<!--Start add city Section-->

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
<div class="container">
    <div class="justify-content-center d-flex text-center   py-5 ">
        <form method="post" class="bg-white rounded shadow p-5"
              action="<?php
              echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>"
              enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label" for="name">اسم الوجهة</label>
                <input class="form-control" type="text" id="name" name="name" value="<?php
                echo $destination['name'] ?>"/>
                <span class="error"> <?php
                    echo $nameError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label" for="description">وصف الوجهة</label>
                <textarea class="form-control" name="description"
                          id="description"><?php
                    echo $destination['description'] ?></textarea>
                <span class="error"> <?php
                    echo $descriptionError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label" for="working_hours">أوقات العمل</label>
                <input class="form-control" type="text" id="working_hours" name="working_hours"
                       value="<?php
                       echo $destination['working_hours'] ?>"/>
                <span class="error"> <?php
                    echo $workingHoursError ?></span>
            </div>



            <div class="mb-3">
                <label class="form-label" for="range">ساعات العمل</label>
                <input class="form-control" type="text" id="range" name="range"
                       value="<?php
                       echo $destination['range'] ?>"/>
                <span class="error"> <?php
                    echo $rangeError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label" for="phone_number">رقم الهاتف</label>
                <input class="form-control" type="tel" id="phone_number" name="phone_number"
                       value="<?php
                       echo $destination['phone_number'] ?>"/>
                <span class="error"> <?php
                    echo $phoneNumberError ?></span>
            </div>



            <div class="mb-3">
                <label class="form-label" for="days">عدد أيام الجولة:</label>
                <input type="number" class="form-control" name="days" value="<?php echo $attraction['days'] ?>" id="days">
                <span class="error"> <?php
                    echo $daysError ?></span>
            </div>



            <div class="mb-3">
                <label class="form-label" for="budget">ميزانية السائح:</label>
                <select class="form-control" id="budget" name="budget">
                    <option value="low">ميزانية منخفضة</option>
                    <option value="medium">ميزانية متوسطة</option>
                    <option value="high">ميزانية عالية</option>
                </select>
                <span class="error"> <?php
                    echo $budgetError ?></span>
            </div>


            <div class="mb-3">
                <label class="form-label" for="city">المدينة</label>
                <select class="form-control" id="city" name="city">
                    <?php
                    foreach ($cities as $city): ?>
                        <option value="
        <?php
                        echo $city['city_id'] ?>>">
                            <?php
                            echo $city['name'] ?>
                        </option>
                    <?php
                    endforeach; ?>
                </select>
                <span class="error"> <?php
                    echo $cityError ?></span>
            </div>


            <div class="mb-3">
                <label class="form-label" for="age">الفئة العمرية للسائح:</label>
                <select class="form-control" id="age" name="age">
                    <option value="child">طفل</option>
                    <option value="young">شاب</option>
                    <option value="adult">بالغ</option>
                    <option value="elderly">كبير فى السن</option>
                </select>
                <span class="error"> <?php
                    echo $ageError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label" for="needs">احتياجات خاصة:</label>
                <select class="form-control" id="needs" name="needs">
                    <option value="yes">نعم</option>
                    <option value="no">لا</option>
                </select>
                <span class="error"> <?php
                    echo $needsError ?></span>
            </div>


            <div class="mb-3">
                <label class="form-label" for="personality">نوع الوجهة</label>
                <select class="form-control" id="personality" name="personality">
                    <option value="night"> للأنشطة الليلية</option>
                    <option value="morning"> للأنشطة الصباحية</option>
                    <option value="adventure"> للأنشطة المغامرة</option>
                    <option value="calm"> للأنشطة الهادئة</option>
                    <option value="open">للأنشطة منفتحة</option>
                    <option value="closed">للأنشطة منغلقة</option>
                </select>
                <span class="error"> <?php
                    echo $personalityError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label"> المدّة:</label><br>
                <input type="hidden" id="morning" name="morningness" value="<?php echo $attraction['morningness'] ?>>">
                <input type="radio" id="morning" name="morningness" value="morning">
                <label class="form-check-label" for="morning">الصباح</label>
                <input type="radio" id="evening" name="morningness" value="evening">
                <label class="form-check-label" for="evening">المساء</label>
                <input type="radio" id="bothMorningEvening" name="morningness" value="both">
                <label class="form-check-label" for="bothMorningEvening">كليهما</label>
                <span class="error"> <?php
                    echo $morningnessError ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label">حالة الضجيج:</label><br>
                <input type="hidden" id="loud" name="noiseness" value="<?php echo $attraction['noiseness'] ?>>">
                <input type="radio" id="loud" name="noiseness" value="loud">
                <label class="form-check-label" for="loud">صاخب</label>
                <input type="radio" id="quiet" name="noiseness" value="quiet">
                <label class="form-check-label" for="quiet">هادئ</label>
                <input type="radio" id="bothNoises" name="noiseness" value="both">
                <label class="form-check-label" for="bothNoises">كليهما</label>
                <span class="error"> <?php
                    echo $noisenessError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label">حالة المغامرة:</label><br>
                <input type="hidden" id="adventurous" name="adventurousness" value="<?php echo $attraction['adventurousness'] ?>>">
                <input type="radio" id="adventurous" name="adventurousness" value="adventurous">
                <label class="form-check-label" for="adventurous">مغامر</label>
                <input type="radio" id="unadventurous" name="adventurousness" value="unadventurous">
                <label class="form-check-label" for="unadventurous">غير مغامر</label>
                <span class="error"> <?php
                    echo $adventurousnessError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label">حالة الفضاء:</label><br>
                <input type="hidden" id="openSpace" name="space" value="<?php echo $attraction['space'] ?>>">
                <input type="radio" id="openSpace" name="space" value="open">
                <label class="form-check-label" for="openSpace">مفتوح</label>
                <input type="radio" id="enclosedSpace" name="space" value="enclosed">
                <label class="form-check-label" for="enclosedSpace">مغلق</label>
                <input type="radio" id="bothSpaces" name="space" value="both">
                <label class="form-check-label" for="bothSpaces">كليهما</label>
                <span class="error"> <?php
                    echo $spaceError ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label"> نوع البيئة:</label><br>
                <input type="hidden" id="nature" name="environment" value="<?php echo $attraction['environment'] ?>>">
                <input type="radio" id="nature" name="environment" value="nature">
                <label class="form-check-label" for="nature">طبيعية</label>
                <input type="radio" id="urban" name="environment" value="urban">
                <label class="form-check-label" for="urban">حضرية</label>
                <input type="radio" id="bothEnvironments" name="environment" value="both">
                <label class="form-check-label" for="bothEnvironments">كليهما</label>
                <span class="error"> <?php
                    echo $environmentError ?></span>
            </div>

            <div class="mb-3">

                <label class="form-label" for="images">الصور</label>
                <input class="form-control" type="file" id="images" name="images" multiple/>

                <span class="error"> <?php
                    echo $imageError ?></span>
            </div>


            <button type="submit" class="btn btn-primary py-2 px-4" name="addDestination">
                تعديل
            </button>

        </form>

    </div>
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


</body>

</html>
