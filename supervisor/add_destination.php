<?php

// Start session to maintain user's session data
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../validate.php";
include "../connection.php";

// Get supervisor ID from session
$supervisor_id = $_SESSION['supervisor_id'];

$query = $con->prepare("SELECT * FROM city");
$query->execute();
$cities = $query->fetchAll();

// Initialize variables for form fields and error messages
$name = $description = $workingHours = $phoneNumber = $days = $month =
$budget = $city = $range = $age = $needs = $personality = '';

$nameError = $descriptionError = $workingHoursError = $phoneNumberError =
$daysError = $monthError = $rangeError = $budgetError = $cityError = $ageError = $needsError =
$personalityError = '';

$imageError = ''; // Initialize imageError here
$successMsg = '';
// Initialize an empty array to store extensions
$extensions = [];
// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Start the transaction
        $con->beginTransaction();

        // Validate form inputs
        $name = validate($_POST['name']);
        $description = validate($_POST['description']);
        $workingHours = validate($_POST['working_hours']);
        $phoneNumber = validate($_POST['phone_number']);
        $city = validate($_POST['city']);
        $range = validate($_POST['range']);

        // Check for empty fields and set appropriate error messages
        if (empty($name)) {
            $nameError = 'الرجاء أدخال اسم الوجهة';
        } elseif (empty($description)) {
            $descriptionError = 'الرجاء أدخال وصف الوجهة';
        } elseif (empty($workingHours)) {
            $workingHoursError = 'الرجاء أدخال أوقات العمل';
        } elseif (empty($range)) {
            $rangeError = 'الرجاء أدخال أوقات العمل';
        } elseif (empty($phoneNumber)) {
            $phoneNumberError = 'الرجاء أدخال رقم الهاتف';
        } elseif (!preg_match('/^05\d{8}$/', $phoneNumber)) {
            $phoneNumberError = 'رقم الهاتف غير مسموح حيث يجب ان يبدأ ب 05 و أن يتكون من 10 أرقام';
        } else {
            // Prepare and execute SQL statement to insert new destination into the database
            $stmt = $con->prepare(
                "INSERT INTO destination (name, description, working_hours, `range`, phone_number, supervisor_id, city_id) VALUES (?, ?, ?,?, ?, ?,?)"
            );
            $stmt->execute([$name, $description, $workingHours, $range, $phoneNumber, $supervisor_id, $city]);

            // Get the last inserted destination ID
            $destination_id = $con->lastInsertId();

            if (empty($days)) {
                $daysError = 'الرجاء أدخال عدد الأيام';
            } elseif (empty($month)) {
                $monthError = 'الرجاء أدخال الشهر';
            } elseif (empty($budget)) {
                $budgetError = 'الرجاء أدخال الميزانية';
            } elseif (empty($city)) {
                $cityError = 'الرجاء اختيار المدينة';
            } elseif ($age) {
                $ageError = 'الرجاء أدخال العمر';
            } elseif ($needs) {
                $needsError = 'الرداء أدخال الاحتياجات الخاصة';
            } elseif ($personality) {
                $personalityError = 'الرجاء أدخال الشخصية';
            } else {
                $stmt = $con->prepare(
                    "INSERT INTO attraction (days, month, budget, age, needs, personality, destination_id) VALUES (?,?,?,?,?,?,?)"
                );
                $stmt->execute([
                    $days,
                    $month,
                    $budget,
                    $age,
                    $needs,
                    $personality,
                    $destination_id
                ]);
            }


            // Handle multiple image uploads
            $imageNameArray = $_FILES['images']['name'];

            // Loop through each file name in $imageNameArray
            foreach ($imageNameArray as $imageName) {
                // Get the extension of each file name and add it to the $extensions array
                $extensions[] = pathinfo($imageName, PATHINFO_EXTENSION);
            }

            // Check if all extensions are the same
            $extension = (count(array_unique($extensions)) === 1) ? $extensions[0] : null;

            if (empty($imageNameArray[0])) {
                $imageError = 'الرجاء أدخال الصورة';
            } elseif (!in_array($extension, ['jpeg', 'png', 'svg', 'jpg'])) {
                $imageError = 'صيغة الملف غير مدعومة';
            } else {
                $total_images = count($imageNameArray);
                // Loop through each image
                for ($i = 0; $i < $total_images; $i++) {
                    // Get the temporary file path
                    $tmpFilePath = $_FILES['images']['tmp_name'][$i];

                    // Check if file exists
                    if ($tmpFilePath != '') {
                        // Generate unique filename
                        $newFilePath = "../uploads/" . uniqid() . '_' . $imageNameArray[$i];

                        // Move the file to the uploads directory
                        // Inside the loop for processing each uploaded image
                        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                            // Insert image details into the database
                            $stmt = $con->prepare(
                                "INSERT INTO destination_images (image, destination_id, date) VALUES (?, ?, NOW())"
                            );
                            $stmt->execute([$newFilePath, $destination_id]);
                            $successMsg = 'تم إضافة وجهة جديدة بنجاح';
                        } else {
                            // If file upload failed, set upload success to false
                            $imageError = 'تم إضافة الوجهة الجديدة، ولكن حدث خطأ أثناء رفع الصور. يرجى المحاولة مرة أخرى.';
                        }
                    }
                }


                // Commit the transaction
                $con->commit();

                // Redirect to show_destinations.php with success message
                header(
                    "Location:show_destinations.php?city_id=" . $city . "&success_message=" .
                    urlencode($successMsg)
                );
                exit;
            }
        }
    } catch (PDOException $e) {
        // Rollback the transaction in case of any errors
        $con->rollback();
        echo "Error: " . $e->getMessage();
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
    </style>

</head>
<body>

<h1>
    إضافة وجهة جديد
</h1>

<form method="post"
      action="<?php
      echo htmlspecialchars($_SERVER['PHP_SELF']) ?>"
      enctype="multipart/form-data">

    <label for="name">اسم الوجهة</label>
    <input type="text" id="name" name="name" value="<?php
    echo $name ?>"/>
    <span class="error"> <?php
        echo $nameError ?></span>
    <br><br>

    <label for="description">وصف الوجهة</label>
    <textarea name="description"
              id="description"><?php
        echo $description ?></textarea>
    <span class="error"> <?php
        echo $descriptionError ?></span>
    <br><br>

    <label for="working_hours">أوقات العمل</label>
    <input type="text" id="working_hours" name="working_hours"
           value="<?php
           echo $workingHours ?>"/>
    <span class="error"> <?php
        echo $workingHoursError ?></span>
    <br><br>
    <label for="range">ساعات العمل</label>
    <input type="text" id="range" name="range"
           value="<?php
           echo $range ?>"/>
    <span class="error"> <?php
        echo $rangeError ?></span>
    <br><br>

    <label for="phone_number">رقم الهاتف</label>
    <input type="tel" id="phone_number" name="phone_number"
           value="<?php
           echo $phoneNumber ?>"/>
    <span class="error"> <?php
        echo $phoneNumberError ?></span>
    <br><br>


    <label for="days">عدد أيام الجولة:</label>
    <select id="days" name="days">
        <option value="1-3">1 - 3 أيام</option>
        <option value="4-7">4 - 7 أيام</option>
        <option value="8+">8 أو أكثر أيام</option>
    </select><br><br>
    <span class="error"> <?php
        echo $daysError ?></span>


    <label for="month">شهر الجولة:</label>
    <input type="month" id="month" name="month">
    <span class="error"> <?php
        echo $monthError ?></span>
    <br><br>

    <label for="budget">ميزانية السائح:</label>
    <select id="budget" name="budget">
        <option value="low">ميزانية منخفضة</option>
        <option value="medium">ميزانية متوسطة</option>
        <option value="high">ميزانية عالية</option>
    </select>
    <span class="error"> <?php
        echo $budgetError ?></span>
    <br><br>

    <label for="city">المدينة</label>
    <select id="city" name="city">
        <?php
        foreach ($cities as $city): ?>
            <option value="
        <?php
            echo $city['city_id'] ?>>">
                <?php
                echo $city['name'] ?>
            </option>
        <?php
        endforeach; ?>
    </select>
    <span class="error"> <?php
        echo $cityError ?></span>
    <br><br>


    <label for="age">الفئة العمرية للسائح:</label>
    <select id="age" name="age">
        <option value="child">طفل</option>
        <option value="young">شاب</option>
        <option value="adult">بالغ</option>
        <option value="elderly">كبير فى السن</option>
    </select>
    <span class="error"> <?php
        echo $ageError ?></span>
    <br><br>

    <label for="needs">احتياجات خاصة:</label>
    <select id="needs" name="needs">
        <option value="yes">نعم</option>
        <option value="no">لا</option>
    </select>
    <span class="error"> <?php
        echo $needsError ?></span>
    <br><br>

    <label for="personality">نوع الوجهة</label>
    <select id="personality" name="personality">
        <option value="night"> للأنشطة الليلية</option>
        <option value="morning"> للأنشطة الصباحية</option>
        <option value="adventure"> للأنشطة المغامرة</option>
        <option value="calm"> للأنشطة الهادئة</option>
        <option value="open">للأنشطة منفتحة</option>
        <option value="closed">للأنشطة منغلقة</option>
    </select>
    <span class="error"> <?php
        echo $personalityError ?></span>
    <br><br>


    <label for="images">الصور</label>
    <input type="file" id="images" name="images[]" multiple/>
    <span class="error"> <?php
        echo $imageError ?></span>
    <br><br>

    <button type="submit" name="addDestination">
        إضافة
    </button>

</form>

</body>
</html>
