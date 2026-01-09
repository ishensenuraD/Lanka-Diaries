<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connection2.php");

echo "Database connection: " . ($conn ? "Success" : "Failed") . "<br>";

if ($_POST) {
    echo "POST data received:<br>";
    print_r($_POST);
    
    if (isset($_POST['submit'])) {
        $name = $_POST['name'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $country = $_POST['country'] ?? '';
        $nic = $_POST['nic'] ?? '';
        $sdate = $_POST['Sdate'] ?? '';
        $edate = $_POST['Edate'] ?? '';
        $size = $_POST['size'] ?? '';
        $budget = 0;
        $preference = $_POST['preference'] ?? '';
        $email = $_POST['email'] ?? '';
        $notes = $_POST['notes'] ?? '';

        echo "<br>Variables set:<br>";
        echo "Name: $name<br>";
        echo "Contact: $contact<br>";
        echo "Size: $size<br>";

        $stmt = $conn->prepare("INSERT INTO trip_pack 
            (FullName, ContactNumber, Country, NIC, StartDate, EndDate, GroupSize, Budget, Preference, Email, Notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            echo "Prepare failed: " . $conn->error;
        } else {
            $bind_result = $stmt->bind_param("ssssssissss", $name, $contact, $country, $nic, $sdate, $edate, $size, $budget, $preference, $email, $notes);
            
            if ($bind_result === false) {
                echo "Bind failed: " . $stmt->error;
            } else {
                if ($stmt->execute()) {
                    echo "Success! Data inserted.";
                } else {
                    echo "Execute failed: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
} else {
    echo "No POST data. Please submit the form first.";
}

$conn->close();
?>
