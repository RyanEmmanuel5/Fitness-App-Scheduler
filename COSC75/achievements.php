<?php
session_start();
include 'db.php';

// Ensure only logged-in users can view
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch achievements for the logged-in user
function getAchievements($username) {
    global $conn; // Using mysqli connection

    $stmt = $conn->prepare(
        "SELECT a.title, a.achievement_condition, a.description, a.achievement_type, ua.date_achieved 
         FROM user_achievements_tbl ua 
         JOIN achievements_tbl a ON ua.achievement_id = a.achievement_id 
         WHERE ua.username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $achievements = $result->fetch_all(MYSQLI_ASSOC);

    return $achievements;
}

// Fetch all achievements
function getAllAchievements() {
    global $conn;

    $stmt = $conn->prepare("SELECT title, achievement_condition, achievement_type FROM achievements_tbl");
    $stmt->execute();
    $result = $stmt->get_result();
    $allAchievements = $result->fetch_all(MYSQLI_ASSOC);

    return $allAchievements;
}

$username = $_SESSION['username'];
$achievements = getAchievements($username);
$allAchievements = getAllAchievements();

// Fetch notifications for the logged-in user
$stmt = $conn->prepare("SELECT message, status FROM notifications_tbl WHERE username = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

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
    <link rel="stylesheet" href="style2.css">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>Achievements</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        body {
            background: url(BG1.png);
        }
        .achievement-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .achievement-item {
            flex: 1 1 calc(33.33% - 20px);
            background-color: rgba(36, 36, 37, 1);
            color: rgba(215, 211, 195, 1);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .achievement-item h5 {
            margin-bottom: 10px;
            color: rgba(215, 97, 60, 1); /* Updated color */
            font-weight: bold;
        }
        .achievement-item p {
            margin-bottom: 10px;
        }
        .achievement-item small {
            font-size: 0.8rem;
            color: rgba(200, 200, 200, 1);
        }
        .navbar-toggler {
            border-color: rgba(215, 211, 195, 1); /* Change border color */
        }
        .navbar-toggler-icon {
            background-color: rgba(215, 211, 195, 1); /* Change the button icon color */
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
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
                        <li class="nav-item"><a class="nav-link" href="index.php" style="color: rgba(215, 211, 195, 1);">Home</a></li>
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


<!-- Unlocked Achievements Section -->
<div class="container mt-5">
    <h2 style="text-align: center; background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); padding: 10px; border-radius: 5px;">Unlocked Achievements</h2>

    <div class="accordion" id="unlockedAchievementsAccordion">

        <!-- All Unlocked Achievements -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="unlockedAllHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#unlockedAll" aria-expanded="true" aria-controls="unlockedAll">
                    All Unlocked Achievements
                </button>
            </h2>
            <div id="unlockedAll" class="accordion-collapse collapse show" aria-labelledby="unlockedAllHeading">
                <div class="accordion-body">
                    <div class="achievement-container">
                        <?php foreach ($achievements as $unlocked): ?>
                            <div class="achievement-item">
                                <h5>
                                    <?php
                                        $icons = [
                                            'Daily' => '🏆',
                                            'Consistency' => '💪',
                                            'Milestone' => '🎯',
                                            'Performance' => '⚡',
                                            'EasterEgg' => '🐣'
                                        ];
                                        echo isset($icons[$unlocked['achievement_type']]) ? $icons[$unlocked['achievement_type']] : '✅';
                                    ?>
                                    <?php echo htmlspecialchars($unlocked['title'], ENT_QUOTES, 'UTF-8'); ?>
                                </h5>
                                <p>
                                    <?php 
                                        echo !empty($unlocked['description']) ? htmlspecialchars($unlocked['description'], ENT_QUOTES, 'UTF-8') : 'Description not available';
                                    ?>
                                </p>
                                <p> <?php echo htmlspecialchars($unlocked['achievement_condition'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><small><strong>Date Achieved:</strong> <?php echo htmlspecialchars($unlocked['date_achieved'], ENT_QUOTES, 'UTF-8'); ?></small></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



 <!-- Locked Achievements Section -->
<div class="container mt-5">
    <h2 style="text-align: center; background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); padding: 10px; border-radius: 5px;">Locked Achievements</h2>

    <!-- Dropdown Categories -->
    <div class="accordion" id="lockedAchievementsAccordion">

        <!-- All Achievements -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="allAchievementsHeading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#allAchievements" aria-expanded="true" aria-controls="allAchievements">
                    All Locked Achievements
                </button>
            </h2>
            <div id="allAchievements" class="accordion-collapse collapse show" aria-labelledby="allAchievementsHeading">
                <div class="accordion-body">
                    <div class="achievement-container">
                        <?php foreach ($allAchievements as $achievement): ?>
                            <?php
                            $isUnlocked = false;
                            foreach ($achievements as $unlocked) {
                                if ($achievement['title'] === $unlocked['title']) {
                                    $isUnlocked = true;
                                    break;
                                }
                            }
                            ?>
                            <?php if (!$isUnlocked): ?>
                                <div class="achievement-item">
                                    <h5>
                                        <?php
                                            $icons = [
                                                'Daily' => '🏆',
                                                'Consistency' => '💪',
                                                'Milestone' => '🎯',
                                                'Performance' => '⚡',
                                                'EasterEgg' => '🐣'
                                            ];
                                            echo isset($icons[$achievement['achievement_type']]) ? $icons[$achievement['achievement_type']] : '🔍';
                                        ?>
                                        <?php echo htmlspecialchars($achievement['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h5>
                                    <p><?php echo htmlspecialchars($achievement['achievement_condition'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Achievements -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="dailyAchievementsHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dailyAchievements" aria-expanded="false" aria-controls="dailyAchievements">
                    Daily Achievements
                </button>
            </h2>
            <div id="dailyAchievements" class="accordion-collapse collapse" aria-labelledby="dailyAchievementsHeading">
                <div class="accordion-body">
                    <div class="achievement-container">
                        <?php foreach ($allAchievements as $achievement): ?>
                            <?php if ($achievement['achievement_type'] === 'Daily'): ?>
                                <div class="achievement-item">
                                    <h5>🏆 <?php echo htmlspecialchars($achievement['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p><?php echo htmlspecialchars($achievement['achievement_condition'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Milestone Achievements -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="milestoneAchievementsHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#milestoneAchievements" aria-expanded="false" aria-controls="milestoneAchievements">
                    Milestone Achievements
                </button>
            </h2>
            <div id="milestoneAchievements" class="accordion-collapse collapse" aria-labelledby="milestoneAchievementsHeading">
                <div class="accordion-body">
                    <div class="achievement-container">
                        <?php foreach ($allAchievements as $achievement): ?>
                            <?php if ($achievement['achievement_type'] === 'Milestone'): ?>
                                <div class="achievement-item">
                                    <h5>🎯 <?php echo htmlspecialchars($achievement['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p><?php echo htmlspecialchars($achievement['achievement_condition'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Achievements -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="performanceAchievementsHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#performanceAchievements" aria-expanded="false" aria-controls="performanceAchievements">
                    Performance Achievements
                </button>
            </h2>
            <div id="performanceAchievements" class="accordion-collapse collapse" aria-labelledby="performanceAchievementsHeading">
                <div class="accordion-body">
                    <div class="achievement-container">
                        <?php foreach ($allAchievements as $achievement): ?>
                            <?php if ($achievement['achievement_type'] === 'Performance'): ?>
                                <div class="achievement-item">
                                    <h5>⚡ <?php echo htmlspecialchars($achievement['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p><?php echo htmlspecialchars($achievement['achievement_condition'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Easter Egg Achievements -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="easterEggAchievementsHeading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#easterEggAchievements" aria-expanded="false" aria-controls="easterEggAchievements">
                    Easter Egg Achievements
                </button>
            </h2>
            <div id="easterEggAchievements" class="accordion-collapse collapse" aria-labelledby="easterEggAchievementsHeading">
                <div class="accordion-body">
                    <div class="achievement-container">
                        <?php foreach ($allAchievements as $achievement): ?>
                            <?php if ($achievement['achievement_type'] === 'EasterEgg'): ?>
                                <div class="achievement-item">
                                    <h5>🐣 <?php echo htmlspecialchars($achievement['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

    </main>

    <!-- Footer-->
    <!-- Bootstrap core JS-->

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
</body>
</html>
