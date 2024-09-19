<?php
session_start();
require 'config.php';  // Ensure this file connects to your MySQL database

$user_id = $_SESSION['user_id'] ?? 1;  // Placeholder for the logged-in user
$race_id = $_GET['race_id'] ?? 1;      // Placeholder race ID

// Fetch active drivers
$drivers_query = "SELECT * FROM active_drivers";
$drivers_result = $conn->query($drivers_query);
$drivers = $drivers_result->fetch_all(MYSQLI_ASSOC);

// Fetch predictions
$prediction_query = "SELECT * FROM user_predictions WHERE user_id = ? AND race_id = ?";
$stmt = $conn->prepare($prediction_query);
$stmt->bind_param("ii", $user_id, $race_id);
$stmt->execute();
$prediction_result = $stmt->get_result();
$prediction = $prediction_result->fetch_assoc();
$stmt->close();

$is_open = true; // Placeholder to indicate if predictions are open

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>F1 Predictions</title>
    <link rel="stylesheet" href="css/predictions.css">
</head>
<body>
    <?php if ($is_open) { ?>
        <form method="post" action="submit_predictions.php?race_id=<?php echo $race_id; ?>">
            <label for="1st_place">1st Place:</label>
            <select id="1st_place" name="1st_place" required>
                <option value="">Select Driver</option>
                <?php foreach ($drivers as $driver): ?>
                    <option value="<?php echo htmlspecialchars($driver['driver_name']); ?>" 
                        <?php echo isset($prediction['1st_place']) && $prediction['1st_place'] == $driver['driver_name'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($driver['driver_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <!-- Add more form fields as necessary -->
            <button type="submit">Submit Prediction</button>
        </form>
    <?php } else { ?>
        <h2>Your Prediction</h2>
        <p>1st Place: <?php echo htmlspecialchars($prediction['1st_place']); ?></p>
        <!-- Display more prediction details here -->
    <?php } ?>
</body>
</html>
