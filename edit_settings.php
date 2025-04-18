<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to edit settings.";
    exit;
}

$userId = $_SESSION['user_id'];
$message = "";

// Fetch current user info
$sql = "SELECT name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update info if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = $_POST['name'];
    $newEmail = $_POST['email'];
    $newRole = $_POST['role'];

    $updateSql = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssi", $newName, $newEmail, $newRole, $userId);

    if ($updateStmt->execute()) {
        $message = "Settings updated successfully!";
        // Refresh the user data
        $user['name'] = $newName;
        $user['email'] = $newEmail;
        $user['role'] = $newRole;
    } else {
        $message = "Error updating settings: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Settings</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 20px;
        }
        .form-container {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        input, select {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
        }
        .message {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Settings</h2>

        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" action="edit_settings.php">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Role:</label>
            <select name="role">
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit">Update Settings</button>
        </form>
    </div>
</body>
</html>
