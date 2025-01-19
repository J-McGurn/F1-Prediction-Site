<?php
if (!$prediction) {
    echo '<p>You made no predictions for this race.</p>';
} else {
    $sql = "
            SELECT 
                drivers_1.image_filename AS first_place_picture,
                drivers_2.image_filename AS second_place_picture,
                drivers_3.image_filename AS third_place_picture,
                race_results.1st_place AS correct_1st_place,
                race_results.2nd_place AS correct_2nd_place,
                race_results.3rd_place AS correct_3rd_place,
                user_predictions.1st_place AS user_1st_place,
                user_predictions.2nd_place AS user_2nd_place,
                user_predictions.3rd_place AS user_3rd_place,
                user_predictions.h2h_1 AS user_h2h_1,
                user_predictions.h2h_2 AS user_h2h_2,
                race_results.h2h_1 AS correct_h2h_1,
                race_results.h2h_2 AS correct_h2h_2,
                drivers_h2h_1.image_filename AS h2h_1_picture,
                drivers_h2h_2.image_filename AS h2h_2_picture,
                user_predictions.fastest_lap AS user_fastest_lap,
                race_results.fastest_lap AS correct_fastest_lap,
                drivers_fastest_lap.image_filename AS fastest_lap_picture,
                user_predictions.retirements AS user_retirements,
                race_results.retirements AS correct_retirements
            FROM 
                user_predictions
            JOIN 
                active_drivers AS drivers_1 ON user_predictions.1st_place = drivers_1.driver_id
            JOIN 
                active_drivers AS drivers_2 ON user_predictions.2nd_place = drivers_2.driver_id
            JOIN 
                active_drivers AS drivers_3 ON user_predictions.3rd_place = drivers_3.driver_id
            JOIN 
                race_results ON user_predictions.race_id = race_results.race_id
            JOIN 
                active_drivers AS drivers_h2h_1 ON user_predictions.h2h_1 = drivers_h2h_1.driver_id
            JOIN 
                active_drivers AS drivers_h2h_2 ON user_predictions.h2h_2 = drivers_h2h_2.driver_id
            JOIN 
                active_drivers AS drivers_fastest_lap ON user_predictions.fastest_lap = drivers_fastest_lap.driver_id
            WHERE 
                user_predictions.user_id = ?
                AND user_predictions.race_id = ?
        ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $current_race_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the data
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Podium data
        $first_place_picture = $row['first_place_picture'];
        $second_place_picture = $row['second_place_picture'];
        $third_place_picture = $row['third_place_picture'];

        $correct_1st_place = $row['correct_1st_place'];
        $correct_2nd_place = $row['correct_2nd_place'];
        $correct_3rd_place = $row['correct_3rd_place'];

        // User's predictions
        $user_1st_place = $row['user_1st_place'];
        $user_2nd_place = $row['user_2nd_place'];
        $user_3rd_place = $row['user_3rd_place'];

        // H2H predictions
        $h2h_1_picture = $row['h2h_1_picture'];
        $h2h_2_picture = $row['h2h_2_picture'];
        $user_h2h_1 = $row['user_h2h_1'];
        $user_h2h_2 = $row['user_h2h_2'];
        $correct_h2h_1 = $row['correct_h2h_1'];
        $correct_h2h_2 = $row['correct_h2h_2'];

        // Fastest Lap data
        $fastest_lap_picture = $row['fastest_lap_picture'];
        $user_fastest_lap = $row['user_fastest_lap'];
        $correct_fastest_lap = $row['correct_fastest_lap'];

        // Retirements data
        $user_retirements = $row['user_retirements'];
        $correct_retirements = $row['correct_retirements'];

        // Compare predictions
        $is_1st_correct = ($user_1st_place == $correct_1st_place);
        $is_2nd_correct = ($user_2nd_place == $correct_2nd_place);
        $is_3rd_correct = ($user_3rd_place == $correct_3rd_place);
        $is_h2h_1_correct = ($user_h2h_1 == $correct_h2h_1);
        $is_h2h_2_correct = ($user_h2h_2 == $correct_h2h_2);
        $is_fastest_lap_correct = ($user_fastest_lap == $correct_fastest_lap);

        $is_retirements_correct = (
            ($user_retirements == 0 && $correct_retirements == 0) ||
            ($user_retirements == 1 && $correct_retirements >= 1 && $correct_retirements <= 2) ||
            ($user_retirements == 3 && $correct_retirements >= 3 && $correct_retirements <= 4) ||
            ($user_retirements == 5 && $correct_retirements >= 5)
        );
    } else {
        echo "No predictions found for the current race.";
        exit;
    }

    $stmt->close();
?>

    <link rel="stylesheet" href="../css/show_predictions.css">

    <h2>Podium</h2>
    <table>
        <title>Podium</title>
        <thead>
            <th>2nd Place</th>
            <th>1st Place</th>
            <th>3rd Place</th>
        </thead>
        <tbody>
            <tr>
                <td><img src="../images/<?php echo htmlspecialchars($second_place_picture); ?>"></td>
                <td><img src="../images/<?php echo htmlspecialchars($first_place_picture); ?>"></td>
                <td><img src="../images/<?php echo htmlspecialchars($third_place_picture); ?>"></td>
            </tr>
            <tr>
                <td><img src="../images/<?php echo $is_2nd_correct ? 'tick.png' : 'cross.png'; ?>"></td>
                <td><img src="../images/<?php echo $is_1st_correct ? 'tick.png' : 'cross.png'; ?>"></td>
                <td><img src="../images/<?php echo $is_3rd_correct ? 'tick.png' : 'cross.png'; ?>"></td>
            </tr>
        </tbody>
    </table>

    <h2>Head-to-Head</h2>
    <table>
        <title>h2hs</title>
        <thead>
            <th>H2H 1</th>
            <th>H2H 2</th>
        </thead>
        <tbody>
            <tr>
                <td><img src="../images/<?php echo htmlspecialchars($h2h_1_picture); ?>" alt="H2H 1"></td>
                <td><img src="../images/<?php echo htmlspecialchars($h2h_2_picture); ?>" alt="H2H 2"></td>
            </tr>
            <tr>
                <td><img src="../images/<?php echo $is_h2h_1_correct ? 'tick.png' : 'cross.png'; ?>" alt="H2H 1 Correctness"></td>
                <td><img src="../images/<?php echo $is_h2h_2_correct ? 'tick.png' : 'cross.png'; ?>" alt="H2H 2 Correctness"></td>
            </tr>
        </tbody>
    </table>

    <h2>Fastest Lap</h2>
    <table>
        <thead>
            <th>Driver</th>
            <th>Result</th>
        </thead>
        <tbody>
            <tr>
                <td><img src="../images/<?php echo htmlspecialchars($fastest_lap_picture); ?>" alt="Fastest Lap"></td>
                <td><img src="../images/<?php echo $is_fastest_lap_correct ? 'tick.png' : 'cross.png'; ?>" alt="Fastest Lap Correctness"></td>
            </tr>
        </tbody>
    </table>

    <?php
        // Map retirements values to their textual descriptions
        $retirement_options = [
            0 => "0",
            1 => "1-2",
            3 => "3-4",
            5 => "5+"
        ];

        // Convert stored values to textual descriptions
        $user_retirements_text = isset($retirement_options[$user_retirements]) ? $retirement_options[$user_retirements] : "Unknown";
    ?>

    <h2>Retirements</h2>
    <table>
        <thead>
            <th>Your Prediction</th>
            <th>Actual Retirements</th>
            <th>Result</th>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($user_retirements_text); ?></td>
                <td><?php echo htmlspecialchars($correct_retirements); ?></td>
                <td><img src="../images/<?php echo $is_retirements_correct ? 'tick.png' : 'cross.png'; ?>" alt="Retirements Correctness"></td>
            </tr>
        </tbody>
    </table>
<?php } ?>