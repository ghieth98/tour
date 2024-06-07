<?php

// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include the file containing the database connection
include "../connection.php";

// Retrieve the tourist ID from the session
$tourist_id = $_SESSION['tourist_id'];

// Prepare SQL query to fetch destination information for favorites of the tourist
$query = $con->prepare(
    "
    SELECT d.destination_id, d.name, d.description, d.working_hours, d.phone_number, 
           r.stars, di.image AS destination_image 
    FROM destination AS d 
    JOIN (
        SELECT destination_id, MIN(destination_image_id) AS first_image_id 
        FROM tours.destination_images 
        GROUP BY destination_id
    ) AS first_images ON d.destination_id = first_images.destination_id 
    JOIN tours.destination_images AS di ON first_images.first_image_id = di.destination_image_id
    LEFT JOIN tours.favorite f ON d.destination_id = f.destination_id
    LEFT JOIN tours.rate r ON d.destination_id = r.destination_id
    WHERE f.tourist_id = ?
    GROUP BY d.destination_id
"
);

// Execute the query with the tourist ID as parameter
$query->execute([$tourist_id]);

// Fetch all the rows returned by the query into $favorites variable
$favorites = $query->fetchAll();

// Retrieve success message from URL query parameters (if available)
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
    عرض بيانات الوجهات المفضلة
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
    foreach ($favorites as $favorite): ?>

        <tr>
            <td><?php
                echo $favorite['destination_id'] ?></td>
            <td><?php
                echo $favorite['name'] ?></td>
            <td><?php
                echo $favorite['description'] ?></td>
            <td><?php
                echo $favorite['working_hours'] ?></td>
            <td><?php
                echo $favorite['range'] ?></td>
            <td><?php
                echo $favorite['phone_number'] ?></td>
            <td><?php
                echo $favorite['stars'] . ' نجوم ' ?></td>
            <td>
                <img src="../uploads/<?php
                echo $favorite['destination_image'] ?>" alt="destination image"
                     style="height: 80px">
            </td>

            <td>
                <div style="display: inline;">
                    <a href="show_destination.php?destination_id=<?php
                    echo $favorite['destination_id'] ?>"
                       type="button">عرض الوجهة</a>


                    <form method="post"
                          action="add_favorite.php?destination_id=
                    <?php
                          echo $favorite['destination_id'] ?>">
                        <button type="submit">اضف او ازل من المفضلة</button>
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

        // Handle click events for adding favorites using event delegation
        $(document).on('click', '.favorite', function () {
            const destinationID = $(this).data('destination_id');
            const button = $(this);

            $.ajax({
                url: 'add_favorite.php',
                type: 'POST',
                data: {destination_id: destinationID},
                success: function (response) {
                    alert(response);
                    // Update button text based on response
                    if (response === 'Favorite added') {
                        button.text('remove favorite');
                    } else if (response === 'Favorite removed') {
                        button.text('add favorite');
                    }
                }
            });
        });

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