<?php
session_start();

// Change these to your actual DB credentials
$host = 'localhost';
$db   = 'newcrm';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming user ID is stored in session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("User not logged in.");
}

$sql = "SELECT name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Account Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 hidden md:block">
      <div class="p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-6">CRMSystem</h1>
        <nav class="space-y-2">
          <a href="./admin.html" class="block px-4 py-2 bg-blue-100 text-blue-700 rounded translatable">Dashboard</a>
          <a href="./contact.php" class="block px-4 py-2 hover:bg-gray-100 rounded translatable">Customer Management</a>
          <a href="./admininbox.html" class="block px-4 py-2 hover:bg-gray-100 rounded translatable">Inbox</a>
          <a href="./task.php" class="block px-4 py-2 hover:bg-gray-100 rounded translatable">Task Management</a>
          <a href="./settings.php" class="block px-4 py-2 hover:bg-gray-100 rounded translatable">Settings</a>
        </nav>
        <div class="mt-10">
          <a href="logout.php" class="text-red-500 font-semibold translatable">Logout</a>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 md:ml-64 p-6">
      <div class="bg-white rounded-lg shadow p-6 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 translatable">Account Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" value="<?= htmlspecialchars($user['name']) ?>" readonly class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <input type="text" value="<?= ucfirst(htmlspecialchars($user['role'])) ?>" readonly class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed">
          </div>
        </div>

        <div class="text-right">
          <a href="edit_settings.php" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit</a>
        </div>
      </div>
    </div>
  </div>
  <script>
        document.addEventListener("DOMContentLoaded", function () {
          const currentPage = window.location.pathname.split("/").pop(); // Get the current file name
          const navLinks = document.querySelectorAll("nav a");
      
          navLinks.forEach(link => {
            const hrefPage = link.getAttribute("href").split("/").pop(); // In case of subfolders
            // Clear previous highlight
            link.classList.remove("bg-blue-50", "text-blue-500", "font-semibold");
            // Set active class if matches current page
            if (hrefPage === currentPage) {
              link.classList.add("bg-blue-50", "text-blue-500", "font-semibold");
            }
          });
        });
      </script>
</body>
</html>
