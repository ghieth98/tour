<?php

// Start session to maintain user's session data
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location: ../login.php');
}

// Include necessary files for validation and database connection
include "../validate.php";
include "../connection.php";

// Get supervisor ID from session
$supervisor_id = $_SESSION['supervisor_id'];

// Prepare a query to fetch all cities
$query = $con->prepare("SELECT * FROM city");
$query->execute();
$cities = $query->fetchAll();

// Initialize form field variables and error messages
$name = $description = $start_time = $end_time = $phoneNumber = $budget = $city = '';
$age = $needs = $personality = $morningness = $noiseness = $adventurousness = $space = $environment = '';

// Initialize error messages
$nameError = $descriptionError = $start_timeError = $end_timeError = $phoneNumberError = $daysError = '';
$budgetError = $cityError = $ageError = $needsError = $personalityError = $morningnessError = '';
$noisenessError = $adventurousnessError = $spaceError = $environmentError = '';
$imageError = '';
$successMsg = '';

// Array to store file extensions
$extensions = [];
$days = [];
// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Start a transaction
        $con->beginTransaction();

        // Validate form inputs
        $name = validate($_POST['name']);
        $description = validate($_POST['description']);
        $phoneNumber = validate($_POST['phone_number']);
        $city = validate($_POST['city']);
        $start_time = ($_POST['start_time']);
        $end_time = ($_POST['end_time']);
//        $range = validate($_POST['range']);

        // Perform validation checks and set error messages
        if (empty($name)) {
            $nameError = 'الرجاء أدخال اسم الوجهة';
        } elseif (empty($description)) {
            $descriptionError = 'الرجاء أدخال وصف الوجهة';
        } elseif (empty($start_time)) {
            $workingHoursError = 'الرجاء أدخال أوقات العمل';
        } elseif (empty($end_time)) {
            $workingHoursError = 'الرجاء أدخال أوقات العمل';
        } elseif (empty($city)) {
            $cityError = 'الرجاء أدخال المدينة';
        }
        elseif (empty($phoneNumber)) {
            $phoneNumberError = 'الرجاء أدخال رقم الهاتف';
        } elseif (!preg_match('/^05\d{8}$/', $phoneNumber)) {
            $phoneNumberError = 'رقم الهاتف غير مسموح حيث يجب ان يبدأ ب 05 و أن يتكون من 10 أرقام';
        } else {
            // Prepare and execute SQL statement to insert new destination
            $stmt = $con->prepare("INSERT INTO destination (name, description, start_date, end_date, phone_number, supervisor_id, city_id) VALUES (?, ?, ?, ?,?,?,?)");
            $stmt->execute([$name, $description, $start_time, $end_time, $phoneNumber, $supervisor_id, $city]);

            // Get the last inserted destination ID
            $destination_id = $con->lastInsertId();

            // Validate additional form inputs for attraction table
            $age = validate($_POST['age']);
            $needs = validate($_POST['needs']);
            $budget = validate($_POST['budget']);
            $personality = validate($_POST['personality']);
            $morningness = validate($_POST['morningness']);
            $noiseness = validate($_POST['noiseness']);
            $adventurousness = validate($_POST['adventurousness']);
            $space = validate($_POST['space']);
            $environment = validate($_POST['environment']);
            $days = ($_POST['days']);

            // Perform validation checks for attraction fields
            if (empty($days)) {
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
                // Prepare and execute SQL statement to insert data into the attraction table
                $daysNumber = count($days);

                $stmt = $con->prepare(
                    "INSERT INTO attraction (days, budget, age, needs, personality, morningness, noiseness, adventurousness, space, environment, destination_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $result = $stmt->execute([
                    $daysNumber,
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
            }

            // Handle multiple image uploads
            $imageNameArray = $_FILES['images']['name'];

            // Loop through each file name in the array
            foreach ($imageNameArray as $imageName) {
                // Get the extension of each file and add it to the extensions array
                $extensions[] = pathinfo($imageName, PATHINFO_EXTENSION);
            }

            // Determine the unique extension, if all files have the same extension
            $extension = (count(array_unique($extensions)) === 1) ? $extensions[0] : null;

            // Validate image uploads
            if (empty($imageNameArray[0])) {
                $imageError = 'برجاء إضافة صورة';
            } elseif (!in_array($extension, ['jpeg', 'png', 'svg', 'jpg'])) {
                $imageError = 'صيغة الملف غير مدعومة';
            } else {
                $total_images = count($imageNameArray);
                // Process each image
                for ($i = 0; $i < $total_images; $i++) {
                    // Get the temporary file path
                    $tmpFilePath = $_FILES['images']['tmp_name'][$i];

                    // Check if file exists
                    if ($tmpFilePath != '') {
                        // Generate unique filename
                        $newFilePath = "../uploads/".uniqid().'_'.$imageNameArray[$i];

                        // Move the file to the uploads directory
                        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                            // Insert image details into the database
                            $stmt = $con->prepare(
                                "INSERT INTO destination_images (image, destination_id, date) VALUES (?, ?, NOW())"
                            );
                            $stmt->execute([$newFilePath, $destination_id]);
                            $successMsg = 'تم إضافة وجهة جديدة بنجاح';
                        } else {
                            // If file upload failed, set upload error
                            $imageError = 'تم إضافة وجهة جديدة بنجاح و لكن حدث خطأ أثناء إضافة الصورة بالرجاء المحاولة مرة أحري';
                        }
                    }
                }
                // Commit the transaction
                $con->commit();

                // Redirect to show_destinations.php with success message
                header("Location: show_destinations.php?city_id=".$city."&success_message=".urlencode($successMsg));
                exit;
            }
        }
    } catch (PDOException $e) {
        // Rollback transaction on error
        $con->rollback();
        error_log("Transaction error: ".$e->getMessage());
        echo "Error: ".$e->getMessage();
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
        <div class="site-navigation d-flex justify-content-between align-items-center">
            <a class="m-0 float-right" href="../index.php">
                <img src="../assets/images/logo.PNG" alt="" style="height: 120px; width: 100px; font-weight: bold; color: white;">
                <span class="text-primary"></span>
            </a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left align-items-center" style="font-weight: bold; font-size: 24px;">
                <li class=""><a href="dashboard.php">الصفحة الرئيسية</a></li>
                <li class=""><a href="edit_profile.php">الملف الشخصي</a></li>
                <li class=""><a href="show_destinations.php">عرض الوجهات</a></li>
                <li class=""><a href="show_reports.php">عرض البلاغات</a></li>
                <!--                <li class=""><a href="show_tourists.php">إدارة السياح</a></li>-->
                <li><a href="../logout.php" onclick="return confirm('هل تريد تسجيل الخروج؟')">تسجيل الخروج</a></li>
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
<div class="hero hero-inner" style="background: url('../assets/images/edge.jpg'); background-size: cover; position:
relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">اضافة وجهة جديد</h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<!--Start add city Section-->
<div class="px-5 py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
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
    <div class="col-md-5 bg-white rounded shadow p-5 text-center">

            <form method="post" style="font-size: 17px"
                  action="<?php
                  echo htmlspecialchars($_SERVER['PHP_SELF']) ?>"
                  enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label" for="name">اسم الوجهة</label>
                    <input class="form-control" type="text" id="name" name="name" value="<?php
                    echo $name ?>"/>
                    <span class="error"> <?php
                        echo $nameError ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">وصف الوجهة</label>
                    <textarea class="form-control" name="description"
                              id="description"><?php
                        echo $description ?></textarea>
                    <span class="error"> <?php
                        echo $descriptionError ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="working_hours">ساعات العمل</label>
                    <div class="mob"><label class="text-grey mr-1">من</label>
                        <input class="ml-1 form-control" type="time" name="start_time">
                        <span class="error"> <?php
                            echo $start_timeError ?></span>
                    </div>
                    <div class="mob mb-2"><label class="text-grey mr-4">إلى</label>
                        <input class="ml-1 form-control" type="time" name="end_time">
                        <span class="error"> <?php
                            echo $end_timeError ?></span>
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label" for="phone_number">رقم الهاتف</label>
                    <input class="form-control" type="tel" id="phone_number" name="phone_number"
                           value="<?php
                           echo $phoneNumber ?>"/>
                    <span class="error"> <?php
                        echo $phoneNumberError ?></span>
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
                    <label class="form-label" for="days">عدد أيام الجولة :</label><br>
                    <input type="checkbox" id="saturday" name="days[]" value="السبت">
                    <label class="form-check-label ml-1" for="saturday">السبت</label>
                    <input type="checkbox" id="sunday" name="days[]" value="الأحد">
                    <label class="form-check-label ml-1" for="sunday">ألأحد</label>
                    <input type="checkbox" id="monday" name="days[]" value="الأثنين">
                    <label class="form-check-label ml-1" for="monday">الأثنين</label>
                    <input type="checkbox" id="tuesday" name="days[]" value="الثلاثاء">
                    <label class="form-check-label ml-1" for="tuesday">الثلاثاء</label>
                    <input type="checkbox" id="wednesday" name="days[]" value="الأربعاء">
                    <label class="form-check-label ml-1" for="wednesday">الأربعاء</label>
                    <input type="checkbox" id="thursday" name="days[]" value="الخميس">
                    <label class="form-check-label ml-1" for="thursday">الخميس</label>
                    <input type="checkbox" id="friday" name="days[]" value="الجمعة">
                    <label class="form-check-label ml-1" for="friday">الجمعة</label>
                    <span class="error"> <?php
                        echo $daysError ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="budget">ميزانية السائح:</label><br>
                    <input required type="radio" id="budgetLow" name="budget" value="low">
                    <label class="form-check-label ml-1" for="budgetLow">ميزانية منخفضة</label>
                    <input required type="radio" id="budgetMedium" name="budget" value="medium">
                    <label class="form-check-label ml-1" for="budgetMedium">ميزانية متوسطة</label>
                    <input required type="radio" id="budgetHigh" name="budget" value="high">
                    <label class="form-check-label ml-1" for="budgetHigh">ميزانية عالية</label>
                    <span class="error"> <?php
                        echo $budgetError ?></span>
                </div>


                <div class="mb-3">
                    <label class="form-label">الفئة العمرية للسائح:</label><br>
                    <input required type="radio" id="ageChild" name="age" value="child">
                    <label class="form-check-label ml-1" for="ageChild">طفل</label>
                    <input required type="radio" id="ageYoung" name="age" value="young">
                    <label class="form-check-label ml-1" for="ageYoung">مراهق</label>
                    <input required type="radio" id="ageAdult" name="age" value="adult">
                    <label class="form-check-label ml-1" for="ageAdult">شاب</label>
                    <input required type="radio" id="ageElderly" name="age" value="elderly">
                    <label class="form-check-label" for="ageElderly">كبير في السن</label>
                    <span class="error"> <?php
                        echo $ageError ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="needs">احتياجات خاصة:</label><br>
                    <input required type="radio" id="needsYes" name="needs" value="yes">
                    <label class="form-check-label ml-1" for="needsYes">نعم</label>
                    <input required type="radio" id="needsNo" name="needs" value="no">
                    <label class="form-check-label" for="needsNo">لا</label>
                    <span class="error"> <?php
                        echo $needsError ?></span>
                </div>


                <div class="mb-3">
                    <label class="form-label" for="personality">نوع الوجهة</label><br>
                    <input type="radio" id="personalityNight" name="personality"
                           value="night">
                    <label class="form-check-label ml-1" for="personalityNight"> للأنشطة المسائية</label>
                    <input type="radio" id="personalityAdventure" name="personality"
                           value="adventure">
                    <label class="form-check-label ml-1" for="personalityAdventure"> للأنشطة
                        المغامرة</label>
                    <input type="radio" id="personalityCalm" name="personality" value="calm">
                    <label class="form-check-label ml-1" for="personalityCalm"> للأنشطة الهادئة و
                        المريحة</label><br>
                    <input type="radio" id="personalityOpen" name="personality" value="open">
                    <label class="form-check-label ml-1 mt-1" for="personalityOpen">شخصية متفتحة</label>
                    <input type="radio" id="personalityClosed" name="personality"
                           value="closed">
                    <label class="form-check-label ml-1" for="personalityClosed">شخصية أكثر تحفظا و
                        انغلاقا</label>
                    <span class="error"> <?php
                        echo $personalityError ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label"> المدّة:</label><br>
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
                    <input type="radio" id="loud" name="noiseness" value="loud">
                    <label class="form-check-label ml-1" for="loud">صاخب</label>
                    <input type="radio" id="quiet" name="noiseness" value="quiet">
                    <label class="form-check-label ml-1" for="quiet">هادئ</label>
                    <input type="radio" id="bothNoises" name="noiseness" value="both">
                    <label class="form-check-label ml-1" for="bothNoises">كليهما</label>
                    <span class="error"> <?php
                        echo $noisenessError ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label">حالة المغامرة:</label><br>
                    <input type="radio" id="adventurous" name="adventurousness" value="adventurous">
                    <label class="form-check-label ml-1" for="adventurous">مغامر</label>
                    <input type="radio" id="unadventurous" name="adventurousness" value="unadventurous">
                    <label class="form-check-label ml-1" for="unadventurous">غير مغامر</label>
                    <span class="error"> <?php
                        echo $adventurousnessError ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label">حالة الفضاء:</label><br>
                    <input type="radio" id="openSpace" name="space" value="open">
                    <label class="form-check-label ml-1" for="openSpace">مفتوح</label>
                    <input type="radio" id="enclosedSpace" name="space" value="enclosed">
                    <label class="form-check-label ml-1" for="enclosedSpace">مغلق</label>
                    <input type="radio" id="bothSpaces" name="space" value="both">
                    <label class="form-check-label ml-1" for="bothSpaces">كليهما</label>
                    <span class="error"> <?php
                        echo $spaceError ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label"> نوع البيئة:</label><br>
                    <input type="radio" id="nature" name="environment" value="nature">
                    <label class="form-check-label ml-1" for="nature">طبيعية</label>
                    <input type="radio" id="urban" name="environment" value="urban">
                    <label class="form-check-label ml-1" for="urban">حضرية</label>
                    <input type="radio" id="bothEnvironments" name="environment" value="both">
                    <label class="form-check-label ml-1" for="bothEnvironments">كليهما</label>
                    <span class="error"> <?php
                        echo $environmentError ?></span>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="images">اختيار صور الوجهات</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="images" name="images[]" multiple>
                        <label class="custom-file-label" for="images">اختيار صور الوجهات</label>
                    </div>
                    <span class="error"><?php
                        echo $imageError ?></span>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2" style="font-size: 14px; font-weight: bold" name="addDestination">
                    إضافة
                </button>

            </form>
        </div>
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
