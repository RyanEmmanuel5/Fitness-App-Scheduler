<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['event_id']);
    $date = $_POST['eventDate'];
    $start_time = $_POST['startTime'];
    $end_time = $_POST['endTime'];
    $title = $_POST['eventTitle'];
    $description = $_POST['eventDescription'];
    $status = isset($_POST['status']) ? $_POST['status'] : null; // Check if status is provided

    try {
        // Prepare the SQL statement dynamically based on whether 'status' is provided
        $sql = "UPDATE schedule_tbl SET date = ?, start_time = ?, end_time = ?, title = ?, description = ?";
        if ($status !== null) {
            $sql .= ", status = ?";
        }
        $sql .= " WHERE event_id = ?";

        $stmt = $conn->prepare($sql);

        if ($status !== null) {
            // Bind parameters including the status
            $stmt->bind_param('ssssssi', $date, $start_time, $end_time, $title, $description, $status, $event_id);
        } else {
            // Bind parameters without the status
            $stmt->bind_param('ssssss', $date, $start_time, $end_time, $title, $description, $event_id);
        }

        if ($stmt->execute()) {
            echo "Event updated successfully!";
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Failed to update event: " . $e->getMessage());
        echo "Error: Unable to update the event.";
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo "Invalid request method.";
}
?>
