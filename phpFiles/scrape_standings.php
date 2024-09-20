<?php
require '../vendor/autoload.php';

use PHPHtmlParser\Dom;

$dom = new Dom;
$url = "https://www.gpfans.com/en/f1-standings/constructors/2024/";

$dom->loadFromUrl($url);
$table = $dom->find('table.full.ranklist')[0];
$data = [];

if ($table) {
    $rows = $table->find('tbody tr');
    echo "<table border='1'>";
    echo "<tr><th>Team</th><th>Points</th></tr>"; // Header row
    foreach ($rows as $row) {
        $cells = $row->find('td');
        if (count($cells) >= 4) {
            $teamCell = $cells[1]->find('h2');
            $team = $teamCell ? trim($teamCell[0]->text) : 'N/A';
            $points = trim($cells[3]->text);

            $data[] = [
                'team' => $team,
                'points' => $points
            ];

            echo "<tr>";
            echo "<td>$team</td>";
            echo "<td>$points</td>";
            echo "</tr>";
        }
        
    }
    echo "</table>";

    // Save the scraped data into a JSON file
    file_put_contents('../data/constructors.json', json_encode($data));
} else {
    echo "Table not found.";
}
?>
