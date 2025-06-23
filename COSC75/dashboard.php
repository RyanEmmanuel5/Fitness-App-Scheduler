<?php
session_start(); // Start the session to access the session variables

// Include the database connection file
include 'db.php'; // Ensure this file defines the $conn variable for database access

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // If the user is not logged in, redirect to login page
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username']; // Get the logged-in username

// Prepare statement to fetch profile picture
$stmt = $conn->prepare("SELECT profile_picture FROM users_tbl WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set default profile picture if none is available
$profilePicture = !empty($user['profile_picture']) ? $user['profile_picture'] : 'uploads/default.png';

?>
<?php
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
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="styles3.css" rel="stylesheet" />
    </head>
    <style>
     
    </style>
    <body>
        <!-- Responsive navbar-->
        <!-- Navigation-->
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

        <!-- Header-->
        <header class="py-5" style="background: rgba(215, 211, 195, 1)">
    <div class="container px-lg-5">
        <div class="p-4 p-lg-5 rounded-3 text-center" style="background: rgba(36, 36, 37, 1);">
            <div class="m-4 m-lg-5" style="color: rgba(215, 211, 195, 1);">
                <!-- Display the Profile Picture -->
                <img src="<?php echo htmlspecialchars($profilePicture); ?>" 
                    alt="Profile Picture" 
                    class="rounded-circle mb-4" 
                    style="width: 150px; height: 150px; object-fit: cover; border: 3px solid rgba(215, 97, 60, 1);">
                <!-- Welcome Message -->
                <h1 class="display-5 fw-bold">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p class="fs-4">"Every rep, every step, and every drop of sweat brings you closer to the best version of yourself. Start moving today for a healthier, happier tomorrow!"</p>
                <a class="btn btn-lg" href="profile.php" style="background-color: rgba(215, 97, 60, 1);">Profile</a>
            </div>
        </div>
    </div>
</header>
        <!-- Page Content-->
        <header class="py-5" style="background-image: url('BG1.png'); background-size: cover; background-position: center;">
        <section class="pt-4">
            <div class="container px-lg-5">
                <!-- Page Features-->
                <div class="row gx-lg-5">
                    <div class="col-lg-6 col-xxl-4 mb-5">
                        <div class="card bg-light border-0 h-100" >
                        <a href="schedule.php" style="text-decoration: none;">
                            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0" style="background: rgba(36, 36, 37, 1);">
                                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4" style="background-color: rgba(215, 97, 60, 1)!important;">
                                    <i class="bi bi-calendar-plus" style="color: rgba(215, 211, 195, 1)"></i>
                                </div>
                                <h2 class="fs-4 fw-bold" style="color: rgba(215, 211, 195, 1);">Schedule</h2>
                                <p class="mb-0" style="color: rgba(215, 211, 195, 1);">SET WHAT FEELS RIGHT FOR YOU</p>
                            </div>
                        </a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xxl-4 mb-5">
                        <a href="report.php" class="text-decoration-none text-dark">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0" style="background: rgba(36, 36, 37, 1);">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4" style="background-color: rgba(215, 97, 60, 1)!important;">
                                        <i class="bi bi-clipboard-data" style="color: rgba(215, 211, 195, 1)"></i>
                                    </div>                                
                                    <h2 class="fs-4 fw-bold" style="color: rgba(215, 211, 195, 1);">Reports</h2>
                                    <p class="mb-0" style="color: rgba(215, 211, 195, 1);">SEE YOUR WORKOUT JOURNEY</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-6 col-xxl-4 mb-5">
                        <a href="achievements.php" class="text-decoration-none text-dark">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0" style="background: rgba(36, 36, 37, 1);">
                                    <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4" style="background-color: rgba(215, 97, 60, 1)!important;">
                                        <i class="bi bi-award" style="color: rgba(215, 211, 195, 1)"></i>
                                    </div>   
                                    <h2 class="fs-4 fw-bold" style="color: rgba(215, 211, 195, 1);">Achievements</h2>
                                    <p class="mb-0" style="color: rgba(215, 211, 195, 1);">EVERY EFFORT EARNS A STORY</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- More content here... -->
                </div>
            </div>
        </section>
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
        <!-- Footer-->
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>
