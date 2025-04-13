<?php
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$role = $data['role'];

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $role);

$response = [];

if ($stmt->execute()) {
  $response['status'] = 'success';
} else {
  $response['status'] = 'error';
  $response['message'] = 'Email already exists or error in registration';
}

echo json_encode($response);
?>
