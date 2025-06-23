<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
include 'db.php';
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
<?php
// Include database connection
include 'db.php';

// Assume user is logged in and username is stored in session
if (!isset($_SESSION['username'])) {
    echo "You are not logged in.";
    exit;
}

$username = $_SESSION['username'];

// Fetch Finished Workouts for this user
$finishedQuery = "SELECT date, title FROM schedule_tbl WHERE status = 'finished' AND username = ?";
$stmt = $conn->prepare($finishedQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$finishedResult = $stmt->get_result();
$finishedWorkouts = [];
if ($finishedResult->num_rows > 0) {
    while ($row = $finishedResult->fetch_assoc()) {
        $finishedWorkouts[] = $row;
    }
}
$stmt->close();

// Fetch Missed Workouts for this user
$missedQuery = "SELECT date, title FROM schedule_tbl WHERE status = 'missed' AND date < CURDATE() AND username = ?";
$stmt = $conn->prepare($missedQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$missedResult = $stmt->get_result();
$missedWorkouts = [];
if ($missedResult->num_rows > 0) {
    while ($row = $missedResult->fetch_assoc()) {
        $missedWorkouts[] = $row;
    }
}
$stmt->close();
$conn->close();
?>
<?php
//for duration
include 'db.php'; // Database connection file

// Assuming $_SESSION['username'] stores the logged-in user
$username = $_SESSION['username'];

// Query to fetch the last 5 workouts
$sql = "SELECT date, TIMESTAMPDIFF(MINUTE, start_time, end_time) AS duration 
        FROM schedule_tbl 
        WHERE username = ? AND status = 'finished'
        ORDER BY date_created DESC 
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$workout_dates = [];
$workout_durations = [];

// Fetch data
while ($row = $result->fetch_assoc()) {
    $workout_dates[] = $row['date'];
    $workout_durations[] = $row['duration'];
}

// Close connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles4.css">
    <style>
/* General Body Styling */
body {
    background-color: #f7f7f7;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Navbar */
.navbar {
    background-color: rgba(36, 36, 37, 1);
}

.navbar-brand img {
    height: 28px;
    width: auto;
}

.navbar-nav .nav-link {
    color: rgba(215, 211, 195, 1);
    font-weight: bold;
}

/* Dashboard Container */
.dashboard-container {
    display: flex;
    gap: 15px;
    background: rgba(36, 36, 37, 1);
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #333;
    margin: 20px;
    height: 83vh; /* Full screen height */
    overflow-y: auto; /* Enable vertical scroll when content exceeds the viewport height */
}

/* Workout History Container */
.workout-history-container {
    background-color: rgba(215, 211, 195, 1);
    padding: 20px;
    border-radius: 10px;
    width: 40%; /* Adjust width as needed */
    height: 100%; /* Set a maximum height */
    overflow-y: auto; /* Enable vertical scroll when content exceeds max-height */
}

.workout-history-container h2 {
    font-weight: bold;
    text-align: center;
    color: rgba(215, 97, 60, 1);
    background: rgba(36, 36, 37, 1);
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-size: 16px;
}

/* Right Section */
.right-section {
    flex: 1.5;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* Workout Frequency and Duration Containers */
.workout-frequency-container,
.duration-container {
    background-color: rgba(215, 211, 195, 1);
    padding: 15px;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    gap: 20px;
}

/* Left and Right Columns */
.left-column, .right-column {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}


/* Workout Frequency Header */
.workout-frequency-container h2,
.duration-container h2 {
    font-weight: bold;
    text-align: center;
    color: rgba(215, 97, 60, 1);
    background: rgba(36, 36, 37, 1);
    padding: 10px 0;
    border-radius: 5px;
    margin-bottom: 15px;
    width: 100%; /* Background stretches full width */
    box-sizing: border-box;
    font-size: 16px;
}

/* Canvas Styling */
canvas {
        width: 100% !important; /* Responsive canvas width */
        height: auto !important; /* Adjust height automatically */
        max-height: 250px; /* Sensible max height */
        overflow: hidden; /* Prevent canvas overflow */
    }

/* Buttons */
button {
    padding: 5px 15px;
    border: none;
    background-color: rgba(215, 97, 60, 1);
    color: rgba(36, 36, 37, 1);
    cursor: pointer;
    border-radius: 5px;
    font-size: 13px;
}

button:hover {
    color: white;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
    font-size: 14px;
}

table thead th {
    background-color: rgba(36, 36, 37, 1);
    color: white;
    padding: 10px;
    text-align: center;
}

table tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}

table tbody tr:nth-child(even) {
    background-color: #ffffff;
}

table td, table th {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

table tbody tr:hover {
    background-color: #f1f1f1;
}

/* Notification Badge */
#notificationCount {
    color: white !important;
}

/* Hidden Tables */
.hidden {
    display: none;
}

/* Media Queries for Responsiveness */
@media screen and (max-width: 768px) {
    .dashboard-container {
        flex-direction: column;
        height: auto; /* Adjust height for mobile */
    }

    .workout-history-container {
        width: 100%; /* Full width on smaller screens */
        max-height: 400px; /* Adjust max height */
    }

    .workout-frequency-container,
    .duration-container {
        flex-direction: column; /* Stack columns vertically */
        gap: 10px;
    }

    .left-column, .right-column {
        width: 100%; /* Full width for each column */
    }

    canvas {
        max-height: 200px; /* Adjust graph height for mobile screens */
    }

    button {
        font-size: 14px; /* Larger font for easier tap */
    }
}
.text-container {
    background-color: rgba(215, 211, 195, 1);
    padding: 15px;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Divides the container into three equal columns */
    gap: 10px; /* Space between the columns */
    height: 270px;
}
.column {
  background-color: transparent;
  padding: 10px;
  text-align: center;
}
.text-container h4 {
    font-weight: bold;
    text-align: center;
    color: rgba(215, 97, 60, 1);
    background: rgba(36, 36, 37, 1);
    padding: 10px;
    border-radius: 5px;
    font-size: 20px;
}


/* Set fixed size for the canvas */
#completionChart {
    width: 160px !important;  /* Set the desired width */
    height: 160px !important; /* Set the desired height */
    justify-content: center;   /* Center horizontally */
    align-items: center;       /* Center vertically */
    margin-left: 50px;
}

</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg py-3" style="background-color: rgba(36, 36, 37, 1);">
    <div class="container px-5">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
            style="border-color: rgba(215, 211, 195, 1);">
            <span class="navbar-toggler-icon" style="background-color: rgba(215, 211, 195, 1);"></span>
        </button>

        <a class="navbar-brand" href="#">
            <img src="logo1.png" alt="Logo" style="height: 40px; width: auto;">
        </a>

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

<!-- Dashboard Section -->
<div class="dashboard-container">
<div class="workout-history-container">
    <!-- Header and Buttons -->
    <div class="header-buttons">
        <h2>Workout History</h2>
        <div class="buttons">
            <button onclick="showTable('finished')">Finished</button>
            <button onclick="showTable('missed')">Missed</button>
        </div>
    </div>

    <!-- Finished Table -->
    <table id="finishedTable">
        <thead>
            <tr>
                <th colspan="2">Finished Workouts</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dynamic Content -->
            <?php if (!empty($finishedWorkouts)) {
                foreach ($finishedWorkouts as $workout) {
                    echo "<tr><td>" . htmlspecialchars($workout['date']) . "</td><td>" . htmlspecialchars($workout['title']) . "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No finished workouts</td></tr>";
            } ?>
        </tbody>
    </table>

    <!-- Missed Table -->
    <table id="missedTable" class="hidden">
        <thead>
            <tr>
                <th colspan="2">Missed Workouts</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dynamic Content -->
            <?php if (!empty($missedWorkouts)) {
                foreach ($missedWorkouts as $workout) {
                    echo "<tr><td>" . htmlspecialchars($workout['date']) . "</td><td>" . htmlspecialchars($workout['title']) . "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No missed workouts</td></tr>";
            } ?>
        </tbody>
    </table>
    </div>
    <div class="right-section">
        <div class="workout-frequency-container">
            <div class="left-column">
                <div class="header-buttons">
                    <h2>Workout Frequency</h2>
                    <canvas id="eventChart" width="400" height="175"></canvas>
                    <div class="buttons">
                        <button id="btn-weekly" class="toggle-btn">Weekly</button>
                        <button id="btn-monthly" class="toggle-btn">Monthly</button>
                    </div>
                </div>
            </div>
            <div class="right-column">
                <div class="header-buttons">
                    <h2>Duration</h2>
                </div>
                <canvas id="durationChart"></canvas>
            </div>
        </div>
        <div class="text-container">
            <div class="column">
                <h4>Total Workout Hours</h4>
                <i class="fa-solid fa-clock" style="font-size: 3em;  margin-top: 20px"></i>
                <p id="totalHours" style="font-size: 3em; font-weight: bold; color:rgba(215, 97, 60, 1);">0 hours</p>
            </div>
            <div class="column">
                <h4>Average Workout Time</h4>
                <i class="fa-solid fa-stopwatch" style="font-size: 3em;  margin-top: 20px"></i>
                <p id="averageWorkoutTime" style="font-size: 3em; font-weight: bold; color:rgba(215, 97, 60, 1);">Calculating...</p>
            </div>
            <div class="column">
                <h4>Completion Rate</h4>
                <div id="chartContainer" style="width: 100%; height: 300px;">
                    <canvas id="completionChart"></canvas>
                </div>            
            </div>
        </div>
    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
// JavaScript to toggle tables
function showTable(type) {
            document.getElementById('finishedTable').classList.add('hidden');
            document.getElementById('missedTable').classList.add('hidden');

            if (type === 'finished') {
                document.getElementById('finishedTable').classList.remove('hidden');
            } else if (type === 'missed') {
                document.getElementById('missedTable').classList.remove('hidden');
            }
        }
        
//for bargraph frequency

document.getElementById('btn-weekly').addEventListener('click', () => {
    fetch('get_event_counts.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            updateChart('Weekly Events', data.weekly.map(item => `Week ${item.week}`), data.weekly.map(item => item.count));
        });
});

document.getElementById('btn-monthly').addEventListener('click', () => {
    fetch('get_event_counts.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            updateChart('Monthly Workouts', data.monthly.map(item => `Month ${item.month}`), data.monthly.map(item => item.count));
        });
});

document.addEventListener('DOMContentLoaded', function () {
    // Fetch data for the default view (weekly events)
    fetch('get_event_counts.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            const labels = data.weekly.map(item => `Week ${item.week}`);
            const counts = data.weekly.map(item => item.count);
            const label = 'Weekly Workout';

            updateChart(label, labels, counts);
        });
});

function initializeChart() {
    const ctx = document.getElementById('eventChart').getContext('2d');
    chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: '',
                data: [],
                backgroundColor: 'rgba(215, 97, 60, 0.3)',
                borderColor: 'rgba(215, 97, 60, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateChart(label, labels, data) {
    chart.data.labels = labels;
    chart.data.datasets[0].label = label;
    chart.data.datasets[0].data = data;
    chart.update();
}

// Initialize the chart on page load
initializeChart();

//for bargraph duration

// Sample PHP data fetched via backend
const labels = <?php echo json_encode($workout_dates); ?>; // X-axis labels (e.g., workout dates)
    const durations = <?php echo json_encode($workout_durations); ?>; // Y-axis values (durations)

    // Bar Graph Configuration
    const ctx = document.getElementById('durationChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels, // Workout dates
            datasets: [{
                label: 'Workout Duration (minutes)',
                data: durations, // Workout durations
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Minutes'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Workout Date'
                    }
                }
            }
        }
    });

// for total work hours
document.addEventListener('DOMContentLoaded', function () {
    fetch('get_finished_hours.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('totalHours').textContent = 'Error: ' + data.error;
            } else {
                const totalHours = parseInt(data.total_hours) || 0;
                animateCount(0, totalHours, document.getElementById('totalHours'));
            }
        })
        .catch(error => {
            document.getElementById('totalHours').textContent = 'Error fetching data';
            console.error(error);
        });

    function animateCount(start, end, element) {
        let current = start;
        const duration = 1000; // Total animation duration in milliseconds
        const stepTime = Math.abs(Math.floor(duration / (end - start))); // Step time
        const timer = setInterval(() => {
            current++;
            element.textContent = current + ' hours';
            if (current >= end) {
                clearInterval(timer);
                element.textContent = end + ' hours'; // Ensure it ends exactly at total
            }
        }, stepTime);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    fetch('get_average_workout_time.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('averageWorkoutTime').textContent = 'Error: ' + data.error;
            } else {
                const averageTime = parseFloat(data.average_hours) || 0;
                animateCount(0, averageTime, document.getElementById('averageWorkoutTime'));
            }
        })
        .catch(error => {
            document.getElementById('averageWorkoutTime').textContent = 'Error fetching data';
            console.error(error);
        });

    function animateCount(start, end, element) {
        let current = start;
        const duration = 10; // Duration of the animation in milliseconds
        const stepTime = Math.floor(duration / (end - start)); // Time per step
        const timer = setInterval(() => {
            current += 0.01;
            const hours = Math.floor(current);
            const minutes = Math.round((current - hours) * 60);
            element.textContent = `${hours}h ${minutes}m`;
            if (current >= end) {
                clearInterval(timer);
                const finalHours = Math.floor(end);
                const finalMinutes = Math.round((end - finalHours) * 60);
                element.textContent = `${finalHours}h ${finalMinutes}m`; // Ensures it ends exactly
            }
        }, stepTime);
    }
});

// for completion rate

document.addEventListener('DOMContentLoaded', function () {
    fetch('get_completion_rate.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                const errorElement = document.getElementById('completionRate');
                if (errorElement) {
                    errorElement.textContent = 'Error: ' + data.error;
                }
            } else {
                const completionRate = data.completion_rate;
                const canvas = document.getElementById('completionChart');
                
                // Set canvas size explicitly via JavaScript (in case CSS does not work as expected)
                canvas.width = 400;  // Width in pixels
                canvas.height = 250; // Height in pixels

                const ctx = canvas.getContext('2d');
                
                // Create the chart
                const chart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Finished', 'Missed'],
                        datasets: [{
                            data: [completionRate, 100 - completionRate], // The completion rate data
                            backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(215, 97, 60, 0.3)']
                        }]
                    },
                    options: {
                        responsive: true,  // This will make it responsive
                        maintainAspectRatio: false,  // Allow the chart to stretch
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(tooltipItem) {
                                        // The raw value is already a percentage, so display it directly
                                        return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2) + '%';
                                    }
                                }
                            }
                        }
                    }
                });

                // Force the chart to resize and redraw, this can be triggered when needed
                window.addEventListener('resize', function () {
                    chart.resize();  // Manually trigger the resize on window resize
                    chart.update();  // Update the chart (forces a re-render)
                });
            }
        })
        .catch(error => {
            const errorElement = document.getElementById('completionRate');
            if (errorElement) {
                errorElement.textContent = 'Error fetching data';
            }
            console.error(error);
        });
});

</script>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
