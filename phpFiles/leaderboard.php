<?php
    session_start();
    require 'config.php';  // Ensure connection to your MySQL database

    // Get current datetime
    $current_datetime = new DateTime();

    // Determine the race_id for leaderboard display based on pagination
    if (isset($_GET['race_id'])) {
        $race_id = (int)$_GET['race_id'];
    } else {
        // Fetch the most recent race that has completed
        $last_completed_race_query = "SELECT race_id FROM races WHERE race_date < NOW() ORDER BY race_date DESC LIMIT 1";
        $last_completed_race_result = $conn->query($last_completed_race_query);
        $last_completed_race = $last_completed_race_result->fetch_assoc();
        $race_id = $last_completed_race['race_id'] ?? null;
    }

    // Fetch race details
    $race_query = "SELECT * FROM races WHERE race_id = ?";
    $stmt = $conn->prepare($race_query);
    $stmt->bind_param("i", $race_id);
    $stmt->execute();
    $race_result = $stmt->get_result();
    $race = $race_result->fetch_assoc();
    $stmt->close();

    // Fetch the previous, next, current, and first race IDs for navigation
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

    // Fetch weekly leaderboard for the selected race using the provided query
    $weekly_query = "
        SELECT u.user_id, CONCAT(u.fname, ' ', u.sname) AS full_name, ws.points 
        FROM users u
        JOIN weekly_standings ws ON u.user_id = ws.user_id
        WHERE ws.race_id = ?
        ORDER BY ws.points DESC";
        
    $stmt = $conn->prepare($weekly_query);
    $stmt->bind_param("i", $race_id);
    $stmt->execute();
    $weekly_leaderboard_result = $stmt->get_result();
    $stmt->close();

    // Fetch season leaderboard using the provided query
    $season_query = "
        SELECT u.user_id, CONCAT(u.fname, ' ', u.sname) AS full_name, us.points 
        FROM users u
        JOIN user_standings us ON u.user_id = us.user_id
        ORDER BY us.points DESC";

    $season_leaderboard_result = $conn->query($season_query);

    // Determine if the "Next Race" button should be displayed
    $show_next_race_button = $current_race_id !== $race_id && $next_race_id !== null;

    include "hotbar.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="icon" href="../images/f1.png">
    <link rel="stylesheet" href="../CSS/leaderboard.css"> <!-- Optional CSS -->
</head>
<body>
    <!-- Race Navigation -->
    <div class="race-navigation">
        <?php if (isset($first_race_id)): ?>
            <?php if ($race_id != $first_race_id): ?>
                <a href="leaderboard.php?race_id=<?php echo $first_race_id; ?>" class="button">|<<</a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($prev_race_id)): ?>
            <a href="leaderboard.php?race_id=<?php echo $prev_race_id; ?>" class="button"><</a>
        <?php endif; ?>

        <span class="current-race">
            <?php echo htmlspecialchars($race['race_name']); ?>
        </span>

        <?php if (isset($next_race_id) && $show_next_race_button): ?>
            <a href="leaderboard.php?race_id=<?php echo $next_race_id; ?>" class="button">></a>
        <?php endif; ?>

        <?php if ($current_race_id && $current_race_id != $race_id): ?>
            <a href="leaderboard.php?race_id=<?php echo $current_race_id; ?>" class="button">>>|</a>
        <?php endif; ?>
    </div>

    <!-- Weekly Leaderboard -->
    <h2>Weekly Leaderboard: <?php echo htmlspecialchars($race['race_name']); ?></h2>
    <table>
        <tr>
            <th>Rank</th>
            <th>Name</th>
            <th>Points</th>
        </tr>
        <?php 
        $rank = 1;
        while ($row = $weekly_leaderboard_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['points']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Season Leaderboard -->
    <h2>Season Leaderboard</h2>
    <table>
        <tr>
            <th>Rank</th>
            <th>Name</th>
            <th>Total Points</th>
        </tr>
        <?php 
        $rank = 1;
        while ($row = $season_leaderboard_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $rank++; ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['points']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>