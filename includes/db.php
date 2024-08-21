<?php
$servername = "localhost";  // Update if necessary
$username = "root";         // Update if necessary
$password = "";             // Update if necessary
$dbname = "school_management";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
