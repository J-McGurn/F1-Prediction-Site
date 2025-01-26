<?php
include 'hotbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Points System</title>
    <link rel="icon" href="../images/f1.png">
    <link rel="stylesheet" href="../css/points_system.css">
</head>

<body>
    <div class="predictions-box">
        <h1>Drivers Predictions</h1>
        <p>Predicting the entire podium correctly earns you <strong>10 points</strong>.</p>
        <p>Predicting a driver on the podium but in the wrong position earns you <strong>3 points</strong> per driver.</p>
    </div>

    <div class="predictions-box">
        <h1>Head-to-Head</h1>
        <p>Predicting each head-to-head correctly earns you <strong>1 point</strong>.</p>
        <p>For a maximum of <strong>2 points</strong>.</p>
    </div>

    <div class="predictions-box">
        <h1>Fastest Lap</h1>
        <p>Predicting the driver with the fastest lap correctly earns you <strong>1 point</strong>.</p>
    </div>

    <div class="predictions-box">
        <h1>No. Of Retirements</h1>
        <p>Predicting the correct number of retirements earns you <strong>3 points</strong>.</p>
        <p>If your guess is off by one, you still earn <strong>1 point</strong>.</p>
    </div>
</body>

</html>