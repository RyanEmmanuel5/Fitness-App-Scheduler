<?php
include 'db.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo '<p style="color: #fff;">You must log in to set or view events.</p>';
    exit;
}

if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $username = $_SESSION['username']; // Current logged-in user's username

    // Fetch the event details from database for that date and logged-in user
    $stmt = $conn->prepare('SELECT event_id, start_time, end_time, title, description, status FROM schedule_tbl WHERE date = ? AND username = ?');
    $stmt->bind_param('ss', $date, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    if ($event) {
        $event_id = $event['event_id'];
        $start_time = $event['start_time'];
        $end_time = $event['end_time'];
        $title = $event['title'];
        $description = $event['description'];
        $status = $event['status'];

        // If the event is marked as 'completed', set it to 'pending' for the new event
        if ($status === 'completed') {
            $status = 'pending';
        }
    } else {
        // If no event is found, set default values for a new event
        $event_id = '';
        $start_time = '';
        $end_time = '';
        $title = '';
        $description = '';
        $status = 'pending';
    }

    // Include the check logic snippet for event handling
    if ($event_id) {
        $stmt = $conn->prepare("SELECT * FROM schedule_tbl WHERE event_id = ? AND username = ?");
        $stmt->bind_param('is', $event_id, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $eventDetails = $result->fetch_assoc();
        $stmt->close();
    }

    // Render the event form
    echo '
    <input type="hidden" id="eventId" name="eventId">
    <form id="eventForm" class="p-3" style="background-color: #2b2b2b; border-radius: 10px; color: #fff;">
        <input type="hidden" name="event_id" value="' . htmlspecialchars($event_id) . '">  <!-- Event ID for existing events -->
        <div class="mb-3">
            <label for="eventDate" class="form-label">Date</label>
            <input type="date" id="eventDate" name="eventDate" class="form-control" value="' . htmlspecialchars($date) . '" readonly style="background-color: #444; color: #fff; border: none;">
        </div>
        <div class="row">
            <div class="col">
                <label for="startTime" class="form-label">Start Time</label>
                <input type="time" id="startTime" name="startTime" class="form-control" value="' . htmlspecialchars($start_time) . '" style="background-color: #444; color: #fff; border: none;">
            </div>
            <div class="col">
                <label for="endTime" class="form-label">End Time</label>
                <input type="time" id="endTime" name="endTime" class="form-control" value="' . htmlspecialchars($end_time) . '" style="background-color: #444; color: #fff; border: none;">
            </div>
        </div>
        <div class="mb-3 mt-3">
            <label for="eventTitle" class="form-label">Title</label>
            <input type="text" id="eventTitle" name="eventTitle" class="form-control" value="' . htmlspecialchars($title) . '" style="background-color: #444; color: #fff; border: none;">
        </div>
        <div class="mb-3">
            <label for="eventDescription" class="form-label">Description</label>
            <textarea id="eventDescription" name="eventDescription" class="form-control" rows="3" style="background-color: #444; color: #fff; border: none;">' . htmlspecialchars($description) . '</textarea>
        </div>
        <div class="d-flex justify-content-between">
            <button type="button" class="btn" id="saveButton" style="background-color: #f05340; color: #fff; width: 100px;">Save</button>
            <button type="button" class="btn" id="deleteButton" style="background-color: #f05340; color: #fff; width: 100px;">Delete</button>
        </div>
    </form>
    <script>
        document.getElementById("saveButton").addEventListener("click", function() {
            const formData = new FormData(document.getElementById("eventForm"));
            fetch("save_event.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while saving the event.");
            });
        });

        document.getElementById("deleteButton").addEventListener("click", function() {
            const confirmDelete = confirm("Are you sure you want to delete this event?");
            if (confirmDelete) {
                const formData = new FormData();
                formData.append("eventDate", document.getElementById("eventDate").value);
                fetch("delete_event.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while deleting the event.");
                });
            }
        });
    </script>';
} else {
    echo '<p style="color: #fff;">Invalid request: Missing or invalid date parameter.</p>';
}
?>
