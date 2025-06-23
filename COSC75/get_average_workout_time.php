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

// SQL to calculate average finished event hours
$query = "SELECT AVG(TIMESTAMPDIFF(HOUR, 
            CONCAT(date, ' ', start_time), 
            CONCAT(date, ' ', end_time))) AS average_hours 
          FROM schedule_tbl 
          WHERE username = ? AND status = 'finished'";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row && isset($row['average_hours'])) {
        $average_hours = $row['average_hours'];
        echo json_encode(['average_hours' => $average_hours]);
    } else {
        echo json_encode(['n/a']);
    }
} else {
    echo json_encode(['error' => $stmt->error]);
}
?>
