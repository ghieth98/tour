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
            <td>' . $supervisor['name'] . '</td>
            <td>' . $supervisor['email'] . '</td>
            <td>
                <div style="display: inline;">
                    <form action="ban_supervisor.php?supervisor_id=' . $supervisor['supervisor_id'] . '" method="post" onsubmit="' .
            (($supervisor['ban'] === 'unbanned') ? 'return confirm(\'هل تريد حظر هذا المشرف؟\');' : 'return confirm(\'هل تريد رفع الحظر عن هذا المشرف؟\');') . '"
                    style="display: inline;">
                        ' . (($supervisor['ban'] === 'unbanned') ? '
                        <select name="ban" id="ban" class="form-select form-select-sm mb-3">
                            <option name="banned" value="banned">حظر</option>
                            <option name="temporary" value="temporary">حظر مؤقت</option>
                        </select>
                        <button type="submit" class="px-4 btn py-1 btn-primary">حظر</button>' : '
                        <button name="ban" value="unbanned" class="px-4 btn py-1 btn-primary" type="submit">رفع الحظر</button>') . '
                    </form>
                </div>
            </td>
        </tr>';

    }
} else {
    $data .= '<tr>
            <td> </td>
            <td> لا يوجد مشرف بهذا الاسم</td>
            <td>  </td>
            <td>  </td>
        </tr>';
}
echo $data;
