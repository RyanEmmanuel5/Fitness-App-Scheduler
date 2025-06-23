<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo 'User not logged in.';
    exit;
}

// Ensure the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventDate = trim($_POST['eventDate']);
    $username = $_SESSION['username'];

    if (!$eventDate) {
        echo 'Invalid request: Missing date parameter.';
        exit;
    }

    // Use a prepared statement to securely delete the event
    $stmt = $conn->prepare("
        DELETE FROM schedule_tbl 
        WHERE date = ? AND username = ?
    ");
    $stmt->bind_param('ss', $eventDate, $username);

    if ($stmt->execute()) {
        echo 'Event deleted successfully!';
    } else {
        echo 'Error deleting event: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'Invalid request method.';
}
?>
