<?php
require 'db.php';
session_start(); // Start the session to access session variables

if (!isset($_SESSION['username'])) {
    echo 'User not logged in.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch POST data safely
    $eventId = isset($_POST['event_id']) ? trim($_POST['event_id']) : null;
    $eventDate = trim($_POST['eventDate']);
    $startTime = trim($_POST['startTime']);
    $endTime = trim($_POST['endTime']);
    $eventTitle = trim($_POST['eventTitle']);
    $eventDescription = trim($_POST['eventDescription']);
    $username = $_SESSION['username'];

    if (!$eventDate || !$startTime || !$endTime || !$eventTitle) {
        echo "All fields are required.";
        exit;
    }

    if ($eventId) {
        // If event ID exists, perform an UPDATE
        $updateStmt = $conn->prepare("
            UPDATE schedule_tbl 
            SET date = ?, start_time = ?, end_time = ?, title = ?, description = ?
            WHERE event_id = ? AND username = ?
        ");
        $updateStmt->bind_param('sssssss', $eventDate, $startTime, $endTime, $eventTitle, $eventDescription, $eventId, $username);

        if ($updateStmt->execute()) {
            echo 'Event updated successfully!';
        } else {
            echo 'Error updating event: ' . $updateStmt->error;
        }

        $updateStmt->close();
    } else {
        // Insert logic if no event_id is provided
        $insertStmt = $conn->prepare("
            INSERT INTO schedule_tbl (date, start_time, end_time, title, description, username, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        $insertStmt->bind_param('ssssss', $eventDate, $startTime, $endTime, $eventTitle, $eventDescription, $username);

        if ($insertStmt->execute()) {
            echo 'Event created successfully!';
        } else {
            echo 'Error saving event: ' . $insertStmt->error;
        }

        $insertStmt->close();
    }

    $conn->close();
} else {
    echo 'Invalid request method.';
}

?>
