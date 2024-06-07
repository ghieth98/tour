<?php
// Start the session to allow session variables usage
session_start();
if (!(isset($_SESSION['email']))) {
    header('Location../login.php');
}
// Include necessary files for validation and database connection
include "../connection.php"; // Assuming this file contains the database connection logic
include "../validate.php"; // Assuming this file contains validation functions

// Retrieve tourist ID from session
$tourist_id = $_SESSION['tourist_id'];
$destination_id = isset($_GET['destination_id']) && is_numeric($_GET['destination_id']) ? intval($_GET['destination_id']) : 0;

// Fetch destination details from the database
$query = $con->prepare("
    SELECT  d.destination_id,d.`range`, d.name, d.description, d.working_hours, d.phone_number, r.stars
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
$review_query = $con->prepare("SELECT * FROM review JOIN tours.tourist t on t.tourist_id = review.tourist_id WHERE destination_id =? AND t.tourist_id=?");
$review_query->execute([$destination_id, $tourist_id]);
$reviews = $review_query->fetchAll();
$review_count = $review_query->rowCount();

// Fetch replies for each review from the database
foreach ($reviews as $review) {
    $review_id = $review['review_id'];

    $reply_query = $con->prepare("SELECT * FROM reply JOIN tours.tourist t on reply.tourist_id = t.tourist_id WHERE review_id =? ");
    $reply_query->execute([$review_id]);
    $replies = $reply_query->fetchAll();
}

// Check if there's a success message passed via GET parameter, if not, set it to an empty string
$successMsg = $_GET['success_message'] ?? '';


// Handle form submission for adding a review
$body = $body_error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = validate($_POST['body']);

    if (empty($body)) {
        $body_error = 'برجاء أدخال نص المراجعة'; // Please enter the review text
    } else {
        // Insert the review into the database
        $stmt = $con->prepare("INSERT INTO review (body, tourist_id, destination_id, date) VALUES (?,?,?,NOW())");
        $stmt->execute([$body, $tourist_id, $destination_id]);
        $successMsg = 'تم إضافة المراجعة بنجاح'; // Review added successfully
        // Redirect to the destination page with success message
        header("Location:show_destination.php?destination_id=" . $destination_id . "&success_message=" . urlencode($successMsg));
        exit; // Exit to prevent further execution after redirection
    }
}
?>


<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <title>توصية بالجولات</title>

    <style>
        .success {
            color: green;
        }
    </style>

</head>
<body>

<h1>
    عرض بيانات الوجهات
</h1>

<span><?php echo $successMsg ?> </span>

<br><br>

<a href="dashboard.php">الصفحة الشخصية</a>


<br><br><br>


اسم الوجهة:<?php echo $destination['name'] ?>
<br>
<br>
وصف الوجهة:<?php echo $destination['description'] ?>
<br>
<br>
 أوقات العمل:<?php echo $destination['working_hours'] ?>
<br>
<br>
عدد ساعات العمل:<?php echo $destination['range'] ?>
<br>
<br>
رقم الجوال:<?php echo $destination['phone_number'] ?>
<br>
<br>
التقييم:<?php echo $destination['stars'] ?>
<br>
<br>
<?php foreach ($images as $image): ?>
    <br><br>
    <img src="../uploads/<?php echo $image['image'] ?>" style="height: 250px; width: 250px" alt="">
    <br><br>
<?php endforeach; ?>


<div>
    <h3><?php echo $review_count ?> ريفيو</h3>

    <ul>
        <?php foreach ($reviews as $review): ?>
            <li>
                <div>
                    <!-- Icons Section -->
                    <div>

                        <a href="edit_review.php?review_id=<?php echo $review['review_id'] ?>">
                            تعديل مراجعة
                        </a>

                        <form action="delete_review.php?review_id=<?php echo $review['review_id'] ?>" method="post"
                              onsubmit="return confirm('هل تريد حذف  هذه المراجعة ؟');">
                            <input type="hidden" name="destination_id" value="<?php echo $destination_id ?>">
                            <button type="submit"> ازالة المراجعة</button>
                        </form>

                        <form action="like_dislike.php?review_id=<?php echo $review['review_id'] ?>" method="post">
                            <input type="hidden" name="action" value="like">
                            <input type="hidden" name="type" value="review">
                            <input type="hidden" name="destination_id" value="<?php echo $destination_id ?>">
                            <button type="submit"> إضافة اعجاب</button>
                        </form>

                        <form action="like_dislike.php?review_id=<?php echo $review['review_id'] ?>" method="post">
                            <input type="hidden" name="action" value="disliked">
                            <input type="hidden" name="type" value="review">
                            <input type="hidden" name="destination_id" value="<?php echo $destination_id ?>">
                            <button type="submit"> إضافة عدم أعجاب</button>
                        </form>

                        <a href="add_report.php?review_id=<?php echo $review['review_id'] ?>&type=review&destination_id=<?php echo $destination_id ?>">
                            رفع بلاغ
                        </a>

                    </div>
                    <!-- End Icons Section -->

                    <h3><?php echo $review['name'] ?></h3>

                    <p><?php echo $review['body'] ?></p>

                    <a href="add_reply.php?review_id=<?php echo $review['review_id'] ?>&destination_id=<?php echo $destination_id ?>">
                        إضافة تعليق
                    </a>

                    <!-- Replies for this review -->
                    <ul>
                        <?php foreach ($replies as $reply): ?>
                            <?php if ($reply['review_id'] == $review['review_id']): ?>
                                <li>
                                    <div>
                                        <a href="edit_reply.php?reply_id=<?php echo $reply['reply_id'] ?>&destination_id=<?php echo $destination_id ?>">
                                            تعديل الرد
                                        </a>

                                        <form action="delete_reply.php?reply_id=<?php echo $reply['reply_id'] ?>"
                                              method="post"
                                              onsubmit="return confirm('هل تريد رفع الحظر عن هذه الرد ؟');">
                                            <input type="hidden" name="review_id"
                                                   value="<?php echo $reply['review_id'] ?>">
                                            <button type="submit"> ازالة الرد</button>
                                        </form>

                                        <form action="like_dislike.php?reply_id=<?php echo $reply['reply_id'] ?>"
                                              method="post">
                                            <input type="hidden" name="action" value="like">
                                            <input type="hidden" name="type" value="reply">
                                            <input type="hidden" name="review_id"
                                                   value="<?php echo $reply['review_id'] ?>">
                                            <input type="hidden" name="destination_id"
                                                   value="<?php echo $destination_id ?>">
                                            <button type="submit"> إضافة اعجاب</button>
                                        </form>

                                        <form action="like_dislike.php?reply_id=<?php echo $reply['reply_id'] ?>"
                                              method="post">
                                            <input type="hidden" name="action" value="disliked">
                                            <input type="hidden" name="type" value="reply">
                                            <input type="hidden" name="review_id"
                                                   value="<?php echo $reply['review_id'] ?>">
                                            <input type="hidden" name="destination_id"
                                                   value="<?php echo $destination_id ?>">
                                            <button type="submit"> إضافة الي عدم الإعجاب</button>
                                        </form>

                                        <a href="add_report.php?reply_id=<?php echo $reply['reply_id'] ?>&type=reply&destination_id=<?php echo $destination_id ?>">
                                            رفع بلاغ
                                        </a>

                                    </div>

                                    <h3><?php echo $reply['name'] ?></h3>

                                    <p><?php echo $reply['body'] ?></p>

                                </li>
                            <?php endif; ?>

                        <?php endforeach; ?>

                    </ul>
                    <!-- End Replies for this review -->
                </div>
                <br><br>
            </li>
        <?php endforeach; ?>
    </ul>
    <!-- END comment-list -->
    <!-- Review Form Section -->
    <div>
        <h3>اترك تعليقك هنا</h3>
        <!-- Review Form -->
        <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="POST">
            <div>
                <label for="body"></label>


                <textarea cols="30" id="body" name="body" rows="4"></textarea>
                <span><?php echo $body_error ?></span>
            </div>

            <div>
                <button type="submit">
                    اضف المراجعة
                </button>
            </div>
        </form>

        <form action="add_rating.php?destination_id=<?php echo $destination_id ?>" method="post">
            <div>
                <div>

                    <input id="star-5" name="star" type="radio" value="5"/>
                    <label for="star-5"></label>

                    <input id="star-4" name="star" type="radio" value="4"/>

                    <label for="star-4"></label>

                    <input id="star-3" name="star" type="radio" value="3"/>

                    <label for="star-3"></label>

                    <input id="star-2" name="star" type="radio" value="2"/>

                    <label for="star-2"></label>

                    <input id="star-1" name="star" type="radio" value="1"/>

                    <label for="star-1"></label>
                    <button type="submit">
                        إضافة تقييم
                    </button>
                </div>


            </div>
        </form>
    </div>
</div>
</body>

</html>
