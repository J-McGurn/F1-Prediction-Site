<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/hotbar.css">
</head>
<body>
<div class="hotbar">
        <img src="f1.png" alt="Logo" class="logo">
        <a href="home.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="settings.php">Settings</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['username'])): ?>
            <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="index.php" class="logout">Logout</a>
        <?php endif; ?>
    </div>
    <div class="content">
