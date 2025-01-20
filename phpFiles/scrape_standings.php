<?php
require '../vendor/autoload.php';
use PHPHtmlParser\Dom;

$teamLogos = [
    'Red Bull Racing' => '../images/redbull.png',
    'Mercedes' => '../images/mercedes.png',
    'Ferrari' => '../images/ferrari.png',
    'Aston Martin' => '../images/astonmartin.png',
    'McLaren' => '../images/mclaren.png',
    'BWT Alpine' => '../images/alpine.png',
    'Visa Cash App RB' => '../images/rb.png',
    'Stake F1 Team' => '../images/stake.png',
    'Haas F1 Team' => '../images/haas.png',
    'Williams' => '../images/williams.png',
];

// CONSTRUCTORS
$dom = new Dom;
$url = "https://www.gpfans.com/en/f1-standings/constructors/2025/";

$dom->loadFromUrl($url);
$table = $dom->find('table.full.ranklist')[0];
$data = [];

if ($table) {
    $rows = $table->find('tbody tr');
    foreach ($rows as $row) {
        $cells = $row->find('td');
        if (count($cells) >= 4) {
            $position = trim($cells[0]->text);
            $teamCell = $cells[1]->find('h2');
            $team = $teamCell ? trim($teamCell[0]->text) : 'N/A';
            $points = trim($cells[3]->text);
            $logo = isset($teamLogos[$team]) ? $teamLogos[$team] : '../images/f1.png';


            $data[] = [
                'position' => $position,
                'team' => $team,
                'points' => $points,
                'logo' => $logo
            ];
        }
    }

    // Save the scraped data into a JSON file
    file_put_contents(__DIR__ . '/../data/constructors.json', json_encode($data));
} else {
    echo "Table not found.";
}

// DRIVERS
$dom = new Dom;
$url = "https://www.gpfans.com/en/f1-standings/2024/";  // Update to the drivers' standings URL

$dom->loadFromUrl($url);
$table = $dom->find('table.full.ranklist')[0];  // Find the correct table for drivers' standings
$data = [];

if ($table) {
    $rows = $table->find('tbody tr');
    echo "<table border='1'>";
    echo "<tr><th>Driver</th><th>Points</th></tr>"; // Update the header for drivers

    foreach ($rows as $row) {
        $cells = $row->find('td');
        if (count($cells) >= 4) {
            // Extract driver name (adjust the structure if needed for the driver table)
            $position = trim($cells[0]->text);
            $team = trim($cells[2]->text);
            $driverCell = $cells[1]->find('div a');
            $driver = $driverCell ? trim($driverCell[0]->text) : 'N/A';
            $points = trim($cells[3]->text);
            $logo = isset($teamLogos[$team]) ? $teamLogos[$team] : '../images/f1.png';
            // Store the data
            $data[] = [
                'position' => $position,
                'driver' => $driver,
                'points' => $points,
                'logo' => $logo
            ];

            // Output the data in a table
            echo "<tr>";
            echo "<td>$team</td>";
            echo "<td>$driver</td>";
            echo "<td>$points</td>";
            echo "</tr>";
        }
    }
    echo "</table>";

    // Save the scraped data into a JSON file
    file_put_contents(__DIR__ . '/../data/drivers.json', json_encode($data));
} else {
    echo "Table not found.";
}
?>