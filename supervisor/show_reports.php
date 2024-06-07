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

// Fetch all reports from the database along with associated information
$query = $con->prepare("
    SELECT
        report.*,
        t.name AS tourist_name,
        r.body AS reply_body,
        rv.body AS review_body
    FROM
        report
    JOIN
        tourist t
    ON
        t.tourist_id = report.tourist_id
    LEFT JOIN
        reply r
    ON
        report.reply_id = r.reply_id
    LEFT JOIN
        review rv
    ON
        report.review_id = rv.review_id
");
$query->execute();
$reports = $query->fetchAll();

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
    عرض بيانات البلاغات
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
        <th> نص البلاغ</th>
        <th> نص التعليق/الرد</th>
        <th>تاريخ التعليق</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($reports as $report): ?>

        <tr>

            <td>
                <?php echo $report['report_id'] ?>
            </td>

            <td>
                <?php echo $report['tourist_name'] ?>
            </td>

            <td>
                <?php echo $report['body'] ?>
            </td>

            <td>
                <?php echo $report['review_body'] ?? $report['reply_body']; ?>
            </td>


            <td> <?php echo $report['date'] ?> </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>
</body>
</html>

