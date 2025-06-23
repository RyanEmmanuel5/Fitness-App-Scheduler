<?php
session_start();
require 'db.php'; // Include the database connection

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT password FROM users_tbl WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $username; // Store the username in the session

            // Update last login time
            $update_stmt = $conn->prepare("UPDATE users_tbl SET last_login = CURRENT_TIMESTAMP WHERE username = ?");
            $update_stmt->bind_param("s", $username);
            $update_stmt->execute();

            echo "success"; // Redirect to landing page or other success handling
        } else {
            echo "Invalid credentials";
        }
    } else {
        echo "Invalid credentials";
    }

    $stmt->close();
}
$conn->close();
?>
