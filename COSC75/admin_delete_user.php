<?php
session_start();
include 'db.php'; // Database connection script

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $userId = intval($_GET['id']);

        // Prepare the SQL DELETE statement
        $stmt = $conn->prepare("DELETE FROM users_tbl WHERE ID = ?");
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "User deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error deleting user."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid ID."]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
