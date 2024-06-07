<?php
// Start session to maintain user's session data
session_start();

if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}

// Include necessary files for validation and database connection
include "../validate.php";
include "../connection.php";

$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ?
    intval($_GET['city_id']) : 0;

$query = $con->prepare("SELECT * FROM city WHERE city_id=?");
$query->execute([$city_id]);
$city = $query->fetch();

// Initialize variables for form fields and error messages
$name = $day = $weather = '';
$nameError = $dayError = $weatherError = '';
$successMsg = '';

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate form inputs
    $name = validate($_POST['name']);
    $day = validate($_POST['day']);
    $weather = validate($_POST['weather']);

    // Validate name
    if (empty($name)) {
        $nameError = 'الرجاء أدخال اسم المدينة';
    } elseif (empty($day)) {
        $dayError = 'الرجاء أدخال تاريخ اليوم';
    } elseif (empty($weather)) {
        $weatherError = 'الرجاء أدخال الطقس';
    } else {
        // If all validations pass, update city in the database
        $stmt = $con->prepare("UPDATE city SET name=?, day=?, weather=? WHERE city_id=?");
        $stmt->execute([$name, $day, $weather, $city_id]);
        $successMsg = 'تم تعديل المدينة بنجاح';
        header("Location: show_cities.php?success_message=" . urlencode($successMsg));
        exit;
    }
}
?>


<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0,
           maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
    تعديل بيانات المدينة
</h1>


<form method="post"
      action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

    <label for="name">اسم المدينة</label>
    <input type="text" id="name" name="name"
           value="<?php echo $city['name'] ?>"/>
    <span class="error"> <?php echo $nameError ?></span>

    <br><br>

    <label for="day">تاريخ اليوم</label>
    <input type="date" id="day" name="day" value="<?php echo $city['day'] ?>"/>
    <span class="error"> <?php echo $dayError ?></span>

    <br><br>

    <label for="weather">الطقس</label>
    <input type="text" id="weather" name="weather"
           value="<?php echo $city['weather'] ?>"/>
    <span class="error"> <?php echo $weatherError ?></span>

    <br><br>

    <button type="submit" name="editCity">
        تعديل
    </button>

</form>

</body>
</html>
