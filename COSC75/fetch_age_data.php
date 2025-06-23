<?php
// Database connection
include 'db.php';

$sql = "SELECT age FROM users_tbl WHERE age IS NOT NULL";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $ageGroups = [
        '1-15' => 0,
        '16-30' => 0,
        '31-45' => 0,
        '46-60' => 0,
        '61-75' => 0,
        '76+' => 0
    ];

    while($row = mysqli_fetch_assoc($result)) {
        $age = $row['age'];
        
        // Group ages into ranges
        if ($age <= 15) {
            $ageGroups['1-15']++;
        } elseif ($age <= 30) {
            $ageGroups['16-30']++;
        } elseif ($age <= 45) {
            $ageGroups['31-45']++;
        } elseif ($age <= 60) {
            $ageGroups['46-60']++;
        } elseif ($age <= 75) {
            $ageGroups['61-75']++;
        } else {
            $ageGroups['76+']++;
        }
    }

    echo json_encode($ageGroups);  // Output the counts as JSON
} else {
    echo json_encode([]);
}
?>
