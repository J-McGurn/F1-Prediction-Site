<?php
session_start();
require 'config.php';  // Ensure you have a file that connects to your MySQL database

$user_id = $_SESSION['user_id'];

// Get current datetime
$current_datetime = new DateTime();

// Determine the race_id to display based on pagination
if (isset($_GET['race_id'])) {
    $race_id = (int)$_GET['race_id'];
} else {
    // Fetch the next upcoming race ID
    $next_open_race_query = "SELECT race_id FROM races WHERE race_date > NOW() ORDER BY race_date ASC LIMIT 1";
    $next_open_race_result = $conn->query($next_open_race_query);
    $next_open_race = $next_open_race_result->fetch_assoc();
    $race_id = $next_open_race['race_id'] ?? null;
}

// Fetch race details
$race_query = "SELECT * FROM races WHERE race_id = ?";
$stmt = $conn->prepare($race_query);
$stmt->bind_param("i", $race_id);
$stmt->execute();
$race_result = $stmt->get_result();
$race = $race_result->fetch_assoc();
$stmt->close();

// Determine if the current datetime is past the race datetime
$is_race_open = $current_datetime <= new DateTime($race['race_date']);
$is_editable = $current_datetime < new DateTime($race['race_date']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_prediction']) && $is_editable) {
    $first_place = $_POST['first_place'];
    $second_place = $_POST['second_place'];
    $third_place = $_POST['third_place'];
    $fastest_lap = $_POST['fastest_lap'];
    $winning_constructor = $_POST['winning_constructor'];
    $any_safety_cars = isset($_POST['any_safety_cars']) ? 1 : 0;
    $any_retirements = isset($_POST['any_retirements']) ? 1 : 0;

    // Insert or update the prediction
    $query = "INSERT INTO predictions (
                user_id, race_id, first_place, second_place, third_place, 
                fastest_lap, winning_constructor, any_safety_cars, any_retirements
              ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
              ) ON DUPLICATE KEY UPDATE
                first_place = VALUES(first_place),
                second_place = VALUES(second_place),
                third_place = VALUES(third_place),
                fastest_lap = VALUES(fastest_lap),
                winning_constructor = VALUES(winning_constructor),
                any_safety_cars = VALUES(any_safety_cars),
                any_retirements = VALUES(any_retirements)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iisssssii", 
        $user_id, $race_id, $first_place, $second_place, $third_place, 
        $fastest_lap, $winning_constructor, $any_safety_cars, $any_retirements
    );

    if ($stmt->execute()) {
        $message = "Prediction submitted successfully.";
    } else {
        $message = "Error submitting prediction: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch race details again to ensure the latest data
$race_query = "SELECT * FROM races WHERE race_id = ?";
$stmt = $conn->prepare($race_query);
$stmt->bind_param("i", $race_id);
$stmt->execute();
$race_result = $stmt->get_result();
$race = $race_result->fetch_assoc();
$stmt->close();

// Calculate deadline for submissions (1 minute before the race date)
$deadline = new DateTime($race['race_date']);
$deadline->modify('-1 minute');

// Fetch existing predictions for the current race
$prediction_query = "SELECT * FROM predictions WHERE user_id = ? AND race_id = ?";
$stmt = $conn->prepare($prediction_query);
$stmt->bind_param("ii", $user_id, $race_id);
$stmt->execute();
$prediction_result = $stmt->get_result();
$prediction = $prediction_result->fetch_assoc();
$stmt->close();

// Fetch the previous, next, and first race IDs based on the current race ID
$prev_race_query = "SELECT race_id FROM races WHERE race_date < (SELECT race_date FROM races WHERE race_id = ?) ORDER BY race_date DESC LIMIT 1";
$next_race_query = "SELECT race_id FROM races WHERE race_date > (SELECT race_date FROM races WHERE race_id = ?) ORDER BY race_date ASC LIMIT 1";
$current_race_query = "SELECT race_id FROM races WHERE race_date > NOW() ORDER BY race_date ASC LIMIT 1";
$first_race_query = "SELECT race_id FROM races ORDER BY race_date ASC LIMIT 1";

$stmt = $conn->prepare($prev_race_query);
$stmt->bind_param("i", $race_id);
$stmt->execute();
$stmt->bind_result($prev_race_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare($next_race_query);
$stmt->bind_param("i", $race_id);
$stmt->execute();
$stmt->bind_result($next_race_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare($current_race_query);
$stmt->execute();
$stmt->bind_result($current_race_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare($first_race_query);
$stmt->execute();
$stmt->bind_result($first_race_id);
$stmt->fetch();
$stmt->close();

// Determine if the race is open for predictions
$is_open = $deadline > new DateTime();

// Determine if the current race is the upcoming race
$is_current_race = $race_id == $current_race_id;

// Determine if the "Next Race" button should be displayed
$show_next_race_button = !$is_current_race && $next_race_id !== null;

$race_date_formatted = $deadline->format('D j M. Y H:i');
$deadline_formatted = $deadline->format('D j M. Y H:i');
?>
