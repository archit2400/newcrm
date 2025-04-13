

<?php
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$response = [];

if ($user && password_verify($password, $user['password'])) {
  $response['status'] = 'success';
  $response['user'] = [
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role']
  ];
} else {
  $response['status'] = 'error';
  $response['message'] = 'Invalid email or password';
}

echo json_encode($response);
?>
