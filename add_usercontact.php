<?php
$servername = "localhost";
$username = "root"; // default for XAMPP
$password = "";     // default for XAMPP
$dbname = "newcrm"; // Change this

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name    = $_POST['name'];
$email   = $_POST['email'];
$company = $_POST['company'];
$phone   = $_POST['phone'];
$tag     = $_POST['tag'];

$sql = "INSERT INTO adduser (name, email, company, phone, tag)
        VALUES ('$name', '$email', '$company', '$phone', '$tag')";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "error: " . $conn->error;
}

$conn->close();
?>
