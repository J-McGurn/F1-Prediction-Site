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
$url = "https://www.gpfans.com/en/f1-race-calendar/singapore-grand-prix-2024";

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
    $driverCell = $cells[1]->find('div a b');
    $first = $driverCell ? trim($driverCell[0]->text) : 'N/A';
    echo $first;

    $row = $rows[1];
    $cells = $row->find('td');
    $driverCell = $cells[1]->find('div a b');
    $second = $driverCell ? trim($driverCell[0]->text) : 'N/A';
    echo $second;

    $row = $rows[2];
    $cells = $row->find('td');
    $driverCell = $cells[1]->find('div a b');
    $third = $driverCell ? trim($driverCell[0]->text) : 'N/A';
    echo $third;

    //DNF's
    $dnfCount = 0;
    foreach ($rows as $row) {
        // Get all the cells in the current row
        $cells = $row->find('td');
        // Make sure there are enough cells in the row before accessing
        if (isset($cells[0])) {
            $position = $cells[0]->text; // Get the text of the 6th column (zero-indexed)
            // Check for DNF (this might vary depending on the content format, adjust as needed)
            if (stripos($position, 'DNF') !== false) {
                $dnfCount++;
            }
        }
    }
    echo $dnfCount;    
}
?>