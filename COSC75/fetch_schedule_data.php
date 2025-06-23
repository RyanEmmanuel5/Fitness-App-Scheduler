<?php
// Database credentials
$host = 'localhost';   // Database host
$db = 'cosc75'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

// Create a connection to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set PDO to throw exceptions for errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch the status column from schedule_tbl
    $query = 'SELECT status FROM schedule_tbl';
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    echo json_encode($results);

} catch (PDOException $e) {
    // In case of error, return a JSON with an error message
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>
