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

// Fetch all reviews from the database, along with associated destination and tourist names
$query = $con->prepare("
    SELECT 
        review.*, 
        d.name AS destination_name, 
        t.name AS tourist_name 
    FROM 
        review 
    JOIN 
        tours.destination d 
    ON 
        d.destination_id = review.destination_id 
    JOIN 
        tours.tourist t 
    ON 
        t.tourist_id = review.tourist_id
");
$query->execute();
$comments = $query->fetchAll();

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
    عرض بيانات المراجعات
</h1>

<span class="success"><?php echo $successMsg ?> </span>

<br><br>

<a href="dashboard.php">الصفحة الشخصية</a>


<br><br><br>


<table>
    <thead>
    <tr>
        <th>الرقم التعريفي</th>
        <th>اسم السائح</th>
        <th>اسم الوجهة</th>
        <th>نص الريفيو</th>
        <th>تاريخ الريفيو</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($comments as $comment): ?>

        <tr>
            <td> <?php echo $comment['review_id'] ?> </td>

            <td> <?php echo $comment['tourist_name'] ?> </td>

            <td> <?php echo $comment['destination_name'] ?> </td>
            <td> <?php echo $comment['body'] ?> </td>
            <td> <?php echo $comment['date'] ?> </td>
            <td>
                <div style="display: inline;">

                    <form action="delete_comment.php?review_id=<?php echo $comment['review_id'] ?>"
                          method="post" onsubmit="return confirm('هل تريد حذف هذا الريفيو ؟');"
                          style="display: inline;">
                        <button type="submit">
                            حذف
                        </button>
                    </form>

                </div>
            </td>
        </tr>

    <?php endforeach; ?>

    </tbody>

</table>
</body>
</html>

