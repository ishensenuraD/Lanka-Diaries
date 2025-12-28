<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "TripPlanner";

// Connect to MySQL server
$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the database
mysqli_select_db($conn, $dbname);

// Create table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS trip_pack (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(100) NOT NULL,
    ContactNumber VARCHAR(20),
    Country VARCHAR(50),
    NIC VARCHAR(20),
    StartDate DATE,
    EndDate DATE,
    GroupSize INT,
    Budget DECIMAL(10,2),
    Preference VARCHAR(50),
    Email VARCHAR(100),
    Notes TEXT
)";

if (!mysqli_query($conn, $table_sql)) {
    die("Error creating table: " . mysqli_error($conn));
}
?>
