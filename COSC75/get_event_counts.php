<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cosc75";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user = $_SESSION['username'];

// Fetch finished weekly data (only 'completed' events)
$weeklySql = "SELECT WEEK(date) as week, COUNT(*) as count 
              FROM schedule_tbl 
              WHERE username = '$user' AND status = 'finished' 
              GROUP BY WEEK(date) 
              ORDER BY week DESC 
              LIMIT 5";

$weeklyResult = $conn->query($weeklySql);

$weeklyData = [];
if ($weeklyResult->num_rows > 0) {
    while ($row = $weeklyResult->fetch_assoc()) {
        $weeklyData[] = ['week' => $row['week'], 'count' => $row['count']];
    }
}

// Fetch finished monthly data (only 'completed' events)
$monthlySql = "SELECT MONTH(date) as month, COUNT(*) as count 
               FROM schedule_tbl 
               WHERE username = '$user' AND status = 'finished' 
               GROUP BY MONTH(date) 
               ORDER BY month DESC 
               LIMIT 5";

$monthlyResult = $conn->query($monthlySql);

$monthlyData = [];
if ($monthlyResult->num_rows > 0) {
    while ($row = $monthlyResult->fetch_assoc()) {
        $monthlyData[] = ['month' => $row['month'], 'count' => $row['count']];
    }
}

$conn->close();

// Return combined data
echo json_encode([
    "weekly" => $weeklyData,
    "monthly" => $monthlyData
]);
?>
