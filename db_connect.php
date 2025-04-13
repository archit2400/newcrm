<?php
$host = 'localhost';
$user = 'root'; // Change if you have a different user
$pass = ''; // Add your MySQL password
$db = 'newcrm';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
