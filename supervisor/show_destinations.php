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

$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ?
    intval($_GET['city_id']) : 0;

// Fetch destinations and their first associated image for the supervisor from the database
$query = $con->prepare(
    "
    SELECT d.*, di.image AS destination_image 
    FROM destination AS d 
    JOIN (
        SELECT destination_id, MIN(destination_image_id) AS first_image_id 
        FROM tours.destination_images 
        GROUP BY destination_id
    ) AS first_images ON d.destination_id = first_images.destination_id 
    JOIN tours.destination_images AS di ON first_images.first_image_id = di.destination_image_id
    WHERE supervisor_id=? AND city_id=?
"
);
$query->execute([$supervisor_id, $city_id]);
$destinations = $query->fetchAll();

// Check if there's a success message passed via GET parameter, if not, set it to an empty string
$successMsg = $_GET['success_message'] ?? '';
?>


<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

<span class="success"><?php
    echo $successMsg ?> </span>

<br><br>

<a href="dashboard.php">الصفحة الشخصية</a>

<br><br>
<a href="add_destination.php">إضافة وجهة جديدة</a>

<br><br><br>


<table>
    <thead>
    <tr>
        <th>الرقم التعريفي</th>
        <th>الاسم</th>
        <th>وصف الوجهة</th>
        <th>أوقات العمل</th>
        <th>ساعات العمل</th>
        <th>رقم الهاتف</th>
        <th>الصورة</th>
        <th>ألإجراءات</th>
    </tr>
    </thead>

    <tbody>
    <?php
    foreach ($destinations as $destination): ?>
        <tr>
            <td><?php
                echo $destination['destination_id'] ?></td>
            <td><?php
                echo $destination['name'] ?></td>
            <td><?php
                echo $destination['description'] ?></td>
            <td><?php
                echo $destination['working_hours'] ?></td>
            <td><?php
                echo $destination['range'] ?></td>
            <td><?php
                echo $destination['phone_number'] ?></td>
            <td>
                <img src="../uploads/<?php
                echo $destination['destination_image'] ?>"
                     alt="destination image"
                     style="height: 80px">
            </td>
            <td>
                <div style="display: inline;">
                    <a href="edit_destination.php?destination_id=<?php
                    echo $destination['destination_id'] ?>"
                       type="button">تعديل</a>

                    <form action="delete_destination.php?destination_id=<?php
                    echo $destination['destination_id'] ?>"
                          method="post"
                          onsubmit="return confirm('هل تريد حذف هذه الوجهة ؟');"
                          style="display: inline;">
                        <input type="hidden" name="city_id" value="<?php
                        echo $city_id ?>">
                        <button type="submit">حذف</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php
    endforeach; ?>
    </tbody>
</table>
</body>
</html>
