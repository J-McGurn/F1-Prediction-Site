<?php
require 'config.php';
require '../vendor/autoload.php';
use PHPHtmlParser\Dom;
$dom = new Dom;

$race_id = (int)$_GET['race_id'];
$raceUrl_query = "SELECT url FROM races WHERE race_id = ?";
$stmt = $conn->prepare($raceUrl_query);
$stmt->bind_param("i", $race_id);
$stmt->execute();

$result = $stmt->get_result();
$race = $result->fetch_assoc();
$stmt->close();

$race_url = $race['url'];
$url = "https://www.skysports.com/f1/grandprix/emilia-romagna/results/2024/race";

// Load the URL
$dom->loadFromUrl($url);

// Try to find the correct table (inspect the page to verify the correct selector)
$table = $dom->find('table'); // Try 'table' or a more specific selector if necessary

$data = [];
if ($table) {
    // Find all rows inside the table body
    $rows = $table->find('tbody tr');

    //Podium Places
    $row = $rows[0];
    $cells = $row->find('td');
    $driverCell = $cells[1]->find('span.standing-table__cell--name-text');
    $first = $driverCell ? trim($driverCell[0]->text) : 'N/A';
    echo $first;

    $row = $rows[1];
    $cells = $row->find('td');
    $driverCell = $cells[1]->find('span.standing-table__cell--name-text');
    $second = $driverCell ? trim($driverCell[0]->text) : 'N/A';
    echo $second;

    $row = $rows[2];
    $cells = $row->find('td');
    $driverCell = $cells[1]->find('span.standing-table__cell--name-text');
    $third = $driverCell ? trim($driverCell[0]->text) : 'N/A';
    echo $third;

    //Fastest Lap
    $fastestLapTime = PHP_INT_MAX;
    $fastestDriver = 'N/A';

    foreach ($rows as $row) {
        $cells = $row->find('td');
        if (isset($cells[6])) {
            $lapTime = trim($cells[6]->text);
            if (preg_match('/\d{1}:\d{2}\.\d{3}/', $lapTime)) {
                $timeParts = explode(':', $lapTime);
                $seconds = $timeParts[0] * 60 + (float)$timeParts[1];

                if ($seconds < $fastestLapTime) {
                    $fastestLapTime = $seconds;
                    $driverCell = $cells[1]->find('span.standing-table__cell--name-text');
                    $fastestDriver = $driverCell ? trim($driverCell[0]->text) : 'N/A';
                }
            }
        }
    }
    echo "Fastest lap by: " . $fastestDriver . '<br>';

    //DNF's
    $dnfCount = 0;
    foreach ($rows as $row) {
        // Get all the cells in the current row
        $cells = $row->find('td');
        // Make sure there are enough cells in the row before accessing
        if (isset($cells[0])) {
            $position = $cells[0]->text; // Get the text of the 6th column (zero-indexed)
            // Check for DNF (this might vary depending on the content format, adjust as needed)
            if (stripos($position, 'RET') !== false) {
                $dnfCount++;
            }
        }
    }
    echo $dnfCount;    
}
?>