<?php
$host = '127.0.0.1'; // This is the IP address of your MySQL server. '127.0.0.1' is the local server (your computer).
$db = 'f1predictions'; // The name of the database you want to connect to.
$user = 'root'; // The MySQL username. 'root' is the default for XAMPP.
$pass = 'BillySQL945!'; // The MySQL password. Leave this blank if you're using XAMPP's default settings.
$charset = 'utf8mb4'; // Character set used in the database to handle special characters.

$dsn = "mysql:host=$host;dbname=$db;charset=$charset"; // Data Source Name (DSN) specifies the host, database, and charset.

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Sets the error mode to exception, which means errors will throw exceptions.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Sets the default fetch mode to associative array, which is easier to work with in PHP.
    PDO::ATTR_EMULATE_PREPARES   => false, // Disables emulation of prepared statements, allowing real prepared statements for better security.
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options); // Creates a new PDO instance and attempts to connect to the database.
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode()); // Catches any errors and throws a PDOException with the error message.
}
