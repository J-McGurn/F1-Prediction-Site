<?php
session_start();
require 'config.php';  // Database connection

$user_id = $_SESSION['user_id'] ?? null;  // Ensure user is logged in
if (!$user_id) {
    die("User not logged in.");
}

// Get current datetime
$current_datetime = new DateTime();

// Step 1: Determine the race_id based on the query parameter or get the next upcoming race
$race_id = $_GET['race_id'] ?? null;
if (!$race_id) {
    $race_id = getNextUpcomingRaceId($conn);
}

// Step 2: Fetch the race details and validate if the race is editable
$race = getRaceDetails($conn, $race_id);
$is_editable = $current_datetime < new DateTime($race['race_date']);

// Step 3: Fetch active drivers
$drivers = getActiveDrivers($conn);

// Step 4: Handle form submission if predictions are being made and race is editable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_prediction']) && $is_editable) {
    handlePredictionSubmission($conn, $user_id, $race_id, $_POST);
}

// Step 5: Fetch the deadline (1 minute before the race) and existing user predictions
$deadline = (new DateTime($race['race_date']))->modify('-1 minute');
$prediction = getUserPrediction($conn, $user_id, $race_id);

// Step 6: Fetch previous, next, first, and current races for pagination
$pagination = getRacePagination($conn, $race_id);

// Step 7: Check if the race is open for new predictions and if the current race is the upcoming race
$is_open = $deadline > new DateTime();
$is_current_race = $race_id == $pagination['current_race_id'];

// Format the race date and deadline for display
$race_date_formatted = $deadline->format('D j M. Y H:i');
$deadline_formatted = $deadline->format('D j M. Y H:i');

// Helper functions below this point
?>


<?php

// Helper: Get next upcoming race ID
function getNextUpcomingRaceId($conn) {
    $query = "SELECT race_id FROM races WHERE race_date > NOW() ORDER BY race_date ASC LIMIT 1";
    $result = $conn->query($query);
    return $result->fetch_assoc()['race_id'] ?? null;
}

// Helper: Get race details by race_id
function getRaceDetails($conn, $race_id) {
    $query = "SELECT * FROM races WHERE race_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $race_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Helper: Fetch all active drivers
function getActiveDrivers($conn) {
    $query = "SELECT driver_id, driver_name, image_filename FROM active_drivers";
    return $conn->query($query)->fetch_all(MYSQLI_ASSOC);
}

// Helper: Handle prediction form submission
function handlePredictionSubmission($conn, $user_id, $race_id, $formData) {
    $first_place = $formData['1st_place'];
    $second_place = $formData['2nd_place'];
    $third_place = $formData['3rd_place'];
    $fastest_lap = $formData['fastest_lap'];
    $any_retirements = isset($formData['retirements']) ? $formData['retirements'] : '0'; 

    $query = "INSERT INTO user_predictions (
                user_id, race_id, 1st_place, 2nd_place, 3rd_place, 
                fastest_lap, retirements
              ) VALUES (
                ?, ?, ?, ?, ?, ?, ?
              ) ON DUPLICATE KEY UPDATE
                1st_place = VALUES(1st_place),
                2nd_place = VALUES(2nd_place),
                3rd_place = VALUES(3rd_place),
                fastest_lap = VALUES(fastest_lap),
                retirements = VALUES(retirements)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iissssi", 
        $user_id, $race_id, $first_place, $second_place, $third_place, 
        $fastest_lap, $any_retirements
    );

    if (!$stmt->execute()) {
        die("Error submitting prediction: " . $stmt->error);
    }

    echo "Prediction submitted successfully.";
}

// Helper: Fetch user's existing predictions for a race
function getUserPrediction($conn, $user_id, $race_id) {
    $query = "SELECT * FROM user_predictions WHERE user_id = ? AND race_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $race_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Helper: Get race pagination (previous, next, first, and current races)
function getRacePagination($conn, $race_id) {
    $pagination = [];

    // Previous Race
    $query = "SELECT race_id FROM races WHERE race_date < (SELECT race_date FROM races WHERE race_id = ?) ORDER BY race_date DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $race_id);
    $stmt->execute();
    $stmt->bind_result($pagination['prev_race_id']);
    $stmt->fetch();
    $stmt->close();

    // Next Race
    $query = "SELECT race_id FROM races WHERE race_date > (SELECT race_date FROM races WHERE race_id = ?) ORDER BY race_date ASC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $race_id);
    $stmt->execute();
    $stmt->bind_result($pagination['next_race_id']);
    $stmt->fetch();
    $stmt->close();

    // Current Race
    $query = "SELECT race_id FROM races WHERE race_date > NOW() ORDER BY race_date ASC LIMIT 1";
    $result = $conn->query($query);
    $pagination['current_race_id'] = $result->fetch_assoc()['race_id'];

    // First Race
    $query = "SELECT race_id FROM races ORDER BY race_date ASC LIMIT 1";
    $result = $conn->query($query);
    $pagination['first_race_id'] = $result->fetch_assoc()['race_id'];

    return $pagination;
}
?>
