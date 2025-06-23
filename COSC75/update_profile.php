<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

// Database connection
$host = 'localhost';
$db = 'cosc75';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    
    // Image upload handling
    $profile_picture = null; // Default to null if no file is uploaded
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $uploadDir = 'uploads/';
        $filePath = $uploadDir . basename($fileName);
    
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $profile_picture = $filePath; // Correctly set
            echo "File uploaded: $profile_picture";
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "No file uploaded or error in upload.";
    }
    
    // Update user details in the database
    $sql = "UPDATE users_tbl SET F_NAME = ?, L_NAME = ?, BIRTHDATE = ?, AGE = ?, PROFILE_PICTURE = ? WHERE USERNAME = ?";
    $sql = "UPDATE users_tbl SET F_NAME = ?, L_NAME = ?, BIRTHDATE = ?, AGE = ?, PROFILE_PICTURE = ? WHERE USERNAME = ?";
    $stmt = $conn->prepare($sql);
    
    // Correct bind_param call
    $stmt->bind_param("ssssss", $first_name, $last_name, $birthdate, $age, $profile_picture, $username);
    
    // Execute and debug
    if ($stmt->execute()) {
        echo "Record updated successfully!";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}

$conn->close();
?>
