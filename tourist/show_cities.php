<?php

// Start the session to allow session variables usage
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../connection.php";

// Fetch all tourists from the database
$cities = $con->query("SELECT * FROM city");

// Check if there's a success message passed via GET parameter, if not, set it to an empty string
$successMsg = $_GET['success_message'] ?? '';
?>


<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>تصوية بالجولات</title>

    <style>


        .success {
            color: green;
        }
    </style>

</head>
<body>

<h1>
    عرض بيانات المدن
</h1>

<span class="success"><?php
    echo $successMsg ?> </span>

<br><br>

<a href="dashboard.php">الصفحة الشخصية</a>

<br><br>


<br><br><br>


<table>
    <thead>
    <tr>
        <th>الرقم التعريفي</th>
        <th>الاسم</th>
        <th>اليوم</th>
        <th>الطقس</th>
    </tr>
    </thead>

    <tbody>
    <?php
    foreach ($cities as $city): ?>

        <tr>
            <td> <?php
                echo $city['city_id'] ?> </td>

            <td>
                <a href="show_destinations.php?city_id=<?php
                echo $city['city_id'] ?>">
                    <?php
                    echo $city['name'] ?>
                </a>
            </td>

            <td> <?php
                echo $city['day'] ?> </td>

            <td> <?php
                echo $city['weather'] ?> </td>

            <td>
                <div style="display: inline;">


                    <a href="show_destinations.php?city_id=<?php
                    echo $city['city_id'] ?>">وجهات المدينة</a>
                </div>
            </td>

        </tr>

    <?php
    endforeach; ?>

    </tbody>

</table>
</body>

</html>

