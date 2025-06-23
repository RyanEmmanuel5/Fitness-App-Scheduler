<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$eventId = $data['event_id'] ?? null;

if ($eventId) {
    // Step 1: Mark the event as missed
    $stmt = $conn->prepare("UPDATE schedule_tbl SET status = 'missed' WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);

    if ($stmt->execute()) {
        // Log success and confirm event update
        error_log("Event ID {$eventId} successfully marked as missed.");

        // Step 2: Fetch event details for the notification
        $stmt2 = $conn->prepare("SELECT username, title, date, start_time, end_time FROM schedule_tbl WHERE event_id = ?");
        $stmt2->bind_param("i", $eventId);
        $stmt2->execute();
        $result = $stmt2->get_result();
        $event = $result->fetch_assoc();

        if ($event) {
            // Step 3: Insert a notification for the missed event
            $stmt3 = $conn->prepare("INSERT INTO notifications_tbl (username, message, status, created_at) VALUES (?, ?, ?, NOW())");
            $message = "You missed a workout: " . $event['title'] . " on " . $event['date'];
            $status = "unread";
            $stmt3->bind_param("sss", $event['username'], $message, $status);

            if ($stmt3->execute()) {
                error_log("Notification for Event ID {$eventId} added successfully.");
                echo json_encode(['success' => true, 'notification' => true]);
            } else {
                error_log("Failed to insert notification for Event ID {$eventId}. Error: " . $conn->error);
                echo json_encode(['success' => true, 'notification' => false, 'error' => $conn->error]);
            }

            $stmt3->close();
        } else {
            error_log("Failed to fetch event details for Event ID {$eventId}.");
            echo json_encode(['success' => true, 'notification' => false, 'error' => 'Event details not found.']);
        }

        $stmt2->close();
    } else {
        // Log failure details
        error_log("Failed to mark Event ID {$eventId} as missed. Error: " . $conn->error);
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }

    $stmt->close();
} else {
    // Log when event ID is not provided
    error_log("Event ID not provided.");
    echo json_encode(['success' => false, 'error' => 'Event ID not provided.']);
}

$conn->close();
?>
