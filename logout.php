<?php

session_start(); // Start the session

session_unset();  // unset the data

session_destroy(); // destroy the session

$successMsg = 'تم تسجيل الخروج بنجاح';
header('location: login.php?message='.$successMsg);


exit();
