<?php 
// Database server settings, using MySQL default
$DB_SERVER = 'localhost';
$DB_USERNAME = 'root';
$DB_PASSWORD = '';
$DB_NAME = 'recLeague';

// Attempt to connect to database
$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

if (!$conn) {
    die("Connection Error: " . mysqli_connect_error());
}

?>