<?php
session_start();
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup and Login</title>
    <link rel="stylesheet" href="../css/index.css"> <!-- Adjust path to CSS -->
</head>
<body>
    <div class="form-container">
        <!-- Signup Form -->
        <div class="form-box">
            <h2>Sign Up</h2>
            <form action="index_process.php" method="post">
                <input type="hidden" name="action" value="signup"> <!-- Indicates signup action -->
                <label for="signup-username">Username:</label>
                <input type="text" id="signup-username" name="username" required>
                <label for="signup-fname">First Name:</label>
                <input type="text" id="signup-fname" name="fname" required>
                <label for="signup-sname">Surname:</label>
                <input type="text" id="signup-sname" name="sname" required>
                <label for="signup-email">Email:</label>
                <input type="email" id="signup-email" name="email" required>
                <label for="signup-password">Password:</label>
                <input type="password" id="signup-password" name="password" required>
                
                <!-- Display errors or success messages if they exist -->
                <?php
                if (isset($_SESSION['errors'])) {
                    foreach ($_SESSION['errors'] as $field => $error) {
                        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                    }
                    // Clear errors after displaying
                    unset($_SESSION['errors']);
                }
                ?>
                <input type="submit" value="Sign Up">
            </form>
        </div>

        <!-- Login Form -->
        <div class="form-box">
            <h2>Log In</h2>
            <form action="index_process.php" method="post">
                <input type="hidden" name="action" value="login"> <!-- Indicates login action -->
                <label for="login-username">Username:</label>
                <input type="text" id="login-username" name="username" required>
                <label for="login-password">Password:</label>
                <input type="password" id="login-password" name="password" required>
                
                <!-- Display errors if they exist -->
                <?php
                if (isset($_SESSION['login_errors'])) {
                    foreach ($_SESSION['login_errors'] as $error) {
                        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
                    }
                    // Clear errors after displaying
                    unset($_SESSION['login_errors']);
                }
                ?>
                <input type="submit" value="Log In">
            </form>
        </div>
    </div>
</body>
</html>
