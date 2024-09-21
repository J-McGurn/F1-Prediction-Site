<?php
require 'submit_predictions.php'; // Include the PHP logic file

include 'hotbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">


    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 Predictions</title>
    <link rel="stylesheet" href="../css/predictions.css"> <!-- Link to the CSS file -->
</head>

<body>
    <div class="container">
        <h1>F1 Predictions for:</h1>

        <div class="race-navigation">
            <?php if (isset($first_race_id)): ?>
                <?php if ($race_id != $first_race_id): ?>
                    <a href="predictions.php?race_id=<?php echo $first_race_id; ?>" class="button">|<<</a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($prev_race_id)): ?>
                        <a href="predictions.php?race_id=<?php echo $prev_race_id; ?>" class="button"><</a>
                            <?php endif; ?>

                            <span class="current-race">
                                <?php echo htmlspecialchars($race['race_name']); ?>
                            </span>

                            <?php if ((isset($next_race_id)) && $show_next_race_button): ?>
                                <a href="predictions.php?race_id=<?php echo $next_race_id; ?>" class="button">></a>
                            <?php endif; ?>

                            <?php if ($current_race_id && $current_race_id != $race_id): ?>
                                <a href="predictions.php?race_id=<?php echo $current_race_id; ?>" class="button">>>|</a>
                            <?php endif; ?>
        </div>

        <p class="deadline">
            Deadline for submitting predictions: <?php echo $deadline_formatted; ?> BST
        </p>

        <?php if (isset($message)) {
            echo "<p>$message</p>";
        } ?>

        <?php if ($is_open) { ?>
            <form method="post" action="predictions.php?race_id=<?php echo $race_id; ?>" class="prediction-form">

                <label for="1st_place">1st Place:</label>
                <select id="1st_place" name="1st_place" <?php echo !$is_editable ? 'disabled' : ''; ?> required>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>" <?php echo (isset($prediction['1st_place']) && $prediction['1st_place'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($driver['driver_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="2nd_place">2nd Place:</label>
                <select id="2nd_place" name="2nd_place" <?php echo !$is_editable ? 'disabled' : ''; ?> required>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>" <?php echo (isset($prediction['2nd_place']) && $prediction['2nd_place'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($driver['driver_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="3rd_place">3rd Place:</label>
                <select id="3rd_place" name="3rd_place" <?php echo !$is_editable ? 'disabled' : ''; ?> required>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>" <?php echo (isset($prediction['3rd_place']) && $prediction['3rd_place'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($driver['driver_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="fastest_lap">Fastest Lap:</label>
                <select id="fastest_lap" name="fastest_lap" <?php echo !$is_editable ? 'disabled' : ''; ?> required>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>" <?php echo (isset($prediction['fastest_lap']) && $prediction['fastest_lap'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($driver['driver_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="retirements">Any Retirements?</label>
                <input type="checkbox" id="retirements" name="retirements" <?php echo isset($prediction['retirements']) && $prediction['retirements'] ? 'checked' : ''; ?> <?php echo !$is_editable ? 'disabled' : ''; ?>><br><br>

                <?php if ($is_editable): ?>
                    <button type="submit" name="submit_prediction">Submit Prediction</button>
                <?php endif; ?>
            </form>

        <?php } else { ?>
            <p>Predictions are closed.</p>
        <?php } ?>

        <?php if (!$prediction): ?>
            <p>You made no predictions for this race.</p>
        <?php endif; ?>
    </div>
</body>

</html>