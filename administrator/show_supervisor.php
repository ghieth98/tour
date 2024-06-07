<?php
// Start the session to allow session variables usage
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../connection.php";

// Fetch all supervisors from the database
$supervisors = $con->query("SELECT * FROM supervisor");

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

    <title>تصوية بالجولات</title>

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

<h1>
    عرض بيانات المشرف
</h1>

<span class="success"><?php echo $successMsg ?> </span>

<br><br>

<a href="dashboard.php">الصفحة الشخصية</a>

<br><br>

<a href="add_supervisor.php">إضافة مسؤول جديد</a>

<br><br><br>

<div>
    <label for="search">ابحث</label>
    <input type="text" id="search" placeholder="ابحث هنا...">
</div>
<br> <br>
<table>
    <thead>
    <tr>
        <th>الرقم التعريفي</th>
        <th>الاسم</th>
        <th>البريد الإلكتروني</th>
        <th>ألإجراءات</th>
    </tr>
    </thead>

    <tbody id="showSearch">
    <?php foreach ($supervisors as $supervisor): ?>
        <tr>
            <td> <?php echo $supervisor['supervisor_id'] ?> </td>

            <td> <?php echo $supervisor['name'] ?> </td>

            <td> <?php echo $supervisor['email'] ?> </td>

            <td>
                <div style="display: inline;">

                    <a href="edit_supervisor.php?supervisor_id=<?php echo $supervisor['supervisor_id'] ?>"
                       type="button">
                        تعديل
                    </a>

                    <form action="delete_supervisor.php?supervisor_id=<?php echo $supervisor['supervisor_id'] ?>"
                          method="post" onsubmit="return confirm('هل تريد حذف هذا المشرف ؟');"
                          style="display: inline;">
                        <button type="submit">
                            حذف
                        </button>
                    </form>

                    <form action="ban_supervisor.php?supervisor_id=<?php echo $supervisor['supervisor_id'] ?>"
                          method="post" onsubmit=" <?php if ($supervisor['ban'] === 'unbanned')  : ?>
                            return confirm('هل تريد حظر هذا المشرف ؟');
                    <?php elseif ($supervisor['ban'] === 'banned' || $supervisor['ban'] === 'temporary'): ?>
                            return confirm('هل تريد رفع الحظر عن هذا المشرف؟')
                    <?php endif; ?>"
                          style="display: inline;">
                        <?php if ($supervisor['ban'] === 'unbanned') : ?>
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
                        <?php elseif ($supervisor['ban'] === 'banned' || $supervisor['ban'] === 'temporary'): ?>
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

<script>
  $(document).ready(function () {
    // Store the original HTML content of the table body
    const originalTableContent = $('#showSearch').html();

    $('#search').on('keyup', function () {
      let search = $(this).val().trim(); // Trim the search string to handle empty space
      if (search !== '') {
        $.ajax({
          method: 'POST',
          url: 'search-supervisor.php',
          data: { name: search },
          success: function (response) {
            $('#showSearch').html(response);
          }
        });
      } else {
        // If search is empty, display the original table content
        $('#showSearch').html(originalTableContent);
      }
    });

  });
</script>
</body>
</html>
