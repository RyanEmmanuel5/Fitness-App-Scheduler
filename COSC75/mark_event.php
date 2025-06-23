<?php
include 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $status = 'missed';

    // Update the event status to 'missed'
    $stmt = $conn->prepare("UPDATE schedule_tbl SET status = ? WHERE event_id = ?");
    $stmt->bind_param("si", $status, $event_id);

    if ($stmt->execute()) {
        // Fetch the username of the event owner
        $stmt_user = $conn->prepare("SELECT username, title FROM schedule_tbl WHERE event_id = ?");
        $stmt_user->bind_param("i", $event_id);
        $stmt_user->execute();
        $result = $stmt_user->get_result();
        $row = $result->fetch_assoc();
        $username = $row['username'];
        $event_title = $row['title'];

        // Insert the notification into notifications_tbl
        $message = "Your event '$event_title' has been marked as missed.";
        $stmt_notification = $conn->prepare("INSERT INTO notifications_tbl (username, message) VALUES (?, ?)");
        $stmt_notification->bind_param("ss", $username, $message);

        if ($stmt_notification->execute()) {
            echo json_encode(["success" => true, "message" => "Event marked as missed and notification sent."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to send notification."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to mark event as missed."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
