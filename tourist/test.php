<?php
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
include "../connection.php";

?>

<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <title>توصية بالجولات</title>

    <style>
        .success {
            color: green;
        }
    </style>

</head>
<body>

<h1>
    عرض بيانات الوجهات
</h1>


<br><br>

<a href="dashboard.php">الصفحة الشخصية</a>


<br><br><br>

<h1>اختبار التوصية بالوجهات</h1>
<form action="result.php" method="post">

    <label for="days">عدد أيام الجولة:</label>
    <select id="days" name="days">
        <option value="1-3">1 - 3 أيام</option>
        <option value="4-7">4 - 7 أيام</option>
        <option value="8+">8 أو أكثر أيام</option>
    </select><br><br>

    <label for="month">شهر الجولة:</label>
    <input type="month" id="month" name="month"><br><br>

    <label for="budget">ميزانية السائح:</label>
    <select id="budget" name="budget">
        <option value="low">ميزانية منخفضة</option>
        <option value="medium">ميزانية متوسطة</option>
        <option value="high">ميزانية عالية</option>
    </select><br><br>

    <label for="age">الفئة العمرية للسائح:</label>
    <select id="age" name="age">
        <option value="child">طفل</option>
        <option value="young">شاب</option>
        <option value="adult">بالغ</option>
        <option value="elderly">كبير فى السن</option>
    </select><br><br>

    <label for="needs">احتياجات خاصة:</label>
    <select id="needs" name="needs">
        <option value="yes">نعم</option>
        <option value="no">لا</option>
    </select><br><br>

    <label for="personality">شخصية السائح:</label>
    <select id="personality" name="personality">
        <option value="night">محب للأنشطة الليلية</option>
        <option value="morning">محب للأنشطة الصباحية</option>
        <option value="adventure">محب للأنشطة المغامرة</option>
        <option value="calm">محب للأنشطة الهادئة</option>
        <option value="open">شخصية منفتحة</option>
        <option value="closed">شخصية منغلقة</option>
    </select><br><br>

    <button type="button">تصفية الوجهات</button>
</form>


</body>
</html>
