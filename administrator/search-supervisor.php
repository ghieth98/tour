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
                    <form action="ban_supervisor.php?supervisor_id=' . $supervisor['supervisor_id'] . '" method="post" onsubmit="';
        if ($supervisor['ban'] === 'unbanned') {
            $data .= 'return confirm(\'هل تريد حظر هذا المشرف ؟\');';
        } elseif ($supervisor['ban'] === 'banned' || $supervisor['ban'] === 'temporary') {
            $data .= 'return confirm(\'هل تريد رفع الحظر عن هذا المشرف؟\');';
        }
        $data .= '" style="display: inline;">';
        if ($supervisor['ban'] === 'unbanned') {
            $data .= '<button type="submit" name="ban" value="banned" class="px-4 btn py-1 btn-primary ">حظر</button>
                      <button type="submit" name="ban" value="temporary" class="px-4 btn py-1 btn-primary ">حظر مؤقت</button>';
        } elseif ($supervisor['ban'] === 'banned' || $supervisor['ban'] === 'temporary') {
            $data .= '<button name="ban" value="unbanned" class="px-4 btn py-1 btn-primary " type="submit">رفع الحظر</button>';
        }
        $data .= '</form>
                </div>
            </td>
        </tr>';
    }
} else {
    $data .= '<tr>
                <td></td>
                <td>لا يوجد مشرف بهذا الاسم</td>
                <td></td>
                <td></td>
            </tr>';
}

echo $data;

