<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$username = $_SESSION['username'];

// Query to calculate completion rate
$query = "SELECT 
            (SUM(CASE WHEN status = 'finished' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS completion_rate 
          FROM schedule_tbl 
          WHERE username = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$completion_rate = $row['completion_rate'] ?? 0;
echo json_encode(['completion_rate' => $completion_rate]);
?>
