<?php
include 'db.php'; // Database connection

header('Content-Type: application/json'); // Specify JSON output

$query = "SELECT 
    SUM(CASE WHEN gender IN ('male', 'm', 'Male', 'M') THEN 1 ELSE 0 END) AS male_count,
    SUM(CASE WHEN gender IN ('female', 'f', 'Female', 'F') THEN 1 ELSE 0 END) AS female_count,
    SUM(CASE WHEN gender IS NULL OR gender NOT IN ('male', 'm', 'Male', 'M', 'female', 'f', 'Female', 'F') THEN 1 ELSE 0 END) AS other_count
    FROM users_tbl";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

echo json_encode($data); // Output JSON
exit;
?>
