<?php
session_start();
require 'config.php';  // Ensure you have a file that connects to your MySQL database

// Get current user_id
$user_id = $_SESSION['user_id'];

// Get current datetime
$current_datetime = new DateTime();

// Determine the race_id to display based on pagination
// Checks to see if current race_id is displayed in URL
if(isset($_GET['race_id'])) {
    $current_race_id = (int)$_GET['race_id'];

}
// If not, gets the current race_id from the table
else {
    $race_id_query = "SELECT race_id FROM races WHERE race_date > NOW() ORDER BY race_date ASC LIMIT 1";
    $stmt = $conn->query($race_id_query);
    $result = $stmt->fetch_assoc();

    if($result) {
        $current_race_id = $result['race_id'];
    }
    // If no current race_id available (i.e end of season), use last race_id
    else {
        $race_id_query = "SELECT race_id FROM races ORDER BY race_date DESC LIMIT 1";
        $stmt = $conn->query($race_id_query);
        $result = $stmt->fetch_assoc();
        $current_race_id = $result['race_id'];
    }
}
echo $current_race_id;

// Fetch race details
$race_query = "SELECT * FROM races WHERE race_id = ?";
$stmt = $conn->prepare($race_query);
$stmt->bind_param("i", $current_race_id);
$stmt->execute();
$race_result = $stmt->get_result();
$race = $race_result->fetch_assoc();
$stmt->close();

// Determine if the current datetime is past the race datetime
$is_race_open = $current_datetime <= new DateTime($race['race_date']);
$is_editable = $current_datetime < new DateTime($race['race_date']);

// Fetch driver options from the active_drivers table
$drivers = [];
$driver_query = "SELECT driver_id, driver_name, image_filename FROM active_drivers"; 
$driver_result = $conn->query($driver_query);

if ($driver_result) {
    while ($row = $driver_result->fetch_assoc()) {
        $drivers[] = $row; // Store driver options in an array
    }
}

// Fetch H2H driver options for active race
// H2H_1
$h2h1 = [];
$h2h1_query = "SELECT h.driver1_id, h.driver2_id,
                ad1.driver_name AS driver1_name, ad1.image_filename AS driver1_image,
                ad2.driver_name AS driver2_name, ad2.image_filename AS driver2_image
                FROM h2h h
                JOIN active_drivers ad1 ON h.driver1_id = ad1.driver_id
                JOIN active_drivers ad2 ON h.driver2_id = ad2.driver_id
                WHERE h.race_id = ? AND h.h2h_number = 1";
$stmt = $conn->prepare($h2h1_query);
$stmt->bind_param("i", $current_race_id);
$stmt->execute();
$h2h1_query_result = $stmt->get_result();
if ($h2h1_query_result) {
    $h2h1 = $h2h1_query_result->fetch_assoc();
}
$stmt->close();

// H2H_2
$h2h2 = [];
$h2h2_query = "SELECT h.driver1_id, h.driver2_id,
                ad1.driver_name AS driver1_name, ad1.image_filename AS driver1_image,
                ad2.driver_name AS driver2_name, ad2.image_filename AS driver2_image
                FROM h2h h
                JOIN active_drivers ad1 ON h.driver1_id = ad1.driver_id
                JOIN active_drivers ad2 ON h.driver2_id = ad2.driver_id
                WHERE h.race_id = ? AND h.h2h_number = 2";
$stmt = $conn->prepare($h2h2_query);
$stmt->bind_param("i", $current_race_id);
$stmt->execute();
$h2h2_query_result = $stmt->get_result();
if ($h2h2_query_result) {
    $h2h2 = $h2h2_query_result->fetch_assoc();
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_prediction']) && $is_editable) {
    $first_place = $_POST['1st_place'];
    $second_place = $_POST['2nd_place'];
    $third_place = $_POST['3rd_place'];
    $h2h_1 = $_POST['h2h1_selection'];
    $h2h_2 = $_POST['h2h2_selection'];
    $fastest_lap = $_POST['fastest_lap'];
    $any_retirements = isset($_POST['retirements']) ? $_POST['retirements'] : '0'; 

    // Validate inputs
    if (!$first_place || !$second_place || !$third_place || !$h2h_1 || !$h2h_2 || !$fastest_lap || !$any_retirements) {
        // Redirect back with an error message
        $error_message = urlencode("Invalid prediction data. Please fill out all required fields.");
        header("Location: predictions.php?race_id=$current_race_id&error=$error_message");
        exit;
    }

    // Validate race_id exists
    $race_check_query = "SELECT COUNT(*) AS count FROM races WHERE race_id = ?";
    $stmt = $conn->prepare($race_check_query);
    $stmt->bind_param("i", $current_race_id);
    $stmt->execute();
    $stmt->bind_result($race_count);
    $stmt->fetch();
    $stmt->close();

    if ($race_count == 0) {
        echo $current_race_id;
        die("Race ID does not exist in the database.");
    }

    // Insert or update the prediction
    $query = "INSERT INTO user_predictions (
                user_id, race_id, 1st_place, 2nd_place, 3rd_place, 
                h2h_1, h2h_2, fastest_lap, retirements
              ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
              ) ON DUPLICATE KEY UPDATE
                1st_place = VALUES(1st_place),
                2nd_place = VALUES(2nd_place),
                3rd_place = VALUES(3rd_place),
                h2h_1 = VALUES(h2h_1),
                h2h_2 = VALUES(h2h_2),
                fastest_lap = VALUES(fastest_lap),
                retirements = VALUES(retirements)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iiiiiiiii", 
        $user_id, $current_race_id, $first_place, $second_place, $third_place, 
        $h2h_1, $h2h_2, $fastest_lap, $any_retirements
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
$stmt->bind_param("i", $current_race_id);
$stmt->execute();
$race_result = $stmt->get_result();
$race = $race_result->fetch_assoc();
$stmt->close();

// Calculate deadline for submissions (1 minute before the race date)
$deadline = new DateTime($race['race_date']);
$deadline->modify('-1 minute');

// Fetch existing predictions for the current race
$prediction_query = "SELECT * FROM user_predictions WHERE user_id = ? AND race_id = ?";
$stmt = $conn->prepare($prediction_query);
$stmt->bind_param("ii", $user_id, $current_race_id);
$stmt->execute();
$prediction_result = $stmt->get_result();
$prediction = $prediction_result->fetch_assoc();
$stmt->close();

// Fetch the first, previous and next race IDs based on the current race ID
$first_race_query = "SELECT race_id FROM races ORDER BY race_date ASC LIMIT 1";
$prev_race_query = "SELECT race_id FROM races WHERE race_date < (SELECT race_date FROM races WHERE race_id = ?) ORDER BY race_date DESC LIMIT 1";
$next_race_query = "SELECT race_id FROM races WHERE race_date > (SELECT race_date FROM races WHERE race_id = ?) ORDER BY race_date ASC LIMIT 1";

$stmt = $conn->prepare($first_race_query);
$stmt->execute();
$stmt->bind_result($first_race_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare($prev_race_query);
$stmt->bind_param("i", $current_race_id);
$stmt->execute();
$stmt->bind_result($prev_race_id);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare($next_race_query);
$stmt->bind_param("i", $current_race_id);
$stmt->execute();
$stmt->bind_result($next_race_id);
$stmt->fetch();
$stmt->close();

// Check if the current_race_id is the latest race to be open
$latest_race_query = "SELECT race_id FROM races WHERE race_date > NOW() ORDER BY race_date ASC LIMIT 1";

$stmt = $conn->query($latest_race_query);
    $result = $stmt->fetch_assoc();

    if($result) {
        $latest_race_id = $result['race_id'];
    }
    // If no current race_id available (i.e end of season), use last race_id
    else {
        $latest_query = "SELECT race_id FROM races ORDER BY race_date DESC LIMIT 1";
        $stmt = $conn->query($latest_query);
        $result = $stmt->fetch_assoc();
        $latest_race_id = $result['race_id'];
    }


// Determine if the race is open for predictions
$is_open = $deadline > new DateTime();

// Determine if the current race is the upcoming race
$is_current_race = $latest_race_id == $current_race_id;

// Determine if the "Next Race" button should be displayed
$show_next_race_button = !$is_current_race && $next_race_id !== null;

$race_date_formatted = $deadline->format('D j M. Y H:i');
$deadline_formatted = $deadline->format('D j M. Y H:i');
?>