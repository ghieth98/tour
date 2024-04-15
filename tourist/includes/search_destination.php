<?php

include "../../connection.php";
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
$name = $_POST['name'];
$stmt = $con->prepare("
    SELECT d.destination_id, d.name
    FROM destination AS d 
    WHERE d.name LIKE '$name%';
");

$query = $stmt->execute();
$results_count = $stmt->rowCount();
$data = '';

if ($results_count > 0) {
    while ($destination = $stmt->fetch()) {
        $data .= '<ul>
        <div class="text-right ">
         <a dir="rtl"
            href="destination_info.php?destination_id='.$destination['destination_id'].' "> '
            .$destination['name'].' </a>
            </div>
   
       
        </ul>';
    }
} else {
    $data .= '<tr>
            <td> </td>
            <td>  </td>
            <td> </td>
            <td>  </td>
            <td> لا يوجد وجهات بهذا الاسم</td>
            <td>  </td>
            <td> </td>
            <td>  </td>
        </tr>';
}
echo $data;