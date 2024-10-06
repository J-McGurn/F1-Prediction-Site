<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect to login page if not logged in
    exit();
}

// Load the data
$dataConstructors = json_decode(file_get_contents('../data/constructors.json'), true);
$dataDrivers = json_decode(file_get_contents('../data/drivers.json'), true);
// Sort and slice data to get the top 5
$topDrivers = array_slice($dataDrivers, 0, 5);
$topConstructors = array_slice($dataConstructors, 0, 5);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="icon" href="../images/f1.png">
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
            <!-- Left section content goes here -->
        </div>
        <div class="right-section">
        <div class="tables">
    <table class="drivers">
        <h3>2024 F1 Drivers Standings</h3>
        <?php if ($topDrivers): ?>
            <?php foreach ($topDrivers as $entry): ?>
                <tr>
                    <td class="position"><?php echo htmlspecialchars($entry['position']); ?>.</td>
                    <td><img src="<?php echo htmlspecialchars($entry['logo']); ?>" alt="Team Logo"></td>
                    <td><?php echo htmlspecialchars($entry['driver']); ?></td>
                    <td><?php echo htmlspecialchars($entry['points']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No data available.</td>
            </tr>
        <?php endif; ?>
        <tr>
            <td colspan="4" class="table-link"><a href="drivers.php">See Full Drivers</a></td>
        </tr>
    </table>
</div>

<table class="constructors">
    <h3>2024 F1 Constructors Standings</h3>
    <?php if ($topConstructors): ?>
        <?php foreach ($topConstructors as $entry): ?>
            <tr>
                <td class="position"><?php echo htmlspecialchars($entry['position']); ?>.</td>
                <td><img src="<?php echo htmlspecialchars($entry['logo']); ?>" alt="Team Logo"></td>
                <td><?php echo htmlspecialchars($entry['team']); ?></td>
                <td><?php echo htmlspecialchars($entry['points']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="2">No data available.</td>
        </tr>
    <?php endif; ?>
    <tr>
        <td colspan="4" class="table-link"><a href="constructors.php">See Full Constructors</a></td>
    </tr>
</table>

        </div>


    </div>
</body>

</html>