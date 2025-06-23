<?php
session_start();

// If the user is already logged in, redirect them to the landing page
if (isset($_SESSION['username'])) {
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="astyle.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        /* Popup error and success message styles */
        #error-message, #success-message {
            display: none;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 80%;
            box-sizing: border-box;
            animation: slideIn 0.5s ease-in-out forwards;
        }

        /* Error message style */
        #error-message {
            background-color: #bc544b; /* Red */
            color: white;
        }

        /* Success message style */
        #success-message {
            background-color: #86945e; /* Green */
            color: white;
        }

        /* Slide-in animation for both error and success messages */
        @keyframes slideIn {
            0% {
                top: -50px;
                opacity: 0;
            }
            100% {
                top: 20px;
                opacity: 1;
            }
        }

        /* Logo styling */
        .logo-container {
            text-align: center;
            margin-top: 30px;
        }

        .logo {
            width: 75px; /* Adjust size as needed */
            height: auto;
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="form-box">
            <!-- Logo Section -->
            <div class="logo-container">
                <img src="logo1.png" alt="Logo" class="logo">
            </div>

            <div class="button-box">
                <div id="btn"></div>
                <button type="button" class="toggle-btn" id="login-btn" onclick="login()">Login</button>
                <button type="button" class="toggle-btn" id="register-btn" onclick="register()">Register</button>
            </div>

            <!-- Error and Success Message Section -->
            <div id="error-message"></div>
            <div id="success-message"></div>

            <!-- Login Form -->
            <form id="login" class="input-group" action="alogin.php" method="POST">
                <input type="text" class="input-field" name="username" placeholder="Username" required>
                <input type="password" class="input-field" name="password" placeholder="Password" required>
                <button type="submit" class="submit-btn">Login</button>
            </form>

            <!-- Register Form -->
            <!-- Adjusted Register Form -->
            <form id="register" class="input-group" method="POST" onsubmit="return validatePassword(event)">
                <input type="text" class="input-field" placeholder="Username" name="A_USERNAME" id="register-username" required>
                <input type="email" class="input-field" placeholder="Email" name="A_EMAIL" id="register-email" required>
                <input type="password" class="input-field" placeholder="Password" name="A_PASSWORD" id="password" required>
                <input type="password" class="input-field" placeholder="Re-type Password" name="A_REPASSWORD" id="repassword" required>
                <button type="submit" class="submit-btn">Register</button>
            </form>
        </div>
    </div>

    <script>
        // Switch to registration form
        function register() {
            document.getElementById("login").style.left = "-400px";
            document.getElementById("register").style.left = "50px";
            document.getElementById("btn").style.left = "105px";
            document.getElementById("error-message").style.display = 'none'; // Hide error message when switching forms
            document.getElementById("success-message").style.display = 'none'; // Hide success message
        }

        // Switch to login form
        function login() {
            document.getElementById("login").style.left = "50px";
            document.getElementById("register").style.left = "450px";
            document.getElementById("btn").style.left = "0";
            document.getElementById("error-message").style.display = 'none'; // Hide error message when switching forms
            document.getElementById("success-message").style.display = 'none'; // Hide success message
        }

        // Validate password match before submitting the form
        // Validate password match before submitting the form
        function validatePassword(event) {
            event.preventDefault(); // Prevent normal form submission
            var password = document.getElementById('password').value;
            var repassword = document.getElementById('repassword').value;
            var username = document.getElementById('register-username').value;

            // First, check if passwords match
            if (password !== repassword) {
                showError('Passwords do not match. Please try again.');
                return false; // Prevent form submission
            }
            // Check username availability via AJAX
            checkUsernameAvailability(username, password, repassword);
            return false; // Prevent immediate form submission, wait for AJAX to complete
        }

        // Function to display error messages as a popup
        function showError(message) {
            var errorMessage = document.getElementById('error-message');
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';

            // Automatically hide the error message after 3 seconds
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 3000); // Hide after 3 seconds
        }

        // Function to display success messages as a popup
        function showSuccess(message) {
            var successMessage = document.getElementById('success-message');
            successMessage.textContent = message;
            successMessage.style.display = 'block';

            // Automatically hide the success message after 3 seconds
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 3000); // Hide after 3 seconds
        }

        // Check if the username is already taken (AJAX request to register.php)
        function checkUsernameAvailability(username, password, repassword) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "aregister.php", true); // Send the request to register.php
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText.trim();
                    if (response === "taken") {
                        showError("Username is already taken. Please choose another one.");
                    } else if (response === "available") {
                        submitRegistration(username, password, repassword);
                    }
                }
            };
            xhr.send("action=check_username&username=" + encodeURIComponent(username)); // Send the action and username to the PHP script
        }

        // Submit the registration form via AJAX
        function submitRegistration(username, password, repassword) {
            var email = document.getElementById('register-email').value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "aregister.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            var formData = "A_USERNAME=" + encodeURIComponent(username) +
                        "&A_PASSWORD=" + encodeURIComponent(password) +
                        "&A_REPASSWORD=" + encodeURIComponent(repassword) +
                        "&A_EMAIL=" + encodeURIComponent(email);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText.trim();
                    if (response === "success") {
                        showSuccess('Registration Successful! Proceed to Login Now');
                    } else {
                        showError(response);
                    }
                }
            };

            xhr.send(formData);
        }

        document.getElementById('login').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();

            xhr.open('POST', 'alogin.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = xhr.responseText.trim();

                    if (response === "success") {
                        window.location.href = "admin_dashboard.php"; // Redirect to landing page
                    } else {
                        showError(response); // Show error message
                    }
                }
            };

            xhr.send(formData); // Send form data via AJAX
        });

    </script>
</body>
</html>
