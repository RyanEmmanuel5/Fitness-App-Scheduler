<?php
session_start();
include 'db.php'; // Database connection script

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the form has been submitted with a valid action
    if (isset($_POST['delete_user']) && $_POST['delete_user'] === 'Delete My Account') {
        $username = $_SESSION['username']; // Assuming the username is stored in session

        // Prepare the SQL DELETE statement
        $stmt = $conn->prepare("DELETE FROM users_tbl WHERE username = ?");
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            // Redirect to logout page or home page after successful deletion
            header("Location: logout.php");
        } else {
            echo "Error deleting user.";
        }

        $stmt->close();
    } else {
        echo "Invalid request.";
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
