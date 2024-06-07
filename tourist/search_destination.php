<?php
include "../connection.php";
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
$name = $_POST['name'];
$stmt = $con->prepare("
    SELECT d.destination_id, d.name, d.description, d.working_hours, d.phone_number, r.stars, di.image AS destination_image 
    FROM destination AS d 
    JOIN (
        SELECT destination_id, MIN(destination_image_id) AS first_image_id 
        FROM tours.destination_images 
        GROUP BY destination_id
    ) AS first_images ON d.destination_id = first_images.destination_id 
    JOIN tours.destination_images AS di ON first_images.first_image_id = di.destination_image_id
    LEFT JOIN tours.favorite f on d.destination_id = f.destination_id
    LEFT JOIN tours.rate r on d.destination_id = r.destination_id
    WHERE d.name LIKE '$name%';
");

$query = $stmt->execute();
$results_count = $stmt->rowCount();
$data = '';

if ($results_count > 0) {
    while ($destination = $stmt->fetch()) {
        $data .= '<tr>
            <td> ' . $destination['destination_id'] . '</td>
            <td> ' . $destination['name'] . ' </td>
            <td> ' . $destination['description'] . '</td>
            <td> ' . $destination['working_hours'] . ' </td>
            <td> ' . $destination['phone_number'] . '</td>
            <td> ' . $destination['stars'] . '.  </td>
            <td>
        <img src="../uploads/' . $destination['destination_image'] . ' " alt="destination image"
                     style="height: 80px">
            </td>

            <td>
                <div style="display: inline;">
                    <a href="show_destination.php?destination_id=' . $destination['destination_id'] . '"
                       type="button">عرض الوجهة</a>


                 <form action="add_favorite.php/destinaton_id=' . $destination['destination_id'] . '"></form>
                        <button type="submit">Add/Remove from Favorites</button>
                    </form>

                </div>

            </td>
        </tr>';
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