<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Insert the updated CSS here */
    </style>
</head>
<body>
    <div class="hotbar">
        <img src="../images/f1.png" alt="Logo" class="logo">
        <div class="hotbar-links">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="index.php" class="logout">Logout</a>
        </div>
    </div>
    <div class="content">
        <!-- Your page content here -->
    </div>
</body>
</html>
