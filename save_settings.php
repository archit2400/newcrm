<?php
$host = "localhost";
$user = "root";
$password = ""; // your MySQL password
$dbname = "newcrm";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get posted data safely
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$company_name = $_POST['company_name'] ?? '';
$industry = $_POST['industry'] ?? '';
$address1 = $_POST['address1'] ?? '';
$address2 = $_POST['address2'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$zip = $_POST['zip'] ?? '';
$company_address = $_POST['company_address'] ?? '';
$suite = $_POST['suite'] ?? '';

// Prepare and execute query
$sql = "UPDATE usersettings SET 
    first_name='$first_name', 
    last_name='$last_name', 
    phone='$phone', 
    company_name='$company_name',
    industry='$industry',
    address1='$address1',
    address2='$address2',
    city='$city',
    state='$state',
    zip='$zip',
    company_address='$company_address',
    suite='$suite'
    WHERE email='$email'";

if ($conn->query($sql) === TRUE) {
    // Optional: Start session to store success message
    session_start();
    $_SESSION['success'] = "Settings updated successfully";

    // Redirect to the settings page
    header("Location: customerset.php"); // Change to your actual page
    exit();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
