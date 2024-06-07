<?php

// Start the session to allow session variables usage
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../connection.php"; // Assuming this file contains the database connection logic

// Retrieve supervisor ID from session
$tourist_id = $_SESSION['tourist_id'];
$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ?
    intval($_GET['city_id']) : 0;

// Fetch destinations and their first associated image for the supervisor from the database
$query = $con->prepare(
    "
   SELECT d.*, r.stars, di.image AS destination_image 
    FROM destination AS d 
    JOIN (
        SELECT destination_id, MIN(destination_image_id) AS first_image_id 
        FROM tours.destination_images 
        GROUP BY destination_id
    ) AS first_images ON d.destination_id = first_images.destination_id 
    JOIN tours.destination_images AS di ON first_images.first_image_id = di.destination_image_id
    LEFT JOIN tours.favorite f on d.destination_id = f.destination_id
    LEFT JOIN tours.rate r on d.destination_id = r.destination_id
    WHERE d.city_id=?
   GROUP BY d.destination_id

"
);

$query->execute([$city_id]);
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

<span class="success"><?php
    echo $successMsg ?> </span>

<br><br>

<a href="dashboard.php">الصفحة الشخصية</a>


<br><br><br>

<div>
    <label for="search">ابحث</label>
    <input type="text" id="search" placeholder="ابحث هنا...">
</div>

<br>
<table>
    <thead>
    <tr>
        <th>الرقم التعريفي</th>
        <th>الاسم</th>
        <th>وصف الوجهة</th>
        <th>مواعيد العمل</th>
        <th>ساعات العمل</th>
        <th>رقم الهاتف</th>
        <th>التقييم</th>
        <th>الصورة</th>
        <th>ألإجراءات</th>
    </tr>
    </thead>

    <tbody id="showSearch">
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
            <td><?php
                echo $destination['stars'] . ' نجوم ' ?></td>
            <td>
                <img src="../uploads/<?php
                echo $destination['destination_image'] ?>" alt="destination image"
                     style="height: 80px">
            </td>

            <td>
                <div style="display: inline;">
                    <a href="show_destination.php?destination_id=<?php
                    echo $destination['destination_id'] ?>"
                       type="button">عرض الوجهة</a>


                    <form method="post" action="add_favorite.php?destination_id=<?php
                    echo $destination['destination_id'] ?>">
                        <input type="hidden" name="city_id" value="<?php
                        echo $city_id ?>">
                        <button type="submit">اضف أو ازل من المفضلة</button>
                    </form>


                </div>

            </td>
        </tr>
    <?php
    endforeach; ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        // Store the original HTML content of the table body
        const originalTableContent = $("#showSearch").html();

        $('#search').on('keyup', function () {
            let search = $(this).val().trim(); // Trim the search string to handle empty space
            if (search !== '') {
                $.ajax({
                    method: 'POST',
                    url: 'search_destination.php',
                    data: {name: search},
                    success: function (response) {
                        $("#showSearch").html(response);
                    }
                });
            } else {
                // If search is empty, display the original table content
                $("#showSearch").html(originalTableContent);
            }
        });

    });
</script>
</body>
</html>
