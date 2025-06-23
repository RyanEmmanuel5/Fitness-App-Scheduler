<?php
require 'db.php';
header('Content-Type: application/json');
session_start();


// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Decode the incoming POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['event_id'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$eventId = $data['event_id'];

// Update the database to mark the event as finished
try {
    $stmt = $conn->prepare("UPDATE schedule_tbl SET status = 'finished' WHERE event_id = ? AND username = ?");
    $stmt->bind_param("is", $eventId, $_SESSION['username']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No changes made']);
    }
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
