<?php
session_start();
include 'db.php';

// Ensure only logged-in users can view
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

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
    <link rel="stylesheet" href="style2.css">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<body>
<!DOCTYPE html>
<html lang="en">
    <style>
        .navbar-toggler {
        border-color: rgba(215, 211, 195, 1); /* Change border color */
    }

    .navbar-toggler-icon {
        background-color: rgba(215, 211, 195, 1); /* Change the button icon color */
    }
    </style>
    <head>
    <title>Home Page</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
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
            <!-- Header-->
            <header class="py-5" style="background-image: url('BG1.png'); background-size: cover; background-position: center;">
            <div class="container px-5 pb-5">
                <div class="row gx-5 align-items-center">
                    <div class="col-xxl-5">
                        <!-- Header text content-->
                        <div class="text-center text-xxl-start">
                            <div class="quote-container" id="quote-container">
                                Loading motivational quote...
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-7">
                        <!-- Header profile picture-->
                        <div class="d-flex justify-content-center mt-5 mt-xxl-0">
                            <div class="image-container" id="image-container">
                                <img src="" alt="Workout Image" id="workout-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

            <!-- About Section-->
            <section class="bg-light py-5">
                <div class="container px-5">
                    <div class="row gx-5 justify-content-center">
                        <div class="col-xxl-8">
                            <div class="text-center my-5">
                                <h2 class="display-5 fw-bolder"><span class="text-gradient d-inline">About Fit-Mate</span></h2>
                                <p class="lead fw-light mb-4">Effortless Workout Scheduling, Progress Monitoring, and Motivation for your Fitness Journey</p>
                                <p class="text-muted">to provide a platform for progress tracking, scheduling workouts, and managing their motivation. it aims to enhance user engagement through features that foster long-term adherence to fitness routines </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <!-- Footer-->
        <!-- Bootstrap core JS-->
        
 <script>

    src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    src="js/scripts.js"

    async function fetchMotivationalQuote() {
        const quoteContainer = document.getElementById('quote-container');
        const apiKey = ''; //change to your API key
        try {
            const response = await fetch('https://api.api-ninjas.com/v1/quotes?category=fitness', {
                method: 'GET',
                headers: { 'X-Api-Key': apiKey }
            });
            if (!response.ok) {
                throw new Error(`API Error: ${response.status} ${response.statusText}`);
            }
            const data = await response.json();
            if (data.length > 0) {
                quoteContainer.innerHTML = `"${data[0].quote}" — ${data[0].author}`;
                // Apply the semi-transparent black background with padding
                quoteContainer.style.color = 'black'; // Light color text for readability
                quoteContainer.style.padding = '20px'; // Add some padding around the text
                quoteContainer.style.borderRadius = '10px'; // Optional: Round the corners
                quoteContainer.style.textAlign = 'center'; // Center the text
                quoteContainer.style.fontWeight = 'bold';
                quoteContainer.style.fontSize = '25px'; // Increase the font size


            } else {
                quoteContainer.innerHTML = 'No quotes found. Stay motivated!';
            }
        } catch (error) {
            console.error('Error fetching quote:', error);
            quoteContainer.innerHTML = 'Failed to load quote. Keep pushing forward!';
        }
    }


        async function fetchWorkoutImage() {
            const imageElement = document.getElementById('workout-image');
            const apiKey = ''; //change to your API key
            try {
                const response = await fetch(`https://pixabay.com/api/?key=${apiKey}&q=exercise+fitness&image_type=photo&per_page=10`);
                if (!response.ok) {
                    throw new Error(`API Error: ${response.status} ${response.statusText}`);
                }
                const data = await response.json();
                if (data.hits.length > 0) {
                    const randomImage = data.hits[Math.floor(Math.random() * data.hits.length)];
                    imageElement.src = randomImage.webformatURL;

                    // Apply inline styles directly to the image element
                    imageElement.style.width = '390px'; // Fixed width
                    imageElement.style.height = '300px'; // Fixed height
                    imageElement.style.objectFit = 'cover'; // Ensure image scales properly
                } else {
                    imageElement.src = '';
                }
            } catch (error) {
                console.error('Error fetching image:', error);
                imageElement.src = '';
            }
        }

        fetchMotivationalQuote();
        setInterval(fetchMotivationalQuote, 10000);

        fetchWorkoutImage();
        setInterval(fetchWorkoutImage, 10000);
    </script>

<script>
             document.getElementById('notificationDropdown').addEventListener('click', function () {
                // Send an AJAX request to mark notifications as read
                fetch('mark_notification_read.php', { method: 'POST' })
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
