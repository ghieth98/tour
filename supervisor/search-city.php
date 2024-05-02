<?php

include "../connection.php";
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
$name = $_POST['name'];
$stmt = $con->prepare("
    SELECT * FROM city 
    WHERE name LIKE '$name%';
");

$query = $stmt->execute();
$results_count = $stmt->rowCount();
$data = '';

if ($results_count > 0) {
    while ($city = $stmt->fetch()) {
        $data .= '<tr>
    <td>
                    <img src="../uploads/'. $city['city_image'] .'"
                         alt="city image"
                         style="height: 80px">
                </td>
    <td>
        <a href="show_city_destinations.php?city_id=' . $city['city_id'] . '">'
            . $city['name'] . '
        </a>
    </td>
            <td>
                    <p>
                        '. substr($city['city_description'], 0, 300) . '...' .'
                        <span class="more" style="display: none;">
                            '. substr($city['city_description'], 300) .'
                        </span>
                        <a class="show-more link-light" onclick="showMore(this)">عرض المزيد </a>
                    </p>
                </td>
    <td>
        <div style="display: inline;">
            <a class="px-4 btn py-1 btn-primary" href="edit_city.php?city_id=' . $city['city_id'] . '">
                تعديل 
            </a>
            <form action="delete_city.php?city_id=' . $city['city_id'] . '" method="post" onsubmit="return confirm(\'هل تريد حذف هذه المدينة ؟\');" style="display: inline;">
                <button class="px-4 btn py-1 btn-primary" type="submit">حذف</button>
            </form>
        </div>
    </td>
</tr>';

    }
} else {
    $data .= '<tr>
            <td> </td>
            <td> لا يوجد مدينة بهذا الاسم</td>
            <td>  </td>
            <td>  </td>
        </tr>';
}
echo $data;
