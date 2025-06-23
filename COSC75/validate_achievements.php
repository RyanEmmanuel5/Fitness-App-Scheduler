<?php
// validate_achievements.php
// Include database connection
require_once 'db.php'; // Your database connection file

session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if ($username === null) {
    echo 'User not logged in.';
    exit;
}

// === 1. One Day Wonder: First Workout Logged ===
$achievement_id = 1;

// Check if user has exactly 1 workout logged
$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM schedule_tbl WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 1) {
    // Verify if 'One Day Wonder' already exists in user_achievements_tbl
    $stmt2 = $conn->prepare("SELECT COUNT(*) AS count FROM user_achievements_tbl WHERE username = ? AND achievement_id = ?");
    $stmt2->bind_param("si", $username, $achievement_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $row2 = $result2->fetch_assoc();

    if ($row2['count'] == 0) {
        // Unlock achievement
        $stmt3 = $conn->prepare("INSERT INTO user_achievements_tbl (username, achievement_id, date_achieved) VALUES (?, ?, NOW())");
        $stmt3->bind_param("si", $username, $achievement_id);
        $stmt3->execute();
        echo "Achievement Unlocked: 'One Day Wonder'!";
    }
}

// === Future Achievements Can Be Added Here ===
// Example: Weekly Warrior, Milestone Achievements, etc.

// Close database connection
$conn->close();
?>
