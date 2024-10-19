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

function h2hQuery($race_id, $h2h_number, $conn, $rows) {
    $h2h_query = "SELECT driver1_id, driver2_id FROM h2h WHERE race_id = ? AND h2h_number = ?";
    $stmt = $conn->prepare($h2h_query);
    $stmt->bind_param("ii", $race_id, $h2h_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver_ids = $result->fetch_assoc();
    $stmt->close();

    $driver1_query = "SELECT driver_name FROM active_drivers WHERE driver_id = ?";
    $stmt1 = $conn->prepare($driver1_query);
    $stmt1->bind_param("i", $driver_ids['driver1_id']);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $driver1 = $result1->fetch_assoc()['driver_name'];

    $driver2_query = "SELECT driver_name FROM active_drivers WHERE driver_id = ?";
    $stmt2 = $conn->prepare($driver2_query);
    $stmt2->bind_param("i", $driver_ids['driver2_id']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $driver2 = $result2->fetch_assoc()['driver_name'];

    foreach($rows as $row) {
        $cells = $row->find('td');
        $driverCell = $cells[1]->find('span.standing-table__cell--name-text');
        $driver = $driverCell ? trim($driverCell[0]->text) : 'N/A';
        if($driver == $driver1) {
            return nameToId($driver, $conn);
        }
        elseif ($driver == $driver2) {
            return nameToId($driver, $conn);
        }
    }
}

function nameToId($driverName, $conn) {
    $query = "SELECT driver_id FROM active_drivers WHERE driver_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $driverName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch as associative array
        return $row['driver_id']; // Return driver_id
    } else {
        return null; // No driver found
    }
    $stmt->close();
}




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
    $first = nameToId($first, $conn);

    $row = $rows[1];
    $cells = $row->find('td');
    $driverCell = $cells[1]->find('span.standing-table__cell--name-text');
    $second = $driverCell ? trim($driverCell[0]->text) : 'N/A';
    $second = nameToId($second, $conn);

    $row = $rows[2];
    $cells = $row->find('td');
    $driverCell = $cells[1]->find('span.standing-table__cell--name-text');
    $third = $driverCell ? trim($driverCell[0]->text) : 'N/A';
    $third = nameToId($third, $conn);

    //H2H's
    $h2h1 = h2hQuery(1, 1, $conn, $rows);
    $h2h2 = h2hQuery(1, 2, $conn, $rows);


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
    $fastestDriver = nameToId($fastestDriver, $conn);

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
}

$race_id = 1;
$insert = "INSERT INTO race_results (
    race_id, 1st_place, 2nd_place, 3rd_place, h2h_1, h2h_2, fastest_lap, retirements)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($insert);
$stmt-> bind_param(
    "iiiiiiii",
    $race_id, $first, $second, $third, $h2h1, $h2h2, $fastestDriver, $dnfCount
);
$stmt->execute();        
?>