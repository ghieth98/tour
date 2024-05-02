<?php

// Start the session to allow session variables usage
session_start();

// Include necessary files for validation and database connection
include "connection.php"; // Assuming this file contains the database connection logic
include "validate.php"; // Assuming this file contains validation functions

if (isset($_SESSION['tourist_id'])) {
    $tourist_id = $_SESSION['tourist_id'];
}

if (isset($_SESSION['supervisor_id'])) {
    $supervisor_id = $_SESSION['supervisor_id'];
}

if (isset($_SESSION['administrator_id'])) {
    $administrator_id = $_SESSION['administrator_id'];
}


$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval($_GET['destination_id']) : 0;

// Fetch destination details from the database
$query = $con->prepare("
    SELECT  d.destination_id,d.start_date, d.end_date, d.name, d.description, d.phone_number, r.stars, f.tourist_id
    FROM destination AS d 
    LEFT JOIN tours.favorite f on d.destination_id = f.destination_id
    LEFT JOIN tours.rate r on d.destination_id = r.destination_id
    WHERE d.destination_id = ?
");
$query->execute([$destination_id]);
$destination = $query->fetch();

// Fetch images associated with the destination from the database
$imageQuery = $con->prepare("SELECT * FROM destination_images WHERE destination_id=?");
$imageQuery->execute([$destination_id]);
$images = $imageQuery->fetchAll();

// Fetch reviews for the destination from the database
$review_query = $con->prepare("SELECT * FROM review JOIN tours.tourist t on t.tourist_id = review.tourist_id WHERE destination_id =? ");
$review_query->execute([$destination_id]);
$reviews = $review_query->fetchAll();
$review_count = $review_query->rowCount();

// Check if there's a success message passed via GET parameter, if not, set it to an empty string
$successMsg = $_GET['success_message'] ?? '';

// Handle form submission for adding a review
$body = $body_error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = validate($_POST['body']);

    if (empty($body)) {
        $body_error = 'برجاء أدخال نص التعليق'; // Please enter the review text
    } else {
        // Insert the review into the database
        $stmt = $con->prepare("INSERT INTO review (body, tourist_id, destination_id, date) VALUES (?,?,?,NOW())");
        $stmt->execute([$body, $tourist_id, $destination_id]);
        $successMsg = 'تم إضافة التعليق بنجاح'; // Review added successfully
        // Redirect to the destination page with success message
        header("Location:destination_info.php?destination_id=".$destination_id."&success_message=".urlencode
            ($successMsg));
        exit; // Exit to prevent further execution after redirection
    }
}
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

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet">
    <link href="assets/css/jquery.fancybox.min.css" rel="stylesheet">
    <link href="fonts/icomoon/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="fonts/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="assets/css/daterangepicker.css" rel="stylesheet">
    <link href="assets/css/aos.css" rel="stylesheet">
    <link href="assets/css/style111243.css" rel="stylesheet">

    <title>توصية بالجولات</title>
    <style>
        .success {
            color: green;
        }
    </style>
</head>

<body>
<div class="site-mobile-menu site-navbar-target">
    <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close">
            <span class="icofont-close js-menu-toggle"></span>
        </div>
    </div>
    <div class="site-mobile-menu-body"></div>
</div>

<!--Start Navbar Section-->
<nav class="site-nav">
    <div class="container">
        <div class="site-navigation">
            <a class="logo m-0 float-right" href="index.php">توصية بالجولات <span class="text-primary"></span></a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-right site-menu float-left">
                <li class="active"><a href="index.php">الرئيسية</a></li>
                <li class="active"><a href="show_cities.php">المدن</a></li>
                <li><a href="destination.php">الوجهات</a></li>

                <?php if (isset($tourist_id)): ?>
                    <li><a href="logout.php">تسجيل الخروج</a></li>
                    <li><a class="fa-solid fa-user" href="tourist/dashboard.php"></a></li>
                <?php elseif(isset($supervisor_id)):  ?>
                    <li><a href="logout.php">تسجيل الخروج</a></li>
                    <li><a class="fa-solid fa-user" href="supervisor/dashboard.php"></a></li>
                <?php elseif(isset($administrator_id)):  ?>
                    <li><a href="logout.php">تسجيل الخروج</a></li>
                    <li><a class="fa-solid fa-user" href="administrator/dashboard.php"></a></li>
                <?php else:  ?>
                    <li><a href="signup.php">تسجيل جديد</a></li>
                    <li><a href="login.php">تسجيل الدخول</a></li>
                <?php endif;  ?>

            </ul>

            <a class="burger ml-auto float-left site-menu-toggle js-menu-toggle d-inline-block d-lg-none light"
               data-target="#main-navbar"
               data-toggle="collapse" href="index.php">
                <span></span>
            </a>

        </div>
    </div>
</nav>
<!--End navbar Section-->

<!-- Start Hero Section-->
<div class="hero hero-inner" style="
    background: url('assets/images/m-k-R1gC_gJaJ14-unsplash.jpg') center;
    background-size: cover;
    position: relative;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mx-auto text-center">
                <div class="intro-wrap">
                    <h1 class="mb-0">
                        <?php
                        echo $destination['name'] ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Hero Section-->

<!--Start Destination Info Section-->
<div class="untree_co-section text-right">

        <?php if ($successMsg):  ?>
            <div class="d-flex justify-content-center">
                <div class="alert alert-success w-25 text-center" role="alert">
                    <?php echo $successMsg ?>
                </div>
            </div>
        <?php endif;  ?>

    <div class="container mx-auto">

        <div class="mb-5">

            <div class="owl-single dots-absolute owl-carousel">
                <?php
                foreach ($images as $image): ?>

                    <img alt="image" class="img-fluid mx-auto d-block rounded " src="uploads/<?php
                    echo $image['image'] ?>" style="height: 600px; width: 1000px;">
                <?php
                endforeach; ?>
            </div>

        </div>
        <div class="col-md-12 mt-4 mb-5">
            <h2 class="mb-3"><?php
                echo $destination['name'] ?></h2>

            <p class="lh-lg" style="font-size: 17px">
                <?php
                echo $destination['description'] ?>
            </p>
            <div class="d-md-flex mt-5 mb-5">
                <ul>
                    <li class="mb-1"> مواعيد العمل: من: <?php
                            echo date("H:i A", strtotime($destination['start_date']));
                            ?> إلى:
                            <?php  echo date("H:i A", strtotime($destination['end_date'])); ?> </li>
                    <li> رقم الهاتف: <?php
                        echo $destination['phone_number'] ?></li>

                </ul>

            </div>

            <!--Start Comment Section-->
            <div class="pt-5 mt-5">
                <h3 class="mb-5"><?php
                    echo $review_count ?> تعليقات</h3>

                <ul class="comment-list">
                    <?php
                    foreach ($reviews as $review): ?>
                        <li class="comment">

                            <div class="comment-body">
                                <!--Icons Section-->
                                <?php
                                if (isset($tourist_id)): ?>
                                    <div class="float-left d-flex">
                                        <?php if ($tourist_id === $review['tourist_id']):  ?>
                                        <span>
                                            <a class="mb-3" href="tourist/includes/edit_review.php?review_id=<?php echo
                                            $review['review_id'] ?>&destination_id=<?php echo $destination_id ?>"
                                               type="button">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                        </span>

                                        <form action="tourist/includes/delete_review.php?review_id=<?php
                                        echo $review['review_id'] ?>" method="post"
                                              onsubmit="return confirm('هل تريد حذف  هذه التعليق ؟');">
                                            <input type="hidden" name="destination_id" value="<?php
                                            echo $destination_id ?>">
                                            <button type="submit" class="mr-3"
                                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>

                                        <?php endif;  ?>

                                        <?php
                                        $stmt = $con->prepare("SELECT COUNT( `like`.like_id) AS like_count, `like`.* FROM `like` WHERE review_id=? AND type='liked'");
                                        $stmt->execute([$review['review_id']]);
                                        $likeCount = $stmt->rowCount();
                                        $likes = $stmt->fetch(PDO::FETCH_ASSOC);
                                        ?>

                                        <form action="tourist/includes/like_dislike.php?review_id=<?php
                                        echo
                                        $review['review_id'] ?>" method="post">
                                            <input type="hidden" name="action" value="like">
                                            <input type="hidden" name="type" value="review">
                                            <input type="hidden" name="destination_id"
                                                   value="<?php
                                                   echo $destination_id ?>">
                                            <?php
                                            $isLiked = $likes['like_count'];
                                            if ($isLiked > 0 ):  ?>
                                                <?php if ($tourist_id === $likes['tourist_id']): ?>
                                                <button type="submit"
                                                        style="background: none; border: none; padding:
                                                                    0; cursor: pointer; ">

                                                            <span class="mr-3">
                                                                <?php echo $likes['like_count'] ?>
                                                            <i class="fa-solid fa-thumbs-up liked"></i>
                                                            </span>
                                                </button>
                                                <?php else: ?>
                                                    <button type="submit"
                                                            style="background: none; border: none; padding:
                                                                    0; cursor: pointer; ">

                                                            <span class="mr-3">
                                                                <?php echo $likes['like_count'] ?>
                                                            <i class="fa-regular fa-thumbs-up "></i>
                                                            </span>
                                                    </button>
                                                <?php endif;  ?>
                                            <?php else:  ?>
                                                <button type="submit"
                                                        style="background: none; border: none; padding: 0; cursor: pointer;">
                                                            <span class="mr-3">
                                                                <?php echo $likes['like_count'] ?>
                                                            <i class="fa-regular
                                                            fa-thumbs-up"></i>
                                                            </span>
                                                </button>
                                            <?php endif;  ?>
                                        </form>


                                        <form action="tourist/includes/add_report.php" method="post">
                                            <input type="hidden" id="review_id" name="review_id" value="<?php echo
                                            $review['review_id'];
                                            ?>">
                                            <input type="hidden" id="type" name="type" value="review">
                                            <input type="hidden" id="destination_id" name="destination_id"
                                                   value="<?php echo $destination_id ?>">
                                            <button type="submit"
                                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                                          <span class="mr-3">
                                            <i class="fa-regular fa-flag"></i>
                                            </span>
                                            </button>

                                        </form>


                                    </div>
                                <?php
                                endif; ?>
                                <!--End Icons Section-->

                                <h5>
                                    <?php
                                    echo $review['name'] ?>
                                </h5>

                                <p>
                                    <?php
                                    echo $review['body'];
                                    ?></p>
                                <!-- Button trigger modal -->
                                <?php
                                if (isset($tourist_id) ): ?>
                                    <a class="mb-3" href="tourist/includes/add_reply.php?review_id=<?php echo $review['review_id']
                                    ?>&destination_id=<?php echo $destination_id ?>"
                                       type="button">
                                        <i class="fa-solid fa-reply"></i>
                                        الرد على التعليق
                                    </a>
                                <?php
                                endif; ?>
                            </div>


                            <ul class="children mb-2">
                                <?php
                                $review_id = $review['review_id'];
                                $reply_query = $con->prepare("SELECT * FROM reply JOIN tours.tourist t on reply.tourist_id = t.tourist_id  WHERE review_id=?");
                                $reply_query->execute([$review_id]);
                                $replies = $reply_query->fetchAll();
                                foreach ($replies as $reply): ?>

                                        <li class="comment">
                                            <div class="vcard bio">
                                            </div>
                                            <div class="comment-body">
                                                <h5> <?php
                                                    echo $reply['name'] ?>
                                                </h5>

                                                <p>
                                                    <?php
                                                    echo $reply['body'] ?>
                                                </p>

                                                <div class="d-flex">
                                                    <?php if (isset($tourist_id) === $reply['tourist_id']): ?>
                                                    <span>
                                                        <a class="mb-3" href="tourist/includes/edit_reply.php?reply_id=<?php
                                                        echo $reply['reply_id'] ?>&destination_id=<?php echo  $destination_id?>"
                                                           type="button">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                        </a>
                                                    </span>

                                                    <form action="tourist/includes/delete_reply.php?reply_id=<?php
                                                    echo
                                                    $reply['reply_id'] ?>"
                                                          method="post"
                                                          onsubmit="return confirm('هل تريد حذف هذا الرد ؟');">
                                                        <input type="hidden" name="review_id"
                                                               value="<?php
                                                               echo $reply['review_id'] ?>">
                                                        <input type="hidden" name="destination_id"
                                                               value="<?php
                                                               echo $destination_id ?>">
                                                        <button type="submit"
                                                                style="background: none; border: none; padding: 0; cursor: pointer;">
                                                            <span class="mr-3">
                                                            <i class="fa-solid fa-trash"></i>
                                                            </span>
                                                        </button>
                                                    </form>
                                                    <?php endif;  ?>
                                                    <?php
                                                    $likeQuery = $con->prepare("SELECT COUNT( `like`.like_id)AS like_count, `like`.tourist_id  FROM `like` WHERE reply_id=? AND type='liked'");
                                                    $likeQuery->execute([$reply['reply_id']]);
                                                    $likeCount = $likeQuery->rowCount();
                                                    $likes = $likeQuery->fetch(PDO::FETCH_ASSOC);
                                                    ?>


                                                    <form action="tourist/includes/like_dislike.php?reply_id=<?php
                                                    echo
                                                    $reply['reply_id'] ?>"
                                                          method="post">
                                                        <input type="hidden" name="action" value="like">
                                                        <input type="hidden" name="type" value="reply">
                                                        <input type="hidden" name="review_id"
                                                               value="<?php
                                                               echo $reply['review_id'] ?>">
                                                        <input type="hidden" name="destination_id"
                                                               value="<?php
                                                               echo $destination_id ?>">
                                                        <?php
                                                        $isLiked = $likes['like_count'];

                                                        if ($isLiked > 0 ):  ?>
                                                            <?php if (isset($tourist_id) === $likes['tourist_id']): ?>
                                                                <button type="submit"
                                                                        style="background: none; border: none; padding:
                                                                    0; cursor: pointer; ">

                                                            <span class="mr-3">
                                                                <?php echo $likes['like_count'] ?>
                                                            <i class="fa-solid fa-thumbs-up liked"></i>
                                                            </span>
                                                                </button>
                                                            <?php else: ?>
                                                                <button type="submit"
                                                                        style="background: none; border: none; padding:
                                                                    0; cursor: pointer; ">

                                                            <span class="mr-3">
                                                                <?php echo $likes['like_count'] ?>
                                                            <i class="fa-regular fa-thumbs-up "></i>
                                                            </span>
                                                                </button>
                                                            <?php endif;  ?>
                                                        <?php else:  ?>
                                                            <button type="submit"
                                                                    style="background: none; border: none; padding: 0; cursor: pointer;">
                                                            <span class="mr-3">
                                                                <?php echo $likes['like_count'] ?>
                                                            <i class="fa-regular
                                                            fa-thumbs-up"></i>
                                                            </span>
                                                            </button>
                                                        <?php endif;  ?>
                                                    </form>

                                                    <form action="tourist/includes/add_report.php" method="post">
                                                        <input type="hidden" id="reply_id" name="reply_id" value="<?php
                                                        echo
                                                        $reply['reply_id'];
                                                        ?>">
                                                        <input type="hidden" id="type" name="type" value="reply">
                                                        <input type="hidden" id="destination_id" name="destination_id"
                                                               value="<?php echo $destination_id ?>">
                                                        <button type="submit"
                                                                style="background: none; border: none; padding: 0; cursor: pointer;">
                                                          <span class="mr-3">
                                                            <i class="fa-regular fa-flag"></i>
                                                          </span>
                                                        </button>

                                                    </form>

                                                </div>

                                            </div>
                                        </li>

                                <?php
                                endforeach; ?>
                            </ul>

                        </li>
                    <?php
                    endforeach; ?>

                </ul>
                <!-- END comment-list -->
                <!-- Review Form Section -->
                <?php
                if (isset($tourist_id)): ?>
                    <div class=" pt-5">
                        <h3 class="mb-5">اترك تعليقك هنا</h3>

                        <form action="<?php
                        echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post" class="p-5 bg-light">

                            <div class="form-group">
                                <label for="body"></label>
                                <textarea class="form-control" cols="30" id="body" name="body" rows="4"></textarea>
                                <span><?php
                                    echo $body_error ?></span>
                            </div>
                            <div class="form-group ">
                                <input class="btn py-3 px-4 btn-primary" type="submit" value="اضف تعليق">
                            </div>

                        </form>



                    </div>
                <div class="pr-5 pb-3 bg-light">
                    <form action="tourist/includes/add_rating.php?destination_id=<?php
                    echo $destination_id ?>" method="post">
                        <div class="form-group  w-50">
                            <div class="stars ">

                                <input class="star star-5" id="star-5" name="star" type="radio" value="5"/>
                                <label class="star star-5" for="star-5"></label>

                                <input class="star star-4" id="star-4" name="star" type="radio" value="4"/>

                                <label class="star star-4" for="star-4"></label>

                                <input class="star star-3" id="star-3" name="star" type="radio" value="3"/>

                                <label class="star star-3" for="star-3"></label>

                                <input class="star star-2" id="star-2" name="star" type="radio" value="2"/>

                                <label class="star star-2" for="star-2"></label>

                                <input class="star star-1" id="star-1" name="star" type="radio" value="1"/>

                                <label class="star star-1" for="star-1"></label>

                            </div>
                            <div class="form-group ">
                                <input class="btn py-3 px-4 btn-primary " type="submit" value="اضف التقييم">
                            </div>
                        </div>


                    </form>
                </div>

                <?php
                endif; ?>
            </div>


        </div>
    </div>
</div>
<!--End Destination Section-->



<!--Start Footer Section-->
<div class="site-footer">
    <div class="inner first">
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

<script src="assets/js/jquery-3.4.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/jquery.animateNumber.min.js"></script>
<script src="assets/js/jquery.waypoints.min.js"></script>
<script src="assets/js/jquery.fancybox.min.js"></script>
<script src="assets/js/aos.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/daterangepicker.js"></script>

<script src="assets/js/typed.js"></script>

<script src="assets/js/custom.js"></script>

</body>

</html>
