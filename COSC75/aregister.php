<?php
// Include the database connection file
require 'db.php';

// Handle the AJAX request for checking username availability
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'check_username') {
    $username = $_POST['username'];

    // Check if the username already exists in the database
    $sql = "SELECT * FROM admin_tbl WHERE A_USERNAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "taken";  // Username already exists
    } else {
        echo "available";  // Username is available
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
    exit;
}

// Handle registration (regular form submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    // Get form data
    $username = $_POST['A_USERNAME'];
    $email = $_POST['A_EMAIL'];
    $password = $_POST['A_PASSWORD'];
    $repassword = $_POST['A_REPASSWORD'];

    // Check if the username already exists
    $sql = "SELECT * FROM admin_tbl WHERE A_USERNAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username is already taken. Please choose another one.";  // Return error message
        exit;
    }

    // Check if the passwords match
    if ($password !== $repassword) {
        echo "Passwords do not match. Please try again.";  // Return error message
        exit;
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user data into the database
    $sql = "INSERT INTO admin_tbl (A_USERNAME, A_EMAIL, A_PASSWORD) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "success";  // Registration successful
    } else {
        echo "An error occurred during registration. Please try again.";  // Return error message
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
