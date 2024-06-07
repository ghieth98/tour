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

// Retrieve form inputs
$days = validate($_POST['days']);
$month = validate($_POST['month']);
$budget = validate($_POST['budget']);
$age = validate($_POST['age']);
$needs = validate($_POST['needs']);
$personality = validate($_POST['personality']);

// Initialize an empty array to hold the destination IDs from recommendations
$recommendedDestinations = [];

// Retrieve rates from the database
$query = $con->prepare("SELECT * FROM rate");
$query->execute();

$rates = $query->fetchAll(PDO::FETCH_ASSOC);

// Retrieve recommendations from API
foreach ($rates as $rate) {
    $data = array(
        'destination_id' => $rate['destination_id'],
        'tourist_id' => $rate['tourist_id'],
        'rate' => $rate['stars']
    );
    // Python API EndPoint
    $url = 'http://localhost:5000/';
    $requestOptions = array(
        'http' => array(
            'header' => 'Content-Type: application/json\r\n',
            'method' => 'POST',
            'content' => json_encode($data),
        ),
    );

    $httpContext = stream_context_create($requestOptions);

    $response = file_get_contents($url, false, $httpContext);
    $recommendations = json_decode($response, true);

    // Merge the recommendations into the array
    $recommendedDestinations = array_merge($recommendedDestinations, $recommendations);
}

// Remove duplicate destination IDs
$recommendedDestinations = array_unique($recommendedDestinations);

// Prepare the base SQL statement
$sql = "SELECT *
        FROM destination
        WHERE ";

// Initialize an array to hold conditions for the WHERE clause
$conditions = [];

// Construct WHERE clause based on form inputs
if (!empty($days)) {
    if ($days == "1-3") {
        $conditions[] = "days BETWEEN 1 AND 3";
    } elseif ($days == "4-7") {
        $conditions[] = "days BETWEEN 4 AND 7";
    } elseif ($days == "8+") {
        $conditions[] = "days >= 8";
    }
}

if (!empty($month)) {
    $conditions[] = "MONTH(date) = :month";
}

if (!empty($budget)) {
    $conditions[] = "budget = :budget";
}

if (!empty($age)) {
    $conditions[] = "age_group = :age";
}

if (!empty($needs)) {
    if ($needs == "yes") {
        $conditions[] = "special_needs = yes";
    } elseif ($needs == "no") {
        $conditions[] = "special_needs = no";
    }
}

if (!empty($personality)) {
    $conditions[] = "personality = :personality";
}

if (!empty($recommendedDestinations)) {
    $recommendedCondition = "destination_id IN (" . implode(",", $recommendedDestinations) . ")";
    $conditions[] = $recommendedCondition;
}

// Check if there are any conditions
if (!empty($conditions)) {
    // Append conditions to the SQL statement
    $sql .= implode(" AND ", $conditions);
} else {
    // If no conditions are specified, select all destinations
    $sql .= "1"; // This is equivalent to "WHERE 1", selecting all
}

// Limit the number of results to 5
$sql .= " LIMIT 5";

// Prepare the SQL statement
$stmt = $con->prepare($sql);

// Bind parameters if needed
if (!empty($month)) {
    $stmt->bindParam(':month', $month);
}

if (!empty($budget)) {
    $stmt->bindParam(':budget', $budget);
}

if (!empty($age)) {
    $stmt->bindParam(':age', $age);
}

if (!empty($personality)) {
    $stmt->bindParam(':personality', $personality);
}

// Execute the query
$stmt->execute();

// Fetch results as needed
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process and display results as needed
foreach ($results as $result) {
    // Display destination details
    echo "Destination Name: " . $result['destination_name'] . "<br>";
    echo "Location: " . $result['location'] . "<br>";
    echo "Days: " . $result['days'] . "<br>";
    echo "Budget: " . $result['budget'] . "<br>";
    // Add more details as needed
    echo "<br>";
}

