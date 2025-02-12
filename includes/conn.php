<?php
require_once("db-conf.php");
require_once("conf.php");
require_once("language_strings.php");

$conn = null; // Initialize the variable to avoid undefined variable notice

try {
    // Establish database connection using MySQLi
    $conn = new mysqli(SQL_HOST, SQL_USER, SQL_PASS, SQL_DB);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error); // Throw exception if connection failed
    }

    // Set character set to UTF-8
    if (!$conn->set_charset("utf8mb4")) { // Change to utf8mb4 if you need full Unicode support
        throw new Exception("Error loading character set utf8mb4: " . $conn->error);
    }

} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Further database operations would go here

// Close the connection
/*if ($conn) {
    $conn->close();
}*/
?>

