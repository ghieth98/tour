<?php

session_start();

if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
include "../connection.php";

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
                <li class=""><a href="edit_password.php">تعديل بيانات كلمة المرور</a></li>
                <li class=""><a href="test.php">الاختبار</a></li>
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
                    <h1 class="mb-0">اختبار التوصية بالجولات</h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->


<div class="untree_co-section">
    <div class="container h-100" style="font-size: 15px">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-12 col-xl-12 ">
                <div class="row justify-content-center ">
                    <div class="col-md-10 col-lg-12 col-xl-12 order-2 order-lg-1">
                        <form class="mx-1 mx-md-4 text-right bg-white p-5 rounded shadow" data-aos="fade-up"
                              data-aos-delay="500" action="result.php" method="post">
                            <div class="mb-3">
                                <label class="form-label" for="start_date">من تاريخ:</label>
                                <input required class="form-control" type="date" id="start_date" name="start_date"
                                       onchange="calculateDays()">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="end_date">إلى تاريخ:</label>
                                <input required class="form-control" type="date" id="end_date" name="end_date"
                                       onchange="calculateDays()">
                            </div>
                            <div class="mb-2 mt-2 " id="result"></div>


                            <div class="mb-3">
                                <label class="form-label">ميزانية السائح:</label><br>
                                <input required type="radio" id="budgetLow" name="budget" value="low">
                                <label class="form-check-label" for="budgetLow">ميزانية منخفضة</label><br>
                                <input required type="radio" id="budgetMedium" name="budget" value="medium">
                                <label class="form-check-label" for="budgetMedium">ميزانية متوسطة</label><br>
                                <input required type="radio" id="budgetHigh" name="budget" value="high">
                                <label class="form-check-label" for="budgetHigh">ميزانية عالية</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">الفئة العمرية للسائح:</label><br>
                                <input required type="radio" id="ageChild" name="age" value="child">
                                <label class="form-check-label" for="ageChild">طفل</label><br>
                                <input required type="radio" id="ageYoung" name="age" value="young">
                                <label class="form-check-label" for="ageYoung">مراهق</label><br>
                                <input required type="radio" id="ageAdult" name="age" value="adult">
                                <label class="form-check-label" for="ageAdult">شاب</label><br>
                                <input required type="radio" id="ageElderly" name="age" value="elderly">
                                <label class="form-check-label" for="ageElderly">كبير في السن</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="needs">احتياجات خاصة:</label><br>
                                <input required type="radio" id="needsYes" name="needs" value="yes">
                                <label class="form-check-label" for="needsYes">نعم</label><br>
                                <input required type="radio" id="needsNo" name="needs" value="no">
                                <label class="form-check-label" for="needsNo">لا</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ما الذي يصفك بشكل أفضل:</label><br>
                                <input type="checkbox" id="personalityNight" name="personality[]"
                                       value="night">
                                <label class="form-check-label" for="personalityNight">محب للأنشطة المسائية</label><br>
                                <input type="checkbox" id="personalityAdventure" name="personality[]"
                                       value="adventure">
                                <label class="form-check-label" for="personalityAdventure">محب للأنشطة
                                    المغامرة</label><br>
                                <input type="checkbox" id="personalityCalm" name="personality[]" value="calm">
                                <label class="form-check-label" for="personalityCalm"> محب للأنشطة الهادئة و
                                    المريحة</label><br>
                                <input type="checkbox" id="personalityOpen" name="personality[]" value="open">
                                <label class="form-check-label" for="personalityOpen">شخصية متفتحة</label><br>
                                <input type="checkbox" id="personalityClosed" name="personality[]"
                                       value="closed">
                                <label class="form-check-label" for="personalityClosed">شخصية أكثر تحفظا و
                                    انغلاقا</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تفضيل المدّة:</label><br>
                                <input required type="radio" id="morning" name="morningness" value="morning">
                                <label class="form-check-label" for="morning">الصباح</label><br>
                                <input required type="radio" id="evening" name="morningness" value="evening">
                                <label class="form-check-label" for="evening">المساء</label><br>
                                <input required type="radio" id="bothMorningEvening" name="morningness" value="both">
                                <label class="form-check-label" for="bothMorningEvening">كليهما</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تفضيل الضجيج:</label><br>
                                <input required type="radio" id="loud" name="noiseness" value="loud">
                                <label class="form-check-label" for="loud">صاخب</label><br>
                                <input required type="radio" id="quiet" name="noiseness" value="quiet">
                                <label class="form-check-label" for="quiet">هادئ</label><br>
                                <input required type="radio" id="bothNoises" name="noiseness" value="both">
                                <label class="form-check-label" for="bothNoises">كليهما</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تفضيل المغامرة:</label><br>
                                <input required type="radio" id="adventurous" name="adventurousness"
                                       value="adventurous">
                                <label class="form-check-label" for="adventurous">مغامر</label><br>
                                <input required type="radio" id="unadventurous" name="adventurousness"
                                       value="unadventurous">
                                <label class="form-check-label" for="unadventurous">غير مغامر</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تفضيل الفضاء:</label><br>
                                <input required type="radio" id="openSpace" name="space" value="open">
                                <label class="form-check-label" for="openSpace">مفتوح</label><br>
                                <input required type="radio" id="enclosedSpace" name="space" value="enclosed">
                                <label class="form-check-label" for="enclosedSpace">مغلق</label><br>
                                <input required type="radio" id="bothSpaces" name="space" value="both">
                                <label class="form-check-label" for="bothSpaces">كليهما</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تفضيل نوع البيئة:</label><br>
                                <input required type="radio" id="nature" name="environment" value="nature">
                                <label class="form-check-label" for="nature">طبيعية</label><br>
                                <input required type="radio" id="urban" name="environment" value="urban">
                                <label class="form-check-label" for="urban">حضرية</label><br>
                                <input required type="radio" id="bothEnvironments" name="environment" value="both">
                                <label class="form-check-label" for="bothEnvironments">كليهما</label>
                            </div>


                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary text-center">
                                    تصفية
                                    الوجهات
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
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
<script>
    function calculateDays() {
        let startDate = document.getElementById("start_date").value;
        let endDate = document.getElementById("end_date").value;

        if (startDate && endDate) {
            startDate = new Date(startDate);
            endDate = new Date(endDate);
            let difference = endDate.getTime() - startDate.getTime();

            let daysDifference = Math.ceil(difference / (1000 * 3600 * 24));
            document.getElementById("result").innerText = "عدد الأيام الرحلة: " + daysDifference
        } else {
            document.getElementById("result").innerText = "";
        }
    }
</script>
</body>
</html>