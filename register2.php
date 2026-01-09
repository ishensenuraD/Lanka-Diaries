<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("connection2.php");

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_form_data($name, $contact, $country, $nic, $sdate, $edate, $size, $budget, $preference, $email, $notes) {
    $errors = [];
    
    // 1. Required Field Validation
    if (empty($name)) $errors[] = "Name is required!";
    if (empty($contact)) $errors[] = "Contact number is required!";
    if (empty($nic)) $errors[] = "NIC is required!";
    if (empty($email)) $errors[] = "Email is required!";
    
    // 2. Name Validation
    if (!empty($name) && !preg_match("/^[a-zA-Z\s]{2,50}$/", $name)) {
        $errors[] = "Invalid name format! Name must be 2-50 letters and spaces only.";
    }
    
    // 3. Contact Number Validation
    if (!empty($contact) && !preg_match("/^\+?[0-9]{10,15}$/", $contact)) {
        $errors[] = "Invalid contact number! Must be 10-15 digits (can start with +).";
    }
    
    // 4. Email Validation
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }
    
    // 5. NIC Validation (Sri Lankan format)
    if (!empty($nic) && !preg_match("/^[0-9]{9}[vVxX]$|^[0-9]{12}$/", $nic)) {
        $errors[] = "Invalid NIC format! Use 9 digits + letter or 12 digits.";
    }
    
    // 6. Date Validation
    if (!empty($sdate) && !empty($edate)) {
        $start_timestamp = strtotime($sdate);
        $end_timestamp = strtotime($edate);
        $today_timestamp = strtotime(date('Y-m-d'));
        $max_timestamp = strtotime('+2 years');
        
        if (!$start_timestamp || !$end_timestamp) {
            $errors[] = "Invalid date format!";
        } else {
            if ($start_timestamp < $today_timestamp) {
                $errors[] = "Start date cannot be in the past!";
            }
            if ($start_timestamp >= $end_timestamp) {
                $errors[] = "End date must be after start date!";
            }
            if ($start_timestamp > $max_timestamp || $end_timestamp > $max_timestamp) {
                $errors[] = "Dates cannot be more than 2 years in advance!";
            }
        }
    }
    
    // 7. Group Size Validation
    if (!empty($size) && (!is_numeric($size) || $size < 1 || $size > 50)) {
        $errors[] = "Group size must be between 1 and 50!";
    }
    
    // 8. Budget Validation
    if (!empty($budget) && (!is_numeric($budget) || $budget < 0)) {
        $errors[] = "Budget must be a positive number!";
    }
    
    // 9. Country Validation
    if (!empty($country) && (strlen($country) < 2 || strlen($country) > 50)) {
        $errors[] = "Country name must be between 2 and 50 characters!";
    }
    
    // 10. Notes Length Validation
    if (!empty($notes) && strlen($notes) > 500) {
        $errors[] = "Notes cannot exceed 500 characters!";
    }
    
    return $errors;
}

if (isset($_POST['submit'])) {
    $name = sanitize_input($_POST['name'] ?? '');
    $contact = sanitize_input($_POST['contact'] ?? '');
    $country = sanitize_input($_POST['country'] ?? '');
    $nic = sanitize_input($_POST['nic'] ?? '');
    $sdate = sanitize_input($_POST['Sdate'] ?? '');
    $edate = sanitize_input($_POST['Edate'] ?? '');
    $size = sanitize_input($_POST['size'] ?? '');
    $budget = sanitize_input($_POST['budget'] ?? 0);
    $preference = sanitize_input($_POST['preference'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $notes = sanitize_input($_POST['notes'] ?? '');

    // Validate all form data
    $validation_errors = validate_form_data($name, $contact, $country, $nic, $sdate, $edate, $size, $budget, $preference, $email, $notes);
    
    if (!empty($validation_errors)) {
        // Display validation errors
        echo "<!DOCTYPE html><html><head><title>Validation Error</title></head><body>";
        echo "<h2>Validation Errors:</h2><ul>";
        foreach ($validation_errors as $error) {
            echo "<li style='color: red;'>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "<button onclick='history.back()'>Go Back</button>";
        echo "</body></html>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO trip_pack 
        (FullName, ContactNumber, Country, NIC, StartDate, EndDate, GroupSize, Budget, Preference, Email, Notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssissss", $name, $contact, $country, $nic, $sdate, $edate, $size, $budget, $preference, $email, $notes);

    if ($stmt->execute()) {
        header("Location: packages.html?status=success");
        exit();
    } else {
        header("Location: packages.html?status=fail");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
