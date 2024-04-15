<?php
include "connection.php";

$current_date = date('Y-m-d-H-i-s');

$stmt = $con->prepare("SELECT tourist_id FROM tourist WHERE ban='temporary' AND ban_expiration_date <=? ");
$stmt->execute([$current_date]);
$expired = $stmt->fetchAll();

foreach ($expired as $tourist) {
    $tourist_id = $tourist['tourist_id'];
    $stmt = $con->prepare("UPDATE tourist SET ban='unbanned', ban_expiration_date=NULL WHERE tourist_id=? ");
    $stmt->execute([$tourist_id]);
}
echo 'تم تعديل بيانات الحظر بنجاح';
