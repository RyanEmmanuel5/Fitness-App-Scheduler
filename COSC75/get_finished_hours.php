<?php
// Start session to get logged-in user
session_start();
include 'db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$username = $_SESSION['username'];

// SQL to calculate total finished event hours
$query = "SELECT SUM(TIMESTAMPDIFF(HOUR, 
            CONCAT(date, ' ', start_time), 
            CONCAT(date, ' ', end_time))) AS total_hours 
          FROM schedule_tbl 
          WHERE username = ? AND status = 'finished'";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_hours = $row['total_hours'] ?? 0;

// Return as JSON
echo json_encode(['total_hours' => $total_hours]);
?>
