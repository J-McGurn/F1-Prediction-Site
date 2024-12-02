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
    <link rel="icon" href="../images/f1.png">
    <link rel="stylesheet" href="../css/predictions.css"> <!-- Link to the CSS file -->
</head>

<body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customDropdowns = document.querySelectorAll('.custom-dropdown'); // Select all custom dropdowns
        
        customDropdowns.forEach(customDropdown => {
            const selectElement = customDropdown.querySelector('select');
            
            // Create the container for showing the selected option (with image and text)
            const dropdownSelected = document.createElement('div');
            dropdownSelected.classList.add('dropdown-selected');
            customDropdown.appendChild(dropdownSelected);
            
            // Create the list of options to be shown when the dropdown is opened
            const dropdownList = document.createElement('div');
            dropdownList.classList.add('dropdown-list');
            customDropdown.appendChild(dropdownList);

            // Loop through the select options and create custom divs with images
            Array.from(selectElement.options).forEach(option => {
                const optionElement = document.createElement('div');
                
                // Set image and text for each option
                optionElement.innerHTML = `<img src="${option.getAttribute('data-image')}" alt="${option.text}"><span>${option.text}</span>`;
                optionElement.dataset.value = option.value;
                dropdownList.appendChild(optionElement);

                // When an option is clicked, set the value and update the displayed image
                optionElement.addEventListener('click', () => {
                    selectElement.value = option.value; // Update the hidden select value
                    dropdownSelected.innerHTML = `<img src="${option.getAttribute('data-image')}" alt="${option.text}"><span>${option.text}</span>`;
                    dropdownList.style.display = 'none'; // Hide the dropdown list
                });
            });

            // Show/hide the dropdown list when the selected area is clicked
            dropdownSelected.addEventListener('click', () => {
                dropdownList.style.display = dropdownList.style.display === 'block' ? 'none' : 'block';
            });

            // Set the initially selected image (or fallback to qmark.png if nothing is selected)
            const selectedOption = selectElement.querySelector('option[selected]') || selectElement.querySelector('option[value="0"]');
            if (selectedOption) {
                dropdownSelected.innerHTML = `<img src="${selectedOption.getAttribute('data-image')}" alt="${selectedOption.text}"><span>${selectedOption.text}</span>`;
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Select all the images with the class "selectable-image"
        const selectableImages = document.querySelectorAll('.selectable-image');
        
        selectableImages.forEach(image => {
            image.addEventListener('click', function () {
                // Get the H2H number (either "h2h1" or "h2h2") from the data-h2h attribute
                const h2hType = this.getAttribute('data-h2h');
                
                // Remove 'selected' class from all images in the current H2H block
                document.querySelectorAll(`.selectable-image[data-h2h="${h2hType}"]`).forEach(img => img.classList.remove('selected'));

                // Add 'selected' class to the clicked image
                this.classList.add('selected');

                // Update the correct hidden input for H2H1 or H2H2 with the selected driver's data-id
                document.getElementById(`${h2hType}_selection`).value = this.getAttribute('data-id');
            });
        });
    });

</script>

    <div class="container">
        <h1>F1 Predictions for:</h1>

        <div class="race-navigation">
            <?php if (isset($first_race_id)): ?>
                <?php if ($race_id != $first_race_id): ?>
                    <a href="predictions.php?race_id=<?php echo $first_race_id; ?>" class="button">|<<</a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($prev_race_id)): ?>
                        <a href="predictions.php?race_id=<?php echo $prev_race_id; ?>" class="button">
                            <</a>
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
            Deadline for submitting predictions: <?php echo $deadline_formatted; ?> UK Time
        </p>

        <?php if ($is_open) { ?>
            <form method="post" action="predictions.php?race_id=<?php echo $race_id; ?>" class="prediction-form">
            <table>
                <tr>
                    <td>
                        <div class="custom-dropdown">
                            <select id="2nd_place" name="2nd_place" <?php echo !$is_editable ? 'disabled' : ''; ?> required>
                                <option value=0 data-image="<?php echo htmlspecialchars('../images/qmark.png'); ?>"></option>
                                <?php foreach($drivers as $driver): ?>
                                    <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>"
                                            data-image="<?php echo htmlspecialchars('../images/' . $driver['image_filename']); ?>"
                                            <?php echo (isset($prediction['2nd_place']) && $prediction['2nd_place'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="custom-dropdown">
                            <select id="1st_place" name="1st_place" <?php echo !$is_editable ? 'disabled' : ''; ?> required>
                                <option value=0 data-image="<?php echo htmlspecialchars('../images/qmark.png'); ?>"></option>
                                <?php foreach($drivers as $driver): ?>
                                    <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>"
                                            data-image="<?php echo htmlspecialchars('../images/' . $driver['image_filename']); ?>"
                                            <?php echo (isset($prediction['1st_place']) && $prediction['1st_place'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="custom-dropdown">
                            <select id="3rd_place" name="3rd_place" <?php echo !$is_editable ? 'disabled' : ''; ?> required>
                                <option value=0 data-image="<?php echo htmlspecialchars('../images/qmark.png'); ?>"></option>
                                <?php foreach($drivers as $driver): ?>
                                    <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>"
                                            data-image="<?php echo htmlspecialchars('../images/' . $driver['image_filename']); ?>"
                                            <?php echo (isset($prediction['3rd_place']) && $prediction['3rd_place'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr class="podium-block">
                    <td><img src ="../images/silver.png" class="silver"></td>
                    <td><img src ="../images/gold.png" class="gold"></td>
                    <td><img src ="../images/bronze.png"></td>
                </tr>
            </table>

            <h2>Head-to-Head 1</h2>
            <div class="special-image-selection">
                <div class="special-image">
                    <img src="../images/<?php echo htmlspecialchars($h2h1['driver1_image']); ?>" 
                        alt="<?php echo htmlspecialchars($h2h1['driver1_name']); ?>" 
                        class="selectable-image" 
                        data-id="<?php echo htmlspecialchars($h2h1['driver1_id']); ?>"
                        data-h2h="h2h1" />
                    <p><?php echo htmlspecialchars($h2h1['driver1_name']); ?></p>
                </div>

                <div class="vs-text">VS</div>

                <div class="special-image">
                    <img src="../images/<?php echo htmlspecialchars($h2h1['driver2_image']); ?>" 
                        alt="<?php echo htmlspecialchars($h2h1['driver2_name']); ?>" 
                        class="selectable-image" 
                        data-id="<?php echo htmlspecialchars($h2h1['driver2_id']); ?>"
                        data-h2h="h2h1" />
                    <p><?php echo htmlspecialchars($h2h1['driver2_name']); ?></p>

                </div>
            </div>

            <!-- Hidden input to store the selected driver for H2H 1 -->
            <input type="hidden" id="h2h1_selection" name="h2h1_selection" value="" required />

            <h2>Head-to-Head 2</h2>
            <div class="special-image-selection">
                <div class="special-image">
                    <img src="../images/<?php echo htmlspecialchars($h2h2['driver1_image']); ?>" 
                        alt="<?php echo htmlspecialchars($h2h2['driver1_name']); ?>" 
                        class="selectable-image" 
                        data-id="<?php echo htmlspecialchars($h2h2['driver1_id']); ?>"
                        data-h2h="h2h2" />
                    <p><?php echo htmlspecialchars($h2h2['driver1_name']); ?></p>
                </div>

                <div class="vs-text">VS</div>

                <div class="special-image">
                    <img src="../images/<?php echo htmlspecialchars($h2h2['driver2_image']); ?>" 
                        alt="<?php echo htmlspecialchars($h2h2['driver2_name']); ?>" 
                        class="selectable-image" 
                        data-id="<?php echo htmlspecialchars($h2h2['driver2_id']); ?>"
                        data-h2h="h2h2" />
                    <p><?php echo htmlspecialchars($h2h2['driver2_name']); ?></p>
                </div>
            </div>

            <!-- Hidden input to store the selected driver for H2H 2 -->
            <input type="hidden" id="h2h2_selection" name="h2h2_selection" value="" required />


  
            
            







                <label for="fastest_lap">Fastest Lap:</label>
                <select id="fastest_lap" name="fastest_lap" <?php echo !$is_editable ? 'disabled' : ''; ?> required>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>" <?php echo (isset($prediction['fastest_lap']) && $prediction['fastest_lap'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($driver['driver_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="retirements">Select Number of Retirements:</label>
                <div class="retirement-options">
                    <?php
                    $retirement_options = [
                        '0' => '0',
                        '1' => '1-2',
                        '3' => '3-4',
                        '5' => '5+',
                    ];
                    ?>
                    <?php foreach ($retirement_options as $value => $label): ?>
                        <button type="button" class="retirement-option" data-value="<?php echo htmlspecialchars($value); ?>">
                            <?php echo htmlspecialchars($label); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="retirements" id="retirements" value ="" required>

                <script>
                    document.querySelectorAll('.retirement-option').forEach(button => {
                        button.addEventListener('click', function() {
                            // Remove 'selected' class from all buttons
                            document.querySelectorAll('.retirement-option').forEach(btn => btn.classList.remove('selected'));
                            // Add 'selected' class to the clicked button
                            this.classList.add('selected');
                            // Set the hidden input value to the selected button's value
                            document.getElementById('retirements').value = this.getAttribute('data-value');
                        });
                    });
                </script>

                <?php if ($is_editable): ?>
                    <button type="submit" name="submit_prediction">Submit Prediction</button>
                <?php endif; ?>
            </form>

        <?php } else { ?>
            <p>Predictions are closed.</p>
            <?php include 'show-predictions.php'; ?>
        <?php } ?>

        <?php if (isset($message)) {
            echo "<p>$message</p>";
        } ?>
    </div>
</body>

</html>