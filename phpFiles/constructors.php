<?php
    include "hotbar.php";

    $data = json_decode(file_get_contents('../data/constructors.json'), true);
?>

<html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Constructor's Standings</title>
        <link rel="icon" href="../images/f1.png">
        <link rel="stylesheet" href="../css/constructors.css">
    </head>
    <body>
    <h1>F1 2024 Constructor's Championship</h1>
    <table>
                <tr>
                    <th>POSITION</th>
                    <th>
                    <th class="team">CONSTRUCTOR</th>
                    <th>POINTS</th>
                </tr>
                <?php if ($data): ?>
                    <?php foreach ($data as $entry): ?>
                        <?php
                        $position = htmlspecialchars($entry['position']);
                        $team = htmlspecialchars($entry['team']);
                        $points = htmlspecialchars($entry['points']);
                        $logo = htmlspecialchars($entry['logo']);
                        ?>
                        <tr>
                            <td class="position"><?php echo $position; ?>.</td>
                            <td><img src="<?php echo $logo; ?>" alt="<?php echo $team; ?>" class="team"></td>
                            <td><?php echo $team; ?></td>
                            <td class="points"><?php echo $points; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No data available.</td>
                    </tr>
                <?php endif; ?>
            </table>
    </body>
</html>