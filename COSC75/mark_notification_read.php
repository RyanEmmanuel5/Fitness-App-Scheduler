<?php
require 'db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$username = $_SESSION['username'];

try {
    // Mark missed events notifications as 'read'
    $stmt1 = $conn->prepare("UPDATE notifications_tbl SET status = 'read' WHERE username = ? AND status = 'unread'");
    $stmt1->bind_param("s", $username);
    $stmt1->execute();
    
    // Mark achievements notifications as 'read'
    $stmt2 = $conn->prepare("UPDATE user_achievements_tbl SET status = 'read' WHERE username = ? AND status = 'unlocked'");
    $stmt2->bind_param("s", $username);
    $stmt2->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>
