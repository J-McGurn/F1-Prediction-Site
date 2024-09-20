<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect to login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="../css/home.css">
    <?php include 'hotbar.php'; ?>
</head>
<body>
    <div class="welcome-message">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['fname']); ?>!</h1>
        <p>You are now logged in.</p>
    </div>

    <div class="container">
        <div class="left-section">
            <table class="constructors">
                <tr>
                    <th>Constructor</th>
                    <th>Points</th>
                </tr>
                <tr>
                    <td><img src="pictures/redbull.png" alt="RedBull" class="team"></td>
                    <td class="numbers">446</td>
                </tr>
                <tr>
                    <td><img src="pictures/mclaren.png" alt="McLaren" class="team"></td>
                    <td class="numbers">438</td>
                </tr>
                <tr>
                    <td><img src="pictures/ferrari.png" alt="Ferrari" class="team"></td>
                    <td class="numbers">407</td>
                </tr>
                <tr>
                    <td><img src="pictures/mercedes.png" alt="Mercedes" class="team"></td>
                    <td class="numbers">292</td>
                </tr>
                <tr>
                    <td><img src="pictures/astonmartin.png" alt="Aston Martin" class="team"></td>
                    <td class="numbers">74</td>
                </tr>
                <tr>
                    <td><img src="pictures/rb.png" alt="Racing Bulls" class="team"></td>
                    <td class="numbers">34</td>
                </tr>
                <tr>
                    <td><img src="pictures/haas.png" alt="Haas" class="team"></td>
                    <td class="numbers">28</td>
                </tr>
                <tr>
                    <td><img src="pictures/alpine.png" alt="Alpine" class="team"></td>
                    <td class="numbers">13</td>
                </tr>
                <tr>
                    <td><img src="pictures/williams.png" alt="Williams" class="team"></td>
                    <td class="numbers">6</td>
                </tr>
                <tr>
                    <td><img src="pictures/kick.png" alt="Kick Sauber" class="team"></td>
                    <td class="numbers">0</td>
                </tr>
            </table>
        </div>
        <div class="middle-section">
            <!-- Content for the middle section -->
            <p>Middle Section Content</p>
        </div>
        <div class="right-section">
            <!-- Content for the right section -->
            <p>Right Section Content</p>
        </div>
    </div>
    
</body>
</html>
