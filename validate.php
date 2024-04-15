<?php
function validate($data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}


// Function to generate a random password
function generateRandomPassword($length): string
{
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $specialChars = '!@#$%^&*()-_+=';

    $password = '';

    // At least one uppercase letter
    $password .= $uppercase[rand(0, strlen($uppercase) - 1)];

    // At least one lowercase letter
    $password .= $lowercase[rand(0, strlen($lowercase) - 1)];

    // At least one number
    $password .= $numbers[rand(0, strlen($numbers) - 1)];

    // At least one special character
    $password .= $specialChars[rand(0, strlen($specialChars) - 1)];

    // Generate remaining characters
    for ($i = 0; $i < $length - 4; $i++) {
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
    }

    // Shuffle the password string to mix characters
    return str_shuffle($password);
}