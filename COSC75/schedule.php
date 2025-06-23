<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>
<?php
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles4.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>
    <style>
       body {
        }
        .schedule-container {
            background-color: #242425;
            color: #fff;
            border-radius: 10px;
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .schedule-header button {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .calendar-wrapper {
            background-color: #f9f9f9;
            color: #333;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            max-width: 475px;
            max-height: 400px;
        }
        <style>
        .calendar-wrapper {
            background-color: #f9f9f9;
            color: #333;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            max-width: 400px; /* Controls overall width */
            max-height: 350px; /* Controls overall height */
        }

        /* Adjust FullCalendar to fit the wrapper */
        #calendar {
            font-size: 12px; /* Reduces text size for better fit */
        }

        .fc-view-container {
            max-width: 100%; /* Ensures the calendar fits the wrapper */
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        .fc-day-header {
            width: 14.2857%; /* Ensures even spacing for all days */
            text-align: center;
            font-weight: bold;
        }

        .fc-toolbar h2 {
            font-size: 16px; /* Adjusts the header title size */
        }

        .fc-toolbar button {
            font-size: 12px; /* Adjust button text size */
            padding: 5px 10px; /* Adjust button padding */
        }

        .fc .fc-day-grid-container {
            max-height: 250px; /* Prevents the calendar grid from expanding */
            overflow-y: auto; /* Enables vertical scrolling if necessary */
        }
        .schedule-header {
            background-color: rgba(215, 211, 195, 1); /* Background color */
            text-align: center; /* Centers the content */
            padding: 10px; /* Adds padding for spacing */
            border-radius: 8px; /* Rounds the corners */
            max-width: 1000px; /* Optional: Set a width if needed */
            margin-bottom: 15px;
        }

        .schedule-header h2 {
            margin: 0; /* Removes default margin */
            font-size: 18px; /* Adjust font size */
            font-weight: bold; /* Makes the text bold */
            color: #242425; /* Dark color for the text */
        }
        .editable-highlight {
        border: 3px solid rgba(215, 97, 60, 1);
        background-color: #f8f9fa; /* Optional: Add a light background color */
    }

    .action-buttons button {
        background-color: #f05340;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        width: 100px; /* Set a fixed width for consistent size */
        text-align: center; /* Ensure text alignment */
        font-weight: normal;
        color: rgba(36, 36, 37, 1); /* Set the font color */
    }

    .action-buttons button:hover {
        color: rgba(215, 211, 195, 1); /* Change font color on hover */
    }
    /* Style for calendar grid cells on hover */
    .fc-day:hover {
        background-color: rgba(215, 97, 60, 0.2); /* Light red hover effect */
        cursor: pointer; /* Change cursor to pointer */
    }

    .fc-event:hover {
        background-color: rgba(215, 97, 60, 0.8) !important; /* Highlight events when hovered */
        color: #fff; /* Make text white for better visibility */
    }
    #event-details {
    background-color: #333;
    color: #fff;
    border-radius: 10px;
    padding: 20px;
    overflow-y: auto;
}

#event-details h4 {
    margin-bottom: 15px;
    font-size: 1.2rem;
    color: #d7613c;
}
@media (max-width: 768px) {
    #event-details {
        margin-top: 15px;
        margin-bottom: 15px;
        order: 2; /* Moves the event details section below the calendar */
    }
}.schedule-row {
    display: flex; /* Enables flexbox layout */
    justify-content: center; /* Centers children horizontally */
    align-items: center; /* Centers children vertically */
    flex-wrap: wrap; /* Allows wrapping on smaller screens */
    gap: 10px; /* Adds space between items */
    min-height: 400px; /* Set a minimum height for better centering */
}

/* Ensure a proper max-width for the container */
.calendar-wrapper,
#event-details {
    flex: 1; /* Allow flexible resizing */
    max-width: 45%; /* Prevent them from growing too large */
    margin: 10px;
}

/* For smaller screens: stack items */
@media (max-width: 768px) {
    .schedule-row {
        flex-direction: column; /* Stack items vertically */
    }
    .calendar-wrapper,
    #event-details {
        max-width: 100%; /* Full-width on small screens */
    }
}
.popup {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.popup-content {
    background-color: rgba(36, 36, 37, 1);
    margin: 10% auto;
    padding: 10px;
    border: 1px solid #888;
    width: 50%;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 60vh;  /* Ensure it takes a percentage of the viewport height */
}

.popup-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.popup-close:hover,
.popup-close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

    .file-input-wrapper {
        margin: 10px 0;
    }

    .file-input-wrapper input {
        display: block;
    }

    textarea {
        width: 100%;
        height: 100px;
        margin-bottom: 10px;
    }

    button {
        border-radius: 10px;
        background-color: rgba(215, 97, 60, 1);
        border: none;
    }
</style>

</head>
<body>
<!-- Modal for event form -->
<div class="modal fade" id="eventFormModal" tabindex="-1" aria-labelledby="eventFormModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventFormModalLabel">Create Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="eventFormContainer"></div>
            </div>
        </div>
    </div>
</div>

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

<!-- Schedule Section -->
<div class="schedule-container mt-5">
    <div class="schedule-header">
        <h2>Your Schedule</h2>
    </div>
    <div class="schedule-row d-flex flex-wrap justify-content-center align-items-center">
    <!-- Calendar Section -->
    <div class="calendar-wrapper" style="flex: 1; max-width: 600px; margin: 10px;">
        <div id="calendar"></div>
    </div>

    <!-- Event Details Section -->
    <div id="event-details" style="flex: 1; max-width: 400px; background-color: #333; color: #fff; border-radius: 10px; padding: 15px; margin: 10px; max-height: 340px; overflow-y: auto;">
        <h4>Upcoming Workouts</h4>
        <div id="event-details-list">
            <!-- Checkboxes will be dynamically populated here -->
        </div>
    </div>
</div>
<!-- Popup -->
<div id="diaryPopup" class="popup">
    <div class="popup-content">
        <span class="popup-close">&times;</span>
        <h3>Add Your Today's Fitness Journey</h3>
        <form id="fitnessForm" action="/cosc75/insert_fitness_data.php" method="POST" enctype="multipart/form-data">
            <label for="addImage">Upload Workout Image:</label>
            <div class="file-input-wrapper">
                <input type="file" id="addImage" name="workoutImage" accept="image/*" required>
                <span class="placeholder">Click to Upload</span>
                <img id="addPreviewImage" src="" alt="Image Preview" style="display: none; max-width: 100px; margin-top: 10px;">
            </div>
            <label for="addstory">Workout Summary:</label>
            <textarea id="addstory" name="summary" placeholder="Enter your workout summary" required></textarea><br><br>
            <button type="submit" class="add">Save</button>
            <button type="button" id="addCancelButton" class="cancel">Cancel</button>
        </form>
    </div>
</div>



    
    <div class="action-buttons">
        <button id="manageButton">Manage</button>
    </div>
</div>
<script>
document.getElementById('notificationDropdown').addEventListener('click', function () {
    fetch('mark_notification_read.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('notificationCount').textContent = '';
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>
<script>
$(document).ready(function () {
    let isEditable = false;
    let eventsData = []; // Placeholder for events data

    // Fetch events from database and return JSON
    function fetchEvents() {
        return fetch('/cosc75/get_events.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response from backend:', data);
                if (Array.isArray(data)) {
                    eventsData = data; // Set globally for use
                    renderEventDetails(eventsData);
                    return eventsData;
                } else {
                    console.error("Unexpected response structure", data);
                    return [];
                }
            })
            .catch(err => {
                console.error('Error fetching events:', err);
                return [];
            });
    }

    // Function to initialize FullCalendar
    function initializeCalendar(events) {
        // Filter out events marked as "missed"
        const filteredEvents = events.filter(event => event.status !== 'missed');
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            defaultView: 'month',
            editable: isEditable,
            events: filteredEvents, // Only include events not marked as missed
            eventRender: function (event, element) {
                element.css('background-color', '#242425');
                element.css('border-color', '#242425');
            },
            dayRender: function (date, cell) {
                const hasEvent = eventsData.some(event => moment(event.start).isSame(date, 'day') && event.status !== 'missed');
                if (hasEvent) {
                    $(cell).css('background-color', '#d7613c');
                } else {
                    $(cell).css('background-color', '');
                }
            },
            dayClick: function (date) {
                if (isEditable) {
                    showEventForm(date);
                } else {
                    alert('Click "Manage" to enable editing.');
                }
            },
            eventClick: function (calEvent) {
                if (calEvent.status === 'finished') {
                    alert('This event is marked as finished and cannot be edited.');
                    return;
                }

                $.ajax({
                    url: 'get_event_form.php',
                    method: 'GET',
                    data: { date: calEvent.start.format('YYYY-MM-DD'), eventId: calEvent.id },
                    success: function (response) {
                        $('#eventFormContainer').html(response);
                        $('#eventFormModal').modal('show');
                    }
                });
            }
        });
    }

    // Function to dynamically refresh the calendar
    function refreshCalendar() {
        fetchEvents().then(events => {
            $('#calendar').fullCalendar('destroy');
            initializeCalendar(events);
        });
    }

    // Handle AJAX for showing forms
    function showEventForm(date) {
        $.ajax({
            url: 'get_event_form.php',
            method: 'GET',
            data: { date: date.format() },
            success: function (response) {
                $('#eventFormContainer').html(response);
                $('#eventFormModal').modal('show');
                $('#eventDate').val(date.format('YYYY-MM-DD'));
            },
            error: function () {
                alert('Failed to load the form.');
            }
        });
    }

    // Manage button toggle logic
    $('#manageButton').on('click', function () {
        const manageButton = $(this);
        isEditable = !isEditable;

        // Toggle the button text
        if (isEditable) {
            manageButton.text('Save');
        } else {
            manageButton.text('Manage');
        }

        // Add or remove the border highlight on the calendar wrapper
        const calendarWrapper = $('.calendar-wrapper');
        if (isEditable) {
            calendarWrapper.addClass('editable-highlight');
        } else {
            calendarWrapper.removeClass('editable-highlight');
        }

        // Re-initialize the calendar dynamically
        refreshCalendar();
    });

    // Render events dynamically
    function renderEventDetails(events) {
        const eventDetailsList = $('#event-details-list');
        eventDetailsList.empty(); 

        if (events.length === 0) {
            eventDetailsList.html('<p>No events scheduled.</p>');
            return;
        }

        events.forEach(event => {
            const checkboxHTML = `
                <div class="form-check mb-2">
                    <input class="form-check-input finish-btn" type="checkbox" value="${event.event_id}">
                    <label class="form-check-label">
                        ${event.title} (${moment(event.start).format('MMMM Do YYYY, h:mm A')} - ${moment(event.end).format('MMMM Do YYYY, h:mm A')})
                    </label>
                </div>
            `;
            eventDetailsList.append(checkboxHTML);
        });

        $('.finish-btn').on('click', handleFinish);
    }

   // Handle the "Finish" button logic
function handleFinish() {
    const eventId = $(this).val();
    console.log("Checkbox clicked for Event ID:", eventId);

    if (!eventId) {
        alert('Invalid event ID.');
        return;
    }

    fetch('/cosc75/finish_event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: eventId })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Response:', data);
            if (data.success) {
                // Show the popup instead of alerting the user
                const popup = document.getElementById('diaryPopup');
                popup.style.display = 'block';
            } else {
                alert('Failed to mark event as finished.');
            }
        })
        .catch(err => console.error('Error processing request:', err));
}

// Close popup logic
document.querySelector('.popup-close').onclick = () => {
    document.getElementById('diaryPopup').style.display = 'none';
};

document.getElementById('addCancelButton').onclick = () => {
    document.getElementById('diaryPopup').style.display = 'none';
};



    // Remove past events that are unchecked and mark them as missed
    function removePastEvents() {
        const currentDate = moment().startOf('day');
        console.log("Checking for past events... Current Date:", currentDate.format('YYYY-MM-DD'));

        eventsData.forEach(event => {
            console.log("Event ID:", event.event_id, "Event End Date:", moment(event.end).format('YYYY-MM-DD'));

            if (moment(event.end).isBefore(currentDate) && !event.checked) {
                console.log(`Event ${event.event_id} is past due and not checked. Marking as missed...`);

                fetch('/cosc75/mark_event_missed.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ event_id: event.event_id })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response from mark_event_missed.php:', data);
                    if (data.success) {
                        console.log(`Event ${event.event_id} marked as missed.`);
                        // Remove the event visually and refresh the calendar
                        $('#calendar').fullCalendar('removeEvents', event.event_id);
                        refreshCalendar(); // Make sure this updates the view
                    } else {
                        console.error(`Failed to mark event ${event.event_id} as missed.`, data.error);
                    }
                })
                .catch(err => {
                    console.error('Error processing request:', err);
                });
            }
        });
    }

    // Call fetch events and initialize the calendar on page load
    fetchEvents().then(events => {
        initializeCalendar(events);
        removePastEvents(); // Call this after initializing the calendar to update the status
    });
});


</script>
<script>
             document.getElementById('notificationDropdown').addEventListener('click', function () {
                // Send an AJAX request to mark notifications as read
                fetch('mark_notifications_read.php', {
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

<script>
 // Handle form submission
document.getElementById('fitnessForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch('/cosc75/insert_fitness_data.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Fitness data saved successfully!');
            document.getElementById('diaryPopup').style.display = 'none'; // Close the popup
            window.location.reload(); // Refresh the entire page
        } else {
            alert('Failed to save fitness data: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(err => console.error('Error processing request:', err));
});

        
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
