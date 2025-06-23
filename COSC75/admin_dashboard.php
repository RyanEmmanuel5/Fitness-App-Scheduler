<?php
session_start();
include 'db.php';

// Ensure only logged-in users can view
if (!isset($_SESSION['username'])) {
    header('Location: admin.php');
    exit();
}
// Query to count the total users from the `users_tbl` table
$query = "SELECT COUNT(*) as total_users FROM users_tbl";
$result = $conn->query($query); // Execute the query

// Fetch the total user count
if ($result) {
    $row = $result->fetch_assoc();
    $totalUsers = $row['total_users'];
} else {
    $totalUsers = 0; // Fallback in case of query failure
}
// Query to count the total number of events
$query = "SELECT COUNT(*) as total_events FROM schedule_tbl";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$totalEvents = $row['total_events'];
?>
<?php
require_once 'db.php'; // Include your database connection file

// Define an array for all months
$allMonths = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// Initialize an array to hold registration counts for each month
$registrations = array_fill(0, 12, 0);

// Fetch data for the line chart
$query = "SELECT DATE_FORMAT(date_created, '%c') AS month_num, COUNT(*) AS registrations 
          FROM users_tbl 
          WHERE YEAR(date_created) = YEAR(CURDATE()) 
          GROUP BY MONTH(date_created)";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $monthIndex = $row['month_num'] - 1; // Convert month number to zero-based index
        $registrations[$monthIndex] = $row['registrations'];
    }
}

// Encode the data for JavaScript
$chartData = [
    'labels' => $allMonths,
    'values' => $registrations
];
?>
<?php
// Database connection
include('db.php');

// Number of rows per page
$rows_per_page = 10;

// Get the current page number from the URL, default is 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset
$offset = ($page - 1) * $rows_per_page;

// Get the search term
$searchTerm = isset($_POST['search']) ? strtolower(mysqli_real_escape_string($conn, $_POST['search'])) : '';

// Total query with search
$total_query = "SELECT COUNT(ID) as total FROM users_tbl WHERE LOWER(USERNAME) LIKE '%$searchTerm%' OR LOWER(EMAIL) LIKE '%$searchTerm%'";
$total_result = mysqli_query($conn, $total_query);
$total_rows = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_rows / $rows_per_page);

// Fetch data with search and pagination
$query = "SELECT * FROM users_tbl WHERE LOWER(USERNAME) LIKE '%$searchTerm%' OR LOWER(EMAIL) LIKE '%$searchTerm%' LIMIT $offset, $rows_per_page";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin Dashboard</title>

    <!-- CSS -->
    <link rel="stylesheet" href="style2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="astyle1.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <style>
        .navbar-toggler {
            border-color: rgba(215, 211, 195, 1);
        }

        .navbar-toggler-icon {
            background-color: rgba(215, 211, 195, 1);
        }

        body {
            background: url(ABG1.png);
        }
        table {
    width: 100%;
    border-collapse: collapse; /* Collapse borders between cells */
}

th, td {
    border: 1px solid rgba(36, 36, 37, 1); /* Border around cells */
    padding: 10px;
    text-align: left;
}

tr:nth-child(even) {
    background-color: rgba(215, 211, 195, 0.5); /* Alternating row colors */
}
.pagination .page-item .page-link {
    color: black;
}

.pagination .page-item.active .page-link {
    background-color: rgba(215, 97, 60, 1);
    color: white;
}
    </style>
</head>

<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
        <!-- Navigation -->
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
                        <li class="nav-item"><a class="nav-link" href="alogout.php" style="color: rgba(215, 211, 195, 1);">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content Area with 4 responsive child containers -->
        <div class="container my-4">
            <div class="row gy-3">
                <!-- First Child Container -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="p-3 border rounded text-center" style="background-color: rgba(215, 211, 195, 1);">
                        <h4 style="background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); border-radius: 5px; padding: 5px">Total Users</h4>
                        <p class="user-count">
                            <i class="fa fa-users user-icon"></i>
                            <span id="userCount">0</span>
                        </p>
                    </div>
                </div>
                <!-- Second Child Container -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="p-3 border rounded text-center" style="background-color: rgba(215, 211, 195, 1);">
                        <h4 style="background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); border-radius: 5px; padding: 5px">Total Workouts</h4>
                        <p class = "user-count">
                            <i class="fa fa-calendar-check user-icon"></i>
                            <span id="eventCount">0</span>
                        </p>
                    </div>
                </div>
                <!-- Third Child Container -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="p-3 border rounded text-center" style="background-color: rgba(215, 211, 195, 1);">
                        <h4 style="background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); border-radius: 5px; padding: 5px">Completion Rate</h4>
                        <p class = "user-count">
                            <i class="fas fa-tachometer-alt"></i>
                            <span id="completionRate"  style="font-size: 40px;">Completion Rate: Loading...</span> <!-- Placeholder for the completion rate -->
                        </p>
                    </div>
                </div>
                <!-- Fourth Child Container -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="p-3 border rounded text-center" style="background-color: rgba(215, 211, 195, 1);">
                        <h4 style="background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); border-radius: 5px; padding: 5px">Gender Distribution</h4>
                        <canvas id="genderBarChart"></canvas>
                    </div>
                </div>

            </div>
        </div>
        <!-- Second Main Content Area with 2 responsive child containers -->
        <div class="container my-4">
            <div class="row gy-3">
                <div class="col-12 col-md-6">
                <div class="p-3 border rounded text-center" style="background-color: rgba(215, 211, 195, 1);">
                    <h4 style="background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); border-radius: 5px; padding: 5px; margin-bottom: -20px;">User Registration Over the Year</h4>
                        <canvas id="userRegistrationChart" width="400" height="150"></canvas>
                    </div>
                </div>
                <!-- Second Child Container -->
                <div class="col-12 col-md-6">
                    <div class="p-3 border rounded text-center" style="background-color: rgba(215, 211, 195, 1);">
                        <h4 style="background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); border-radius: 5px; padding: 5px; margin-bottom: 30px;">User Age Group</h4>
                        <!-- Bar Graph -->
                        <canvas id="ageBarChart" width="400" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third Main Content Area with 1 responsive child container -->
        <div class="container my-4" style="max-width: 1320px; width: 100%; margin: 0 auto;">
        <div class="row gy-3">
            <div class="col-12">
                <div class="p-4 border rounded" style="background-color: rgba(215, 211, 195, 1); width: 100%;">
                    <div class="col-12 mb-3">
                    <h4 style="background-color: rgba(36, 36, 37, 1); color: rgba(215, 97, 60, 1); border-radius: 5px; padding: 5px; text-align: center;">Users Table</h4>
                    <input type="text" id="search" class="form-control" placeholder="Search by Username or Email">
                    </div>
                        <table class="table">
                            <thead style="background-color: rgba(36, 36, 37, 1); color: white;">
                                <tr>
                                    <th style="text-align: center;">ID</th>
                                    <th style="text-align: center;">USERNAME</th>
                                    <th style="text-align: center;">EMAIL</th>
                                    <th style="text-align: center;">FIRST NAME</th>
                                    <th style="text-align: center;">LAST NAME</th>
                                    <th style="text-align: center;">BIRTHDATE</th>
                                    <th style="text-align: center;">AGE</th>
                                    <th style="text-align: center;">GENDER</th>
                                    <th style="text-align: center;">DATE JOINED</th>
                                    <th style="text-align: center;">LAST LOGIN</th>
                                    <th style="text-align: center;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr style='height: 30px;'>
                                            <td>{$row['ID']}</td>
                                            <td>{$row['USERNAME']}</td>
                                            <td>{$row['EMAIL']}</td>
                                            <td>{$row['F_NAME']}</td>
                                            <td>{$row['L_NAME']}</td>
                                            <td>{$row['BIRTHDATE']}</td>
                                            <td>{$row['AGE']}</td>
                                            <td>{$row['GENDER']}</td>
                                            <td>{$row['DATE_CREATED']}</td>
                                            <td>{$row['last_login']}</td>
                                            <td><button class='btn btn-danger delete-btn' data-id='{$row['ID']}'>Delete</button></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='11' class='text-center'>No records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <?php
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    $active = ($i == $page) ? 'active' : '';
                                    echo "<li class='page-item $active'>
                                            <a class='page-link' href='?page=$i'>$i</a>
                                        </li>";
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const totalUsers = <?php echo $totalUsers; ?>; // Fetch user count from PHP
        const userCountSpan = document.getElementById("userCount");
        let currentCount = 0; // Starting point for animation

        function animateCount() {
            const increment = Math.ceil(totalUsers / 100); // Calculate incremental step
            const intervalTime = 10; // Interval delay in milliseconds
            const interval = setInterval(function() {
                currentCount += increment; // Increment the count by steps
                if (currentCount >= totalUsers) {
                    currentCount = totalUsers; // Ensure it doesn't overshoot
                    clearInterval(interval); // Stop the interval once the goal is reached
                }
                userCountSpan.innerText = currentCount; // Update the displayed number
            }, intervalTime);
        }

        animateCount();
    });
    
    document.addEventListener("DOMContentLoaded", function() {
        const totalEvents = <?php echo $totalEvents; ?>; // Pass total events from PHP
        const eventCountSpan = document.getElementById("eventCount");
        let currentCount = 0;

        function animateCount() {
            const increment = Math.ceil(totalEvents / 100); 
            const intervalTime = 10;

            const interval = setInterval(function() {
                currentCount += increment;

                if (currentCount >= totalEvents) {
                    currentCount = totalEvents;
                    clearInterval(interval);
                }

                eventCountSpan.innerText = currentCount; 
            }, intervalTime);
        }

        animateCount();
    });

    </script>
    <script>
        fetch('fetch_gender_data.php') // Path should match the actual file location
            .then(response => response.json()) // Parse JSON response
            .then(data => {
                const ctx = document.getElementById('genderBarChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Male', 'Female', 'Other/Null'],
                        datasets: [{
                            data: [data.male_count, data.female_count, data.other_count],
                            backgroundColor: ['#d7613c80', '#4bc0c080', '#6c757d'],
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        aspectRatio: 3.5,  // Controls the overall aspect ratio
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 8 // Set font size for x-axis labels
                                    }
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: 8 // Set font size for y-axis labels
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false // Hide the legend if you don't want it
                            }
                        },
                        // Adjust bar size and space between bars
                        datasets: [{
                            barPercentage: 0.5,  // Makes the bars thinner
                            categoryPercentage: 0.8,  // Reduces the space between bars
                        }],
                        // Font size adjustments for chart title or labels
                        elements: {
                            bar: {
                                borderWidth: 1
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching gender data:', error));
    </script>
    <script>
    // Fetch data from the server (you need to update the API endpoint)
    fetch('fetch_schedule_data.php')  // Adjust the path to your actual endpoint
        .then(response => response.json()) // Parse JSON response
        .then(data => {
            // Assuming data contains an array of status values
            const statuses = data.map(item => item.status);
            
            // Count 'finished' and 'missed' occurrences
            const finishCount = statuses.filter(status => status === 'finished').length;
            const missedCount = statuses.filter(status => status === 'missed').length;

            // Calculate completion rate
            const totalCount = finishCount + missedCount;
            const completionRate = totalCount > 0 ? (finishCount / totalCount) * 100 : 0; // Avoid division by zero

            // Function to animate the completion rate
            const animateCompletionRate = (targetRate) => {
                const completionRateElement = document.getElementById('completionRate');
                let currentRate = 0;
                const duration = 2000; // Animation duration in milliseconds (2 seconds)
                const steps = 100; // Number of steps for the animation
                const increment = targetRate / steps; // Increment per step
                const intervalTime = duration / steps; // Time for each increment step

                // Set an interval to update the rate
                const interval = setInterval(() => {
                    currentRate += increment;
                    if (currentRate >= targetRate) {
                        currentRate = targetRate; // Ensure it doesn't exceed the target
                        completionRateElement.textContent = ` ${currentRate.toFixed(2)}%`;
                        clearInterval(interval); // Stop the interval once target is reached
                    } else {
                        completionRateElement.textContent = ` ${currentRate.toFixed(2)}%`;
                    }
                }, intervalTime); // Execute every `intervalTime` milliseconds
            };

            // Start the animation
            animateCompletionRate(completionRate);
        })
        .catch(error => {
            console.error('Error fetching schedule data:', error);
            const completionRateElement = document.getElementById('completionRate');
            completionRateElement.textContent = 'Completion Rate: Error';
        });
    </script>
<script>
        document.addEventListener("DOMContentLoaded", function () {
            // Data from PHP
            const chartData = <?php echo json_encode($chartData); ?>;

            const ctx = document.getElementById('userRegistrationChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels, // Months
                    datasets: [{
                        label: 'Users Registered',
                        data: chartData.values, // Registration counts
                        borderColor: 'rgba(215, 97, 60, 1)',
                        backgroundColor: 'rgba(215, 97, 60, 1)',
                        borderWidth: 1,
                        tension: 0.4 // Smooth curve
                    }]
                },
                options: {
                    responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12// Minimized font size for legend
                            }
                        }
                    },
                    title: {
                        display: true,
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 9 // Minimized font size for x-axis labels
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10 // Minimized font size for y-axis labels
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
   <script>
    fetch('fetch_age_data.php')  // Ensure this path matches your PHP script location
    .then(response => response.json())  // Parse JSON response
    .then(data => {
        const ctx = document.getElementById('ageBarChart').getContext('2d');
        const ageGroups = Object.keys(data);
        const counts = Object.values(data);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ageGroups,
                datasets: [{
                    data: counts,
                    backgroundColor: ['#d7613c80', '#4bc0c080', '#6c757d', '#007bff80', '#28a74580', '#ffc10780']
                }]
            },
            options: {
                responsive: true,
                aspectRatio: 3.5,
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 8
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 8
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    })
    .catch(error => console.error('Error fetching age data:', error));
</script>
<script>
 document.getElementById('search').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr');

    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let rowVisible = false;

        for (let j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().includes(searchValue)) {
                rowVisible = true;
                break;
            }
        }
        row.style.display = rowVisible ? '' : 'none';
    });

    // Update pagination
    const filteredRows = [...rows].filter(row => row.style.display !== 'none');
    const totalPages = Math.ceil(filteredRows.length / <?php echo $rows_per_page; ?>);

    const currentPage = Math.ceil(filteredRows.length / <?php echo $rows_per_page; ?>);
    const paginationLinks = document.querySelectorAll('.pagination .page-link');

    paginationLinks.forEach(link => {
        const pageNum = parseInt(link.textContent, 10);
        link.parentElement.classList.toggle('active', pageNum === currentPage);
    });
});


    // Delete button functionality
    document.getElementById('tableBody').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('delete-btn')) {
        const userId = e.target.getAttribute('data-id');

        if (confirm(`Are you sure you want to delete user with ID: ${userId}?`)) {
            fetch(`admin_delete_user.php?id=${userId}`, { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        e.target.closest('tr').remove();
                        // Automatically refresh the page
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    }
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
</body>

</html>
