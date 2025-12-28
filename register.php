<?php
include("connection.php");

if (isset($_POST['submit'])) {
    $name = $_POST['name'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $country = $_POST['country'] ?? '';
    $nic = $_POST['nic'] ?? '';
    $sdate = $_POST['Sdate'] ?? '';
    $edate = $_POST['Edate'] ?? '';
    $size = $_POST['size'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $preference = $_POST['preference'] ?? '';
    $email = $_POST['email'] ?? '';
    $notes = $_POST['notes'] ?? '';


    $stmt = $conn->prepare("INSERT INTO trip_plan (FullName, ContactNumber, Country, NIC, StartDate, EndDate, GroupSize, Budget, Preference, Email, Notes) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssissss", $name, $contact, $country, $nic, $sdate, $edate, $size, $budget, $preference, $email, $notes);


    if ($stmt->execute()) {
        header("Location: planning.html?status=success");
        exit();
    } else {
        header("Location: planning.html?status=fail");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
