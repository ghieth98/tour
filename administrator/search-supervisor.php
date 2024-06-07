<?php
include "../connection.php";
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
$name = $_POST['name'];
$stmt = $con->prepare("
    SELECT * FROM supervisor 
    WHERE name LIKE '$name%';
");

$query = $stmt->execute();
$results_count = $stmt->rowCount();
$data = '';

if ($results_count > 0) {
    while ($supervisor = $stmt->fetch()) {
        $data .= '<tr>
                <td>' . $supervisor['supervisor_id'] . '</td>
                <td>' . $supervisor['name'] . '</td>
                <td>' . $supervisor['email'] . '</td>
                <td>
                    <div style="display: inline;">
                        <a href="edit_supervisor.php?supervisor_id=' . $supervisor['supervisor_id'] . '" type="button">تعديل</a>
                        <form action="delete_supervisor.php" method="post" style="display: inline;">
                            <input name="supervisor_id" type="hidden" value="' . $supervisor['supervisor_id'] . '">
                            <button onclick="return confirm(\'هل تريد حذف هذا المشرف؟\');" type="submit">حذف</button>
                        </form>
                        <form action="ban_supervisor.php" method="post" style="display: inline;">
                            <input name="supervisor_id" type="hidden" value="' . $supervisor['supervisor_id'] . '">
                            <button onclick="return confirm(\'هل تريد حظر هذا المشرف؟\');" type="submit">حظر</button>
                        </form>
                        <form action="unban_supervisor.php" method="post" style="display: inline;">
                            <input name="supervisor_id" type="hidden" value="' . $supervisor['supervisor_id'] . '">
                            <button onclick="return confirm(\'هل تريد رفع الحظر عن هذا المشرف؟\');" type="submit">رفع الحظر</button>
                        </form>
                    </div>
                </td>
            </tr>';;
    }
} else {
    $data .= '<tr>
            <td> </td>
            <td>  </td>
            <td> </td>
            <td>  </td>
            <td> لا يوجد مشرف بهذا الاسم</td>
            <td>  </td>
            <td> </td>
            <td>  </td>
        </tr>';
}
echo $data;
