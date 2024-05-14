<?php

include "../../connection.php";
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
$name = $_POST['name'];
$stmt = $con->prepare("
   SELECT d.*, r.stars, di.image AS destination_image, C.name AS city_name,       
    COUNT(DISTINCT f.favorite_id) AS favorite_count 
    FROM destination AS d 
    JOIN (
        SELECT destination_id, MIN(destination_image_id) AS first_image_id 
        FROM tours.destination_images 
        GROUP BY destination_id
    ) AS first_images ON d.destination_id = first_images.destination_id 
    JOIN tours.destination_images AS di ON first_images.first_image_id = di.destination_image_id
    LEFT JOIN tours.favorite f on d.destination_id = f.destination_id
    LEFT JOIN tours.rate r on d.destination_id = r.destination_id
    LEFT JOIN tours.city c on c.city_id = d.city_id
    WHERE d.name LIKE '$name%'
   GROUP BY d.destination_id
");

$query = $stmt->execute();
$results_count = $stmt->rowCount();
$data = '';


if ($results_count > 0) {
    while ($destination = $stmt->fetch()) {
        $data .= '  <div class="col-lg-3 rounded col-md-6 mb-3 ">
<div class="card h-100 shadow">
                    <a href="destination_info.php?destination_id='.$destination['destination_id'].'">
                        <img alt="صورة الوجهة" class="card-img-top" style="height: 300px"
                             src="uploads/'.$destination['destination_image'].'">
                    </a>
                    <div class="card-body">
                        <a href="destination_info.php?destination_id='.$destination['destination_id'].'">
                            <h5 class="card-title">'.$destination['name'].'</h5>
                        </a>
                        <p class="card-text">'.substr($destination['description'], 0, 300).'...</p>
                        <p class="text-muted"> أوقات العمل : من: '.date("H:i A", strtotime($destination['start_date'])).' إلى: '.date("H:i A", strtotime($destination['end_date'])).' </p>
                        <p class="text-muted"> اسم المدينة : '.$destination['city_name'].' </p>
                        <p class="text-muted">رقم الهاتف: '.$destination['phone_number'].'</p>
                        
                        <form method="post" action="add_favorite.php?destination_id='.$destination['destination_id'].'">
                            <input type="hidden" name="city_id" value="'.$destination['city_id'].'">';

        $favorite = $destination['favorite_count'] > 0;
        if (isset($tourist_id)) {
            if ($favorite) {
                $data .= '<button style="background: none; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-solid fa-xl fa-heart pl-3 favorite" type="submit"></i>
                          </button>';
            } else {
                $data .= '<button style="background: none; border: none; padding: 0; cursor: pointer;">
                            <i class="fa-regular fa-xl fa-heart pl-3 " style="cursor: pointer;" type="submit"></i>
                          </button>';
            }
        }

        $data .= '<i class="fa-solid fa-star" style="color: #f3f31c"></i> '.$destination['stars'].'
                  </form>
                </div>
                </div>
              </div>';
    }
} else {
    $data .= '<h2 class="text-center">لم يتم العثور علي وجهات</h2>';
}
echo $data;

