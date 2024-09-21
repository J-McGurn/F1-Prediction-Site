<?php
$host = '127.0.0.1'; // The IP address of your MySQL server
$db = 'f1predictions'; // The name of the database
$user = 'root'; // The MySQL username
$pass = 'BillySQL945!'; // The MySQL password

// Create MySQLi connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>