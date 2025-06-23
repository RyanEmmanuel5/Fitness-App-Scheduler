<?php
session_start(); // Start the session

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $summary = $_POST['summary'];
    $image = $_FILES['workoutImage']['name'];
    $target = "uploads/" . basename($image);

    // Get the logged-in username
    $username = $_SESSION['username']; // Ensure username is set during login

    // Validate and handle file upload
    if (move_uploaded_file($_FILES['workoutImage']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO fitness_entries (username, summary, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $summary, $target);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "File upload failed."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request."]);
}

$conn->close();
?>
