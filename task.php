<?php
session_start();
// Database configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'newcrm';

// Create connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Handle form submission for CREATE and UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = $_POST['id'] ?? null;
    $title          = $_POST['title'] ?? '';
    $assignee       = $_POST['assignee'] ?? '';
    $priority       = $_POST['priority'] ?? '';
    $deadline_date  = $_POST['deadline_date'] ?? '';
    $deadline_time  = $_POST['deadline_time'] ?? '';
    $is_recurring   = isset($_POST['recurring']) ? 1 : 0;
    $recurrence_type = $_POST['recurrence_type'] ?? '';
    $description    = $_POST['description'] ?? '';
    $status         = $_POST['status'] ?? 'Pending';
    $attachment     = $_POST['existing_attachment'] ?? '';

    // File upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename    = basename($_FILES['attachment']['name']);
        $targetPath  = $uploadDir . $filename;
        move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath);
        $attachment = 'uploads/' . $filename;
        
        // Delete old file if updating
        if (!empty($_POST['existing_attachment']) && $id) {
            @unlink(__DIR__ . '/' . $_POST['existing_attachment']);
        }
    }

    if ($id) {
        // UPDATE existing task
        $stmt = $conn->prepare(
            'UPDATE tasks SET 
                title = ?, 
                assignee = ?, 
                priority = ?, 
                deadline_date = ?, 
                deadline_time = ?, 
                is_recurring = ?, 
                recurrence_type = ?, 
                description = ?, 
                attachment = ?,
                status = ?
             WHERE id = ?'
        );
        $stmt->bind_param(
            'ssssssssssi',
            $title, $assignee, $priority, $deadline_date,
            $deadline_time, $is_recurring, $recurrence_type,
            $description, $attachment, $status, $id
        );
    } else {
        // CREATE new task
        $stmt = $conn->prepare(
            'INSERT INTO tasks (
                title, assignee, priority, deadline_date, deadline_time, 
                is_recurring, recurrence_type, description, attachment, status
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'ssssssssss',
            $title, $assignee, $priority, $deadline_date,
            $deadline_time, $is_recurring, $recurrence_type,
            $description, $attachment, $status
        );
    }
    
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = $id ? 'Task updated successfully!' : 'Task assigned successfully!';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle DELETE request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    // First get the attachment path if exists
    $stmt = $conn->prepare('SELECT attachment FROM tasks WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    
    if ($task && !empty($task['attachment'])) {
        @unlink(__DIR__ . '/' . $task['attachment']);
    }
    
    // Then delete the task
    $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['message'] = 'Task deleted successfully!';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle GET request for editing (fetch single task)
$editTask = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $conn->prepare('SELECT * FROM tasks WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $editTask = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all tasks
$result = $conn->query('SELECT * FROM tasks ORDER BY created_at DESC');
$tasks  = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    $result->free();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Admin - Task Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans flex">
    <!-- Sidebar remains the same -->
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30">
    <div class="h-full flex flex-col">
      <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200">
        <div class="flex items-center space-x-2">
          <div class="h-8 w-8  rounded-md flex items-center justify-center">
            <span class="text-white font-bold"></span>
          </div>
          <span class="text-xl font-bold text-gray-800">CRMSystem<span>
        </div>
        <button id="closeSidebar" class="md:hidden text-gray-500 hover:text-gray-600">
          ✕
        </button>
      </div>

      <nav class="flex-1 overflow-auto py-4 px-3 space-y-1">
        <a href="./admin.html" class="flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-md translatable">Dashboard</a>
        <a href="./contact.php" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md translatable">Customer Management</a>
        <a href="./admininbox.html" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md translatable">Inbox</a>
        <a href="./task.php" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md translatable">Task Management</a>
        <a href="./settings.php" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md translatable">Settings</a>
      </nav>

      <div class="border-t border-gray-200 p-4">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-full flex items-center justify-center mr-3">
            <span class="font-medium text-blue-700"></span>
          </div>
         
        </div>
        <div class="mt-4 flex items-center justify-between">
            <a href="logout.php" class="text-red-500 font-semibold">

          <button class="text-red-500 font-semibold translatable">Logout</button>
        </a>
        </div>
      </div>
    </div>
  </div>

    <!-- Main Content -->
    <div class="flex-1 p-8 md:ml-64">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Task Management</h1>

        <!-- Flash Message -->
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Assign Tasks to Team Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">
                <?= $editTask ? 'Edit Task' : 'Assign Tasks to Team' ?>
            </h2>
            
            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" class="space-y-4 mb-8">
                <?php if ($editTask): ?>
                    <input type="hidden" name="id" value="<?= $editTask['id'] ?>">
                    <input type="hidden" name="existing_attachment" value="<?= $editTask['attachment'] ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
                        <input type="text" name="title" required 
                               value="<?= $editTask['title'] ?? '' ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assignee</label>
                        <select name="assignee" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select user/team</option>
                            <option value="John (Sales)" <?= ($editTask['assignee'] ?? '') === 'John (Sales)' ? 'selected' : '' ?>>John (Sales)</option>
                            <option value="Sarah (Marketing)" <?= ($editTask['assignee'] ?? '') === 'Sarah (Marketing)' ? 'selected' : '' ?>>Sarah (Marketing)</option>
                            <option value="IT Team" <?= ($editTask['assignee'] ?? '') === 'IT Team' ? 'selected' : '' ?>>IT Team</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="Low" <?= ($editTask['priority'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
                            <option value="Medium" <?= ($editTask['priority'] ?? 'Medium') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="High" <?= ($editTask['priority'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="Pending" <?= ($editTask['status'] ?? 'Pending') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="In Progress" <?= ($editTask['status'] ?? '') === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= ($editTask['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deadline Date</label>
                        <input type="date" name="deadline_date" required 
                               value="<?= $editTask['deadline_date'] ?? '' ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deadline Time</label>
                        <input type="time" name="deadline_time" required 
                               value="<?= $editTask['deadline_time'] ?? '' ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="recurring" id="recurring" 
                               <?= ($editTask['is_recurring'] ?? 0) ? 'checked' : '' ?> 
                               class="mr-2 h-4 w-4">
                        <label for="recurring" class="text-sm text-gray-700">Recurring?</label>
                    </div>
                    <select name="recurrence_type" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Daily" <?= ($editTask['recurrence_type'] ?? '') === 'Daily' ? 'selected' : '' ?>>Daily</option>
                        <option value="Weekly" <?= ($editTask['recurrence_type'] ?? '') === 'Weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="Monthly" <?= ($editTask['recurrence_type'] ?? '') === 'Monthly' ? 'selected' : '' ?>>Monthly</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= $editTask['description'] ?? '' ?></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attach Files</label>
                    <?php if ($editTask && !empty($editTask['attachment'])): ?>
                        <div class="mb-2">
                            <span class="text-sm text-gray-700">Current file: </span>
                            <a href="<?= $editTask['attachment'] ?>" target="_blank" class="text-indigo-600 hover:underline">
                                <?= basename($editTask['attachment']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="attachment" id="file-upload" class="hidden">
                    <label for="file-upload" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-200">
                        <i class="fas fa-paperclip mr-2"></i> Choose File
                    </label>
                    <span id="fileName" class="ml-2 text-sm text-gray-500">No file chosen</span>
                </div>
                
                <div class="flex justify-between">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                        <?= $editTask ? 'Update Task' : 'Assign Task' ?>
                    </button>
                    
                    <?php if ($editTask): ?>
                        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                            Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <h3 class="text-lg font-medium text-gray-700 mb-4">Assigned Tasks Overview</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($task['title']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($task['assignee']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?= $task['priority'] === 'High' ? 'bg-red-100 text-red-800' : '' ?>
                                        <?= $task['priority'] === 'Medium' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                                        <?= $task['priority'] === 'Low' ? 'bg-green-100 text-green-800' : '' ?>">
                                        <?= htmlspecialchars($task['priority']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($task['deadline_date']) ?> 
                                    <?= !empty($task['deadline_time']) ? 'at ' . htmlspecialchars($task['deadline_time']) : '' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?= $task['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                                        <?= $task['status'] === 'In Progress' ? 'bg-blue-100 text-blue-800' : '' ?>
                                        <?= $task['status'] === 'Completed' ? 'bg-green-100 text-green-800' : '' ?>">
                                        <?= htmlspecialchars($task['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="?action=edit&id=<?= $task['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                    <a href="?action=delete&id=<?= $task['id'] ?>" 
                                       onclick="return confirm('Are you sure you want to delete this task?')"
                                       class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // File upload name display
        document.getElementById('file-upload').addEventListener('change', function (e) {
            document.getElementById('fileName').textContent = 
                e.target.files.length > 0 ? e.target.files[0].name : 'No file chosen';
        });

        // Toggle recurrence select based on checkbox
        document.getElementById('recurring').addEventListener('change', function() {
            document.querySelector('select[name="recurrence_type"]').disabled = !this.checked;
        });
        // Initialize on page load
        document.querySelector('select[name="recurrence_type"]').disabled = 
            !document.getElementById('recurring').checked;
    </script>
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