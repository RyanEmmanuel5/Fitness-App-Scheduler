<?php
require 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in']);
    error_log('Unauthorized user access');
    exit();
}

// Get the logged-in username from the session
$username = $_SESSION['username'];

// Fetch events with status 'pending' for the logged-in user
$stmt = $conn->prepare("SELECT event_id, DATE, START_TIME, END_TIME, TITLE, DESCRIPTION FROM schedule_tbl WHERE USERNAME = ? AND STATUS = 'pending'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Prepare events data
$events = [];
while ($row = $result->fetch_assoc()) {
    error_log('Fetched Row: ' . json_encode($row)); // Debugging log for fetched rows
    $events[] = [
        'event_id' => $row['event_id'], // Include the unique event ID
        'start' => $row['DATE'] . 'T' . $row['START_TIME'],
        'end' => $row['DATE'] . 'T' . $row['END_TIME'],
        'title' => $row['TITLE'],
        'description' => $row['DESCRIPTION']
    ];
}

// Debugging the final JSON output
error_log('Final Events JSON: ' . json_encode($events));

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($events);

$stmt->close();
$conn->close();
?>
