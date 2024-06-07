<?php
// Start session to maintain user's session data
session_start();

if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../validate.php";
include "../connection.php";

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

    // Check if name already exists in database
    $name_query = $con->prepare("SELECT name FROM city WHERE name=?");
    $name_query->execute([$name]);
    $cities_names = $name_query->fetchAll();
    $name_count = $name_query->rowCount();

    // If Conditions
    if (empty($name)) {
        $nameError = 'الرجاء أدخال اسم المدينة';
    } elseif ($name_count > 0) {
        foreach ($cities_names as $city_name) {
            if ($city_name['name'] == $name) {
                $nameError = 'اسم المدينة هذا موجود مسبقا';
            }
        }
    } elseif (empty($day)) {
        $dayError = 'الرجاء أدخال تاريخ اليوم';
    } elseif (empty($weather)) {
        $weatherError = 'الرجاء أدخال الطقس';
    } else {
        // If all validations pass, insert new supervisor into the database
        $stmt = $con->prepare("INSERT INTO city(name, day, weather) VALUES (?, ?, ?)");
        $stmt->execute([$name, $day, $weather]);
        $successMsg = 'تم إضافة مدينة جديد بنجاح';
        header("Location: show_cities.php?success_message=" . urlencode($successMsg));
        exit;
    }
}
?>


<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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
    إضافة مدينة جديد
</h1>


<form method="post"
      action="<?php
      echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">

    <label for="name">اسم المدينة</label>
    <input type="text" id="name" name="name" value="<?php
    echo $name ?>"/>
    <span class="error"> <?php
        echo $nameError ?></span>

    <br><br>

    <label for="day">تاريخ اليوم</label>
    <input type="date" id="day" name="day" value="<?php
    echo $day ?>"/>
    <span class="error"> <?php
        echo $dayError ?></span>

    <br><br>

    <label for="weather">الطقس</label>
    <input type="text" id="weather" name="weather"/>
    <span class="error"> <?php
        echo $weatherError ?></span>

    <br><br>

    <button type="submit" name="addSupervisor">
        إضافة
    </button>

</form>

</body>
</html>
