<?php
    include "hotbar.php";

    $data = json_decode(file_get_contents('../data/drivers.json'), true);
?>

<html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Drivers's Standings</title>
        <link rel="icon" href="../images/f1.png">
        <link rel="stylesheet" href="../css/drivers.css">
    </head>
    <body>
    <h1>F1 2024 Driver's Championship</h1>

    <table>
        <tr>
            <th>POSITION</th>
            <th>TEAM</th>
            <th>DRIVER</th>
            <th>POINTS</th>
        </tr>
        <?php if($data): ?>
            <?php foreach ($data as $entry): ?>
                <tr>
                    <td class="position"><?php echo htmlspecialchars($entry['position']); ?>.</td>
                    <td><img src="<?php echo htmlspecialchars($entry['logo']); ?>" alt="Team Logo" width="50" height="50"></td>
                    <td><?php echo htmlspecialchars($entry['driver']); ?></td>
                    <td class="points"><?php echo htmlspecialchars($entry['points']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No data available.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>