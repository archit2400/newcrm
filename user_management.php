<?php
include 'db_connect.php'; // Make sure this file connects to your database
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30">
  <div class="h-full flex flex-col">
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200">
      <div class="flex items-center space-x-2">
        <div class="h-8 w-8 rounded-md flex items-center justify-center">
          <span class="text-white font-bold"></span>
        </div>
        <span class="text-xl font-bold text-gray-800">CRMSystem</span>
      </div>
      <button id="closeSidebar" class="md:hidden text-gray-500 hover:text-gray-600">
        ✕
      </button>
    </div>
    <nav class="flex-1 overflow-auto py-4 px-3 space-y-1">
      <a href="./admin.html" class="flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-md translatable">Dashboard</a>
      <a href="./user_management.php" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md translatable">User Management</a>
      <a href="./admininbox.html" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md translatable">Inbox</a>
      <a href="reports_analytics.html" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md translatable">Reports & Analytics</a>
      <a href="./settings.html" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md translatable">Settings</a>
    </nav>
    <div class="border-t border-gray-200 p-4">
      <div class="flex items-center">
        <div class="h-10 w-10 rounded-full lue-100 flex items-center justify-center mr-3">
          <span class="font-medium text-blue-700"></span>
        </div>
      </div>
      <div class="mt-4 flex items-center justify-between">
        <a href="logout.php" class="text-red-500 font-semibold translatable">Logout</a>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="md:ml-64 p-4">
  <h1 class="text-3xl font-bold mb-6 text-center text-blue-700 translatable">User Management</h1>
  <select id="languageSelect" class="px-2 py-1 border rounded text-sm">
              <option value="en">English</option>
              <option value="hi">Hindi</option>
              <option value="es">Spanish</option>
              <option value="fr">French</option>
              <option value="de">German</option>
              <option value="te">Telugu</option>
              <option value="ta">Tamil</option>
              <option value="ml">Malayalam</option>
              <option value="ja">Japanese</option>
              <option value="it">Italian</option>
            </select>
  <!-- Add User Form -->
  <form action="adduser.php" method="POST" class="bg-white p-6 rounded-lg shadow mb-8">
    <h2 class="text-xl font-semibold mb-4 text-gray-700">Add New User</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <input type="text" name="name" placeholder="Name" required class="border p-3 rounded w-full" />
      <input type="email" name="email" placeholder="Email" required class="border p-3 rounded w-full" />
      <input type="text" name="role" placeholder="Role (e.g., Admin)" required class="border p-3 rounded w-full" />
    </div>
    <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded">
      Add User
    </button>
  </form>

  <!-- Users Table -->
  <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
    <h2 class="text-xl font-semibold mb-4 text-gray-700 translatable">All Users</h2>
    <table class="w-full table-auto">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="p-3 translatable">ID</th>
          <th class="p-3 translatable">Name</th>
          <th class="p-3 translatable">Email</th>
          <th class="p-3 translatable">Role</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM newuser ORDER BY id DESC");
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $name = $row['name'];
            $email = $row['email'];
            $role = isset($row['role']) ? $row['role'] : 'N/A';

            echo "<tr class='border-t hover:bg-gray-50'>
                    <td class='p-3'>{$id}</td>
                    <td class='p-3'>{$name}</td>
                    <td class='p-3'>{$email}</td>
                    <td class='p-3'>{$role}</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='4' class='p-4 text-center text-gray-500'>No users found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- JavaScript for sidebar toggle -->
<script>
  const sidebar = document.getElementById("sidebar");
  const closeSidebar = document.getElementById("closeSidebar");

  closeSidebar?.addEventListener("click", () => {
    sidebar.classList.add("-translate-x-full");
  });

  // Optional: Add open sidebar logic if you have a hamburger button

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
