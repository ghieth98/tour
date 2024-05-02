<?php

// Start the session to access session variables
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}

// Include the database connection file
include "../connection.php";

// Include the validate function file if needed
include "../validate.php";

// Query api_records table for the url
$queryUrl = $con->prepare("SELECT * FROM api_records ORDER BY api_record_id DESC LIMIT 1");
$queryUrl->execute();
$apiRecord = $queryUrl->fetch(PDO::FETCH_ASSOC);
$url = $apiRecord['url'];


// Get the start and end dates from the form submission
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Convert the date strings to DateTime objects
$startDate = new DateTime($start_date);
$endDate = new DateTime($end_date);

// Calculate the difference in days
$dateDifference = $startDate->diff($endDate);

// Get the number of days from the DateInterval object
$days = $dateDifference->days;
$budget = validate($_POST['budget']);
$age = validate($_POST['age']);
$needs = validate($_POST['needs']);
$morningness = validate($_POST['morningness']);
$noiseness = validate($_POST['noiseness']);
$adventurousness = validate($_POST['adventurousness']);
$space = validate($_POST['space']);
$environment = validate($_POST['environment']);

// Initialize an empty array to hold the destination IDs from recommendations
$recommendedDestinations = [];
$user_id = $_SESSION['tourist_id'];
// Query the rates from the database
$query = $con->prepare("SELECT * FROM rate");
$query->execute();

$rates = $query->fetchAll(pdo::FETCH_ASSOC);

// Retrieve recommendations from API
// Initialize arrays to collect user IDs, item IDs, and rates
$user_ids = [];
$item_ids = [];
$rates_values = [];

// Collect the data from the rows into arrays
foreach ($rates as $rate) {
    $user_ids[] = $rate['tourist_id'];
    $item_ids[] = $rate['destination_id'];
    $rates_values[] = $rate['stars'];
}

// Prepare the JSON data according to the desired structure
$data = [
    'dataset' => [
        'user_id' => $user_ids,
        'item_id' => $item_ids,
        'rate' => $rates_values
    ],
    'id' => $user_id
];

// Convert the data to a JSON string
$json_data = json_encode($data);
// Python API Endpoint
// Prepare the request options
$requestOptions = array(
    'http' => array(
        'header' => 'Content-Type: application/json', // Set the content type to JSON
        'method' => 'POST', // Set the method to POST
        'content' => $json_data, // Set the content to the JSON data
    ),
);
// Create the HTTP context
$httpContext = stream_context_create($requestOptions);
// Make the POST request and retrieve the response
$response = file_get_contents($url.'/recommend/', false, $httpContext);

// Decode the JSON response
$recommendations = json_decode($response, true);


// Merge the recommendations into the array
$recommendedDestinations = array_merge($recommendedDestinations, $recommendations);
// Remove duplicate destination IDs
$recommendedDestinations = array_unique($recommendedDestinations);


$query = $con->prepare("
    SELECT
        d.*,
        di.image AS destination_image,
        c.name AS city_name,
        a.*,
        r.stars,
        COUNT(DISTINCT f.favorite_id) AS favorite_count
    FROM
        tours.destination AS d
    JOIN (
        SELECT destination_id, MIN(destination_image_id) AS first_image_id
        FROM tours.destination_images
        GROUP BY destination_id
    ) AS first_images ON d.destination_id = first_images.destination_id
    JOIN tours.destination_images AS di ON first_images.first_image_id = di.destination_image_id
    LEFT JOIN tours.favorite AS f ON d.destination_id = f.destination_id
    LEFT JOIN tours.rate AS r ON d.destination_id = r.destination_id
    LEFT JOIN tours.city AS c ON d.city_id = c.city_id
    JOIN tours.attraction AS a ON d.destination_id = a.destination_id
    WHERE
        a.days = ?
       AND a.budget = ?
        AND a.age = ?
        AND a.needs = ?
        AND a.morningness = ?
        AND a.noiseness = ?
        AND a.adventurousness = ?
        AND a.space = ?
       AND a.environment = ?
        AND a.destination_id IN ("
    .implode(",",
        $recommendedDestinations).")
    GROUP BY
        d.name;
");


// Execute the query with parameters
$query->execute(array(
    $days,
    $budget,
    $age,
    $needs,
    $morningness,
    $noiseness,
    $adventurousness,
    $space,
    $environment
));


// Fetch results as needed
$results = $query->fetchAll(PDO::FETCH_ASSOC);


$successMsg = $_GET['success_message'] ?? '';

$city_id = isset($_GET['city_id']) && is_numeric($_GET['city_id']) ?
    intval($_GET['city_id']) : 0;
?>

<!doctype html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600&family=El+Messiri:wght@400;500;600;700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&display=swap"
        rel="stylesheet">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/owl.carousel.min.css" rel="stylesheet">
    <link href="../assets/css/owl.theme.default.min.css" rel="stylesheet">
    <link href="../assets/css/jquery.fancybox.min.css" rel="stylesheet">
    <link href="../assets/fonts/icomoon/style.css" rel="stylesheet">
    <link href="../assets/fonts/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/date rangepicker.css" rel="stylesheet">
    <link href="../assets/css/aos.css" rel="stylesheet">
    <link href="../assets/css/style111243.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <title>توصية بالجولات</title>
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

<!--Start mobile toggle section-->
<div class="site-mobile-menu site-navbar-target">
    <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close">
            <span class="icofont-close js-menu-toggle"></span>
        </div>
    </div>
    <div class="site-mobile-menu-body"></div>
</div>
<!--End mobile toggle section-->

<!--Start Navbar Section-->
<nav class="site-nav">
    <div class="container">
        <div class="site-navigation">
            <a class="logo m-0 float-right" href="../index.php">توصية بالجولات <span class="text-primary"></span></a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left">
                <li class=""><a href="dashboard.php">الصفحة الرئيسية</a></li>
                <li class=""><a href="edit_profile.php">تعديل بيانات الملف الشخصي</a></li>
                <li class=""><a href="test.php">الاختبار</a></li>
                <li><a href="../logout.php">تسجيل الخروج</a></li>
            </ul>

            <a class="burger ml-auto float-left site-menu-toggle js-menu-toggle d-inline-block d-lg-none light"
               data-target="#main-navbar"
               data-toggle="collapse" href="../index.php">
                <span></span>
            </a>

        </div>
    </div>
</nav>
<!--End navbar Section-->

<!--Start Hero Section-->
<div class="hero hero-inner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">نتيجة الأختبار</h1>

                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<div class="untree_co-section">

    <div class="row row-cols-1 row-cols-md-3 g-4 p-5 text-right" data-aos="fade-up" data-aos-delay="500">

        <div class="col-lg-12 text-center">
            <?php
            if ($successMsg): ?>
                <div class="d-flex justify-content-center">
                    <div class="alert alert-success w-25 text-center" role="alert">
                        <?php
                        echo $successMsg ?>
                    </div>
                </div>
            <?php
            endif; ?>
        </div>
        <?php
        if (empty($results)): ?>
            <div class="col-lg-12 rounded mt-5 col-md-6 mb-3 ">
                <div
                    class="card justify-content-center d-flex text-center center-div bg-white mt-4 p-5 rounded  shadow">
                    <h5>لا يوجد نتائج مطابقة</h5>
                    <div class="card-body">
                        <a href="test.php" class="btn btn-info">
                            أعد الأختبار
                        </a>
                    </div>
                </div>
            </div>
        <?php
        else: ?>
            <?php
            foreach ($results as $result): ?>
                <!--Start Card-->
                <div class="col-lg-3 rounded col-md-6 mb-3 ">
                    <div class="card h-100 shadow">
                        <a href="../destination_info.php?destination_id=<?php
                        echo $result['destination_id'] ?>">
                            <img alt="صورة الوجهة" class="card-img-top" style="height: 300px"
                                 src="../uploads/<?php
                                 echo $result['destination_image'] ?>">
                        </a>
                        <div class="card-body">
                            <a href="../destination_info.php?destination_id=<?php
                            echo $result['destination_id'] ?>">
                                <h5 class="card-title"><?php
                                    echo $result['name'] ?></h5>
                            </a>
                            <p class="card-text"><?php
                                echo substr($result['description'], 0, 300) ?>...</p>
                            <p class="text-muted"> أوقات العمل : <?php
                                echo $result['working_hours'] ?> </p>
                            <p class="text-muted"> اسم المدينة : <?php
                                echo $result['city_name'] ?> </p>
                            <p class="text-muted">رقم الهاتف: <?php
                                echo $result['phone_number'] ?></p>


                            <form method="post" action="../add_favorite.php?destination_id=<?php
                            echo $result['destination_id'] ?>">

                                <input type="hidden" name="city_id" value="<?php
                                echo $result['city_id'] ?>">
                                <?php
                                $favorite = $result['favorite_count'] > 0 ?>
                                <?php
                                if (isset($tourist_id)) : ?>
                                    <?php
                                    if ($favorite): ?>
                                        <button style="background: none; border: none; padding: 0; cursor: pointer;">
                                            <i class="fa-solid fa-xl fa-heart pl-3 favorite"
                                               type="submit"></i>
                                        </button>
                                    <?php
                                    else: ?>
                                        <button style="background: none; border: none; padding: 0; cursor: pointer;">
                                            <i class="fa-regular fa-xl fa-heart pl-3 " style="cursor: pointer;"
                                               type="submit"></i>
                                        </button>
                                    <?php
                                    endif; ?>
                                <?php
                                endif; ?>
                                <i class="fa-solid fa-star " style="color: #f3f31c"></i> <?php
                                echo $result['stars'] ?>
                            </form>


                        </div>
                    </div>
                </div>
                <!--End Card-->
            <?php
            endforeach; ?>
        <?php
        endif; ?>
    </div>

</div>


<!--Start Footer Section-->
<div class="site-footer fixed-bottom">
    <div class="inner first ">
        <div class="inner dark">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-8  mb-md-0 mx-auto">
                        <p>
                            جميع الحقوق محفوظة للتوصية بالجولات @ 2024
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Footer Section-->


<div id="overlayer"></div>
<div class="loader">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<script src="../assets/js/jquery-3.4.1.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/owl.carousel.min.js"></script>
<script src="../assets/js/jquery.animateNumber.min.js"></script>
<script src="../assets/js/jquery.waypoints.min.js"></script>
<script src="../assets/js/jquery.fancybox.min.js"></script>
<script src="../assets/js/aos.js"></script>
<script src="../assets/js/moment.min.js"></script>
<script src="../assets/js/daterangepicker.js"></script>

<script src="../assets/js/typed.js"></script>
<script src="../assets/js/custom.js"></script>


</body>

</html>



