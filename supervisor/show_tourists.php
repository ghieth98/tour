<?php
// Start the session to allow session variables usage
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../connection.php";

// Fetch all tourists from the database
$tourists = $con->query("SELECT * FROM tourist");

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
    عرض بيانات السائحين
</h1>

<span class="success"><?php echo $successMsg ?> </span>

<br><br>

<a href="dashboard.php">الصفحة الشخصية</a>

<br><br>


<br><br><br>


<table>
    <thead>
    <tr>
        <th>الرقم التعريفي</th>
        <th>الاسم</th>
        <th>البريد الإلكتروني</th>
        <th>ألإجراءات</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($tourists as $tourist): ?>

        <tr>
            <td> <?php echo $tourist['tourist_id'] ?> </td>

            <td> <?php echo $tourist['name'] ?> </td>

            <td> <?php echo $tourist['email'] ?> </td>

            <td>
                <div style="display: inline;">

                    <form action="ban_tourist.php?tourist_id=<?php echo $tourist['tourist_id'] ?>"
                          method="post" onsubmit=" <?php if ($tourist['ban'] === 'unbanned')  : ?>
                            return confirm('هل تريد حظر هذا السائح ؟');
                    <?php elseif ($tourist['ban'] === 'banned' || $tourist['ban'] === 'temporary'): ?>
                            return confirm('هل تريد رفع الحظر عن هذا السائح؟')
                    <?php endif; ?>"
                          style="display: inline;">
                        <?php if ($tourist['ban'] === 'unbanned') : ?>
                            <select name="ban" id="ban">
                                <option name="banned" value="banned">
                                    حظر
                                </option>
                                <option name="temporary" value="temporary">
                                    حظر مؤقت
                                </option>
                            </select>
                            <button type="submit">
                                حظر
                            </button>
                        <?php elseif ($tourist['ban'] === 'banned' || $tourist['ban'] === 'temporary'): ?>
                            <button name="ban" value="unbanned" type="submit">
                                رفع الحظر
                            </button>
                        <?php endif; ?>
                    </form>


                </div>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>
</body>

</html>

