<?php
require 'config.php'; // Assuming this file sets up $conn for the database connection

// Step 1: Fetch all user predictions and race results for all users and races
$sql = "
    SELECT 
        user_predictions.user_id,
        user_predictions.race_id,
        user_predictions.1st_place AS user_1st_place,
        user_predictions.2nd_place AS user_2nd_place,
        user_predictions.3rd_place AS user_3rd_place,
        user_predictions.h2h_1 AS user_h2h_1,
        user_predictions.h2h_2 AS user_h2h_2,
        user_predictions.fastest_lap AS user_fastest_lap,
        user_predictions.retirements AS user_retirements,
        race_results.1st_place AS correct_1st_place,
        race_results.2nd_place AS correct_2nd_place,
        race_results.3rd_place AS correct_3rd_place,
        race_results.h2h_1 AS correct_h2h_1,
        race_results.h2h_2 AS correct_h2h_2,
        race_results.fastest_lap AS correct_fastest_lap,
        race_results.retirements AS actual_retirements
    FROM 
        user_predictions
    JOIN 
        race_results ON user_predictions.race_id = race_results.race_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Initialize arrays to store points
    $weekly_points = []; // race_id => [user_id => points]
    $total_points = [];  // user_id => total points

    // Step 2: Calculate points for each user and race
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id']; // Current user
        $race_id = $row['race_id']; // Current race

        // Calculate points for the current race
        $points = 0;

        // Podium points
        if ($row['user_1st_place'] == $row['correct_1st_place']) $points++;
        if ($row['user_2nd_place'] == $row['correct_2nd_place']) $points++;
        if ($row['user_3rd_place'] == $row['correct_3rd_place']) $points++;

        // H2H points
        if ($row['user_h2h_1'] == $row['correct_h2h_1']) $points++;
        if ($row['user_h2h_2'] == $row['correct_h2h_2']) $points++;

        // Fastest Lap point
        if ($row['user_fastest_lap'] == $row['correct_fastest_lap']) $points++;

        // Retirements points
        $user_retirements = $row['user_retirements'];
        $actual_retirements = $row['actual_retirements'];

        if (
            ($user_retirements == 0 && $actual_retirements == 0) || // Predicted 0
            ($user_retirements == 1 && $actual_retirements >= 1 && $actual_retirements <= 2) || // Predicted 1-2
            ($user_retirements == 3 && $actual_retirements >= 3 && $actual_retirements <= 4) || // Predicted 3-4
            ($user_retirements == 5 && $actual_retirements >= 5) // Predicted 5+
        ) {
            $points++;
        }

        // Update weekly points
        if (!isset($weekly_points[$race_id])) {
            $weekly_points[$race_id] = [];
        }
        $weekly_points[$race_id][$user_id] = $points;

        // Update total points
        if (!isset($total_points[$user_id])) {
            $total_points[$user_id] = 0;
        }
        $total_points[$user_id] += $points;
    }

    // Step 3: Update weekly_standings table for all users and races
    foreach ($weekly_points as $race_id => $users) {
        foreach ($users as $user_id => $points) {
            $stmt = $conn->prepare("
                INSERT INTO weekly_standings (user_id, race_id, points)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE points = ?
            ");
            $stmt->bind_param("iiii", $user_id, $race_id, $points, $points);
            $stmt->execute();
        }
    }

    // Step 4: Update user_standings table for all users
    foreach ($total_points as $user_id => $points) {
        $stmt = $conn->prepare("
            INSERT INTO user_standings (user_id, points)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE points = ?
        ");
        $stmt->bind_param("iii", $user_id, $points, $points);
        $stmt->execute();
    }

    echo "Points calculation completed for all users.";
} else {
    echo "No predictions or results found.";
}

$conn->close();
?>
