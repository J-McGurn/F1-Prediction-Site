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
    <link rel="stylesheet" href="css/predictions.css"> <!-- Link to the CSS file -->
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

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

        <?php if ($is_open) { ?>
            <form method="post" action="predictions.php?race_id=<?php echo $race_id; ?>" class="prediction-form">
                <label for="first_place">1st Place:</label>
                <input type="text" id="first_place" name="first_place" value="<?php echo htmlspecialchars($prediction['first_place'] ?? ''); ?>" <?php echo !$is_editable ? 'readonly' : ''; ?> required><br><br>

                <label for="second_place">2nd Place:</label>
                <input type="text" id="second_place" name="second_place" value="<?php echo htmlspecialchars($prediction['second_place'] ?? ''); ?>" <?php echo !$is_editable ? 'readonly' : ''; ?> required><br><br>

                <label for="third_place">3rd Place:</label>
                <input type="text" id="third_place" name="third_place" value="<?php echo htmlspecialchars($prediction['third_place'] ?? ''); ?>" <?php echo !$is_editable ? 'readonly' : ''; ?> required><br><br>

                <label for="fastest_lap">Fastest Lap:</label>
                <input type="text" id="fastest_lap" name="fastest_lap" value="<?php echo htmlspecialchars($prediction['fastest_lap'] ?? ''); ?>" <?php echo !$is_editable ? 'readonly' : ''; ?> required><br><br>

                <label for="any_retirements">Any Retirements?</label>
                <input type="checkbox" id="any_retirements" name="any_retirements" <?php echo isset($prediction['any_retirements']) && $prediction['any_retirements'] ? 'checked' : ''; ?> <?php echo !$is_editable ? 'disabled' : ''; ?>><br><br>

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