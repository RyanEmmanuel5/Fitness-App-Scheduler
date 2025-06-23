<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username']; // Logged-in user's username

// Database connection
$host = 'localhost';
$db = 'cosc75';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details from the database (this must happen first)
$sql = "SELECT * FROM users_tbl WHERE USERNAME = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if (!$user_data) {
    die("User not found. Please ensure the database contains the correct user data.");
}

// Handle form submission (update user details)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    // Check if age or gender is empty
    $age = empty($age) ? NULL : $age;
    $gender = empty($gender) ? NULL : $gender;

    // Handle profile picture upload
    $profile_picture_path = $user_data['PROFILE_PICTURE']; // Default to current picture
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate file type
        if (in_array($imageFileType, $valid_extensions)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $profile_picture_path = $target_file; // Update the picture path
            } else {
                $error = "Failed to upload profile picture.";
            }
        } else {
            $error = "Invalid file type. Please upload an image (JPG, JPEG, PNG, GIF).";
        }
    }

    // Update the database
    $update_sql = "UPDATE users_tbl SET F_NAME = ?, L_NAME = ?, BIRTHDATE = ?, AGE = ?, GENDER = ?, PROFILE_PICTURE = ? WHERE USERNAME = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssisss", $first_name, $last_name, $birthdate, $age, $gender, $profile_picture_path, $username);

    if (!$stmt->execute()) {
        $error = "Failed to update profile: " . $conn->error;
    } else {
        $success = "Profile updated successfully.";
        // Refresh user data after update
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
    }

    $stmt->close();
}

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $delete_query = "DELETE FROM users_tbl WHERE username = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param('s', $username);
    if ($delete_stmt->execute()) {
        session_destroy();
        header('Location: logout.php'); // Redirect to logout
        exit;
    } else {
        $error_message = "Error deleting user.";
    }
}

// Fetch fitness entries for the logged-in user
$query_fitness = "SELECT image_path, summary, created_at FROM fitness_entries WHERE username = ?";
$stmt_fitness = $conn->prepare($query_fitness);
$stmt_fitness->bind_param('s', $username); // Using username to filter fitness entries
$stmt_fitness->execute();
$result_fitness = $stmt_fitness->get_result();
$fitness_entries = $result_fitness->fetch_all(MYSQLI_ASSOC);

// Fetch notifications from missed events table
$stmt1 = $conn->prepare("SELECT message, status, created_at FROM notifications_tbl WHERE username = ? ORDER BY created_at DESC LIMIT 10");
$stmt1->bind_param("s", $username);
$stmt1->execute();
$result1 = $stmt1->get_result();
$missedEventNotifications = $result1->fetch_all(MYSQLI_ASSOC);

// Fetch notifications from user achievements table with their titles
$stmt2 = $conn->prepare("
    SELECT 
        CONCAT('Achievement Unlocked: ', a.title) AS message, 
        'unread' AS status, 
        ua.date_achieved AS created_at
    FROM user_achievements_tbl ua
    INNER JOIN achievements_tbl a ON ua.achievement_id = a.achievement_id
    WHERE ua.username = ? AND ua.status = 'unlocked'
    ORDER BY ua.date_achieved DESC
    LIMIT 10
");
$stmt2->bind_param("s", $username);
$stmt2->execute();
$result2 = $stmt2->get_result();
$achievementNotifications = $result2->fetch_all(MYSQLI_ASSOC);

// Merge notifications (combine missed event notifications and achievements notifications)
$notifications = array_merge($missedEventNotifications, $achievementNotifications);

// Sort combined notifications by timestamp (created_at)
usort($notifications, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles4.css">

    <style>
        /* Override button focus and active states to keep original color */
        .btn:focus, .btn:active {
            outline: none; /* Remove outline */
            box-shadow: none; /* Remove box shadow */
            background-color: inherit; /* Keep original background */
            border-color: inherit; /* Keep original border color */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg py-3" style="background-color: rgba(36, 36, 37, 1);">
                <div class="container px-5">
                    <!-- Navbar Button -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" 
                        style="border-color: rgba(215, 211, 195, 1);">
                        <span class="navbar-toggler-icon" style="background-color: rgba(215, 211, 195, 1);"></span>
                    </button>

                    <!-- Logo -->
                    <a class="navbar-brand" href="#">
                        <img src="logo1.png" alt="Logo" style="height: 40px; width: auto;">
                    </a>

                    <!-- Collapsible Menu -->
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder">
                            <li class="nav-item"><a class="nav-link" href="landing.php" style="color: rgba(215, 211, 195, 1);">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="dashboard.php" style="color: rgba(215, 211, 195, 1);">Dashboard</a></li>
                            <!-- Notification Dropdown -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" 
       data-bs-toggle="dropdown" aria-expanded="false" style="color: rgba(215, 211, 195, 1);">
        Notifications <span class="badge bg-danger" id="notificationCount">
            <?php 
            // Count unread notifications
            $unreadCount = count(array_filter($notifications, function($n) {
                return isset($n['status']) && $n['status'] === 'unread';
            }));
            echo $unreadCount > 0 ? $unreadCount : ''; 
            ?>
        </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
        <?php 
        // Filter unread notifications
        $unreadNotifications = array_filter($notifications, function($n) {
            return isset($n['status']) && $n['status'] === 'unread';
        });
        ?>
        <?php if (count($unreadNotifications) > 0): ?>
            <?php foreach ($unreadNotifications as $notification): ?>
                <li>
                    <a class="dropdown-item <?php echo ($notification['status'] == 'unread') ? 'font-weight-bold' : ''; ?>"
                       style="font-size: 14px;"> 
                       <?php echo htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li><a class="dropdown-item text-center" href="#">No new notifications</a></li>
        <?php endif; ?>
    </ul>
</li>
                            <li class="nav-item"><a class="nav-link" href="Logout.php" style="color: rgba(215, 211, 195, 1);">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

    <!-- Main Content -->
    <main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-12 bg-dark text-white p-4 rounded">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <!-- Profile Picture Section -->
                        <div class="profile-picture bg-transparent mb-3 text-center">
                            <label for="profilePictureInput" id="profilePictureLabel" style="cursor: pointer;">
                                <img id="profilePreview" src="<?= htmlspecialchars($user_data['PROFILE_PICTURE'] ?: 'https://via.placeholder.com/150') ?>" alt="Profile Picture" class="img-fluid">
                            </label>
                            <!-- Hidden file input -->
                            <input type="file" id="profilePictureInput" name="profile_picture" class="form-control d-none" accept="image/*" onchange="previewImage(event)" disabled>
                        </div>
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control bg-secondary text-white" value="<?= htmlspecialchars($user_data['USERNAME']) ?>" readonly>
                    </div>

                    <div class="col-md-8">
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-secondary text-white" value="<?= htmlspecialchars($user_data['EMAIL']) ?>" readonly>
                        </div>
                        <!-- First Name and Last Name -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control bg-secondary text-white editable" name="first_name" value="<?= htmlspecialchars($user_data['F_NAME']) ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control bg-secondary text-white editable" name="last_name" value="<?= htmlspecialchars($user_data['L_NAME']) ?>" readonly>
                            </div>
                        </div>
                        <!-- Birthdate, Age, and Gender -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="birthdate" class="form-label">Birthdate</label>
                                <input type="date" class="form-control bg-secondary text-white editable" name="birthdate" value="<?= htmlspecialchars($user_data['BIRTHDATE']) ?>" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control bg-secondary text-white editable" name="age" value="<?= htmlspecialchars($user_data['AGE']) ?>" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <input type="text" class="form-control bg-secondary text-white editable" name="gender" value="<?= htmlspecialchars($user_data['GENDER']) ?>" readonly>
                            </div>
                        </div>
                        <!-- Date Created -->
                        <div class="mb-3">
                            <label for="date-created" class="form-label">Date Joined</label>
                            <input type="text" class="form-control bg-secondary text-white" value="<?= htmlspecialchars($user_data['DATE_CREATED']) ?>" readonly>
                        </div>
                        <!-- Buttons -->
                        <div class="d-flex justify-content-around">
                            <button type="button" id="updateButton" class="btn btn-warning">Update</button>
                            <button type="submit" class="btn btn-success d-none" id="saveButton">Save</button>
                            <form action="delete_user.php" method="POST">
                                <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </form>
            <div class="container mt-4" style="padding: 20px; background-color:rgba(215, 211, 195, 1); border-radius: 10px;">
                <!-- Additional content or sections can be added here -->
                <h1 class="text-center" style = "background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); border-radius:5px; padding: 10px;">Your Workout Diary</h1>
                <!-- Fitness Entries Feed -->
                <div class="container mt-4" style="max-width: 1200px; height: auto; overflow-y: auto;">
                    <div class="row">
                        <?php foreach ($fitness_entries as $entry): ?>
                            <div class="col-md-4 mb-3 d-flex justify-content-center align-items-center">
                                <div class="card bg-secondary text-white" style="width: 400px; height: 350px;">
                                    <!-- Reduced image size and made it square -->
                                    <img src="<?= htmlspecialchars($entry['image_path']) ?>" alt="Fitness Entry Image" class="card-img-top img-small">
                                    <div class="card-body" style="max-height: 150px; overflow-y: auto;">
                                        <p class="card-text"><?= htmlspecialchars($entry['summary']) ?></p>
                                        <small class="text-muted"><?= htmlspecialchars($entry['created_at']) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
</main>
<style>
    .img-small {
        width: 100%;  /* Adjust the image size */
        height: auto; /* Maintain aspect ratio */
        max-height: 200px;
    }
</style>
    <style>
   .editable-highlight {
    border: 2px solid rgba(215, 97, 60, 1);
    background-color: #f8f9fa; /* Light gray background */
}
#profilePictureLabel.editable-highlight {
    border: 3px solid rgba(215, 97, 60, 1); /* Optional: add border to label */

}
#profilePreview {
    border-radius: 0; /* Optional: if you want to make it circular */
    width: 150px; /* Set a fixed width */
    height: 150px; /* Set the height to the same as the width to make it square */
    object-fit: cover; /* Ensure the image covers the entire area without distortion */
    cursor: pointer; /* Make it clickable */
}
.editable-highlight {
        border: 3px solid rgba(215, 97, 60, 1);
        background-color: #f8f9fa; /* Optional: Add a light background color */
    }

</style>

<script>
 // Update button functionality to make fields editable and enable the file input
document.getElementById('updateButton').addEventListener('click', function () {
    const editableFields = document.querySelectorAll('.editable');
    editableFields.forEach(field => {
        field.readOnly = false;
        field.classList.add('editable-highlight'); // Add highlight class
    });
    document.getElementById('updateButton').classList.add('d-none');
    document.getElementById('saveButton').classList.remove('d-none');

    // Enable the profile picture input and add highlight class
    const profilePictureInput = document.getElementById('profilePictureInput');
    profilePictureInput.disabled = false;
    profilePictureInput.classList.add('editable-highlight'); // Highlight the file input

    // Add highlight to the label that acts as the file input trigger
    const profilePictureLabel = document.getElementById('profilePictureLabel');
    profilePictureLabel.classList.add('editable-highlight'); // Add highlight class to the label
});

// View button functionality to make fields read-only and remove the highlight class
document.getElementById('viewButton').addEventListener('click', function () {
    const editableFields = document.querySelectorAll('.editable');
    editableFields.forEach(field => {
        field.readOnly = true;
        field.classList.remove('editable-highlight'); // Remove highlight class
    });
    document.getElementById('updateButton').classList.remove('d-none');
    document.getElementById('saveButton').classList.add('d-none');

    // Disable the profile picture input and remove highlight class
    const profilePictureInput = document.getElementById('profilePictureInput');
    profilePictureInput.disabled = true;
    profilePictureInput.classList.remove('editable-highlight'); // Remove highlight class

    // Remove highlight from the label
    const profilePictureLabel = document.getElementById('profilePictureLabel');
    profilePictureLabel.classList.remove('editable-highlight'); // Remove highlight class
});

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = new Image();
            img.src = e.target.result;
            
            img.onload = function() {
                // Get the smallest dimension to make it square
                const size = Math.min(img.width, img.height);
                
                // Create a canvas to crop/resize the image to square
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                // Set the canvas size to the square size
                canvas.width = size;
                canvas.height = size;

                // Draw the image centered in the square canvas
                ctx.drawImage(img, (img.width - size) / 2, (img.height - size) / 2, size, size, 0, 0, size, size);
                
                // Convert the canvas to a data URL
                const dataUrl = canvas.toDataURL('image/png');
                
                // Set the preview image source to the resized square image
                document.getElementById('profilePreview').src = dataUrl;
            };
        };
        
        reader.readAsDataURL(file);
    }
}
function confirmDelete() {
        return confirm('Are you sure you want to delete your account? This action cannot be undone.');
    }
document.addEventListener("DOMContentLoaded", function() {
            const deleteBtn = document.getElementById('deleteButton');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete your profile?')) {
                        e.preventDefault();
                    }
                });
            }
        });


</script>
<script>
document.getElementById('notificationDropdown').addEventListener('click', function () {
                // Send an AJAX request to mark notifications as read
                fetch('mark_notification_read.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the badge count to 0
                        document.getElementById('notificationCount').textContent = '';
                    }
                })
                .catch(error => console.error('Error:', error));
            });
</script>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
</body>
</html>
