<?php
include 'db_connect.php';

$result = mysqli_query($conn, "SELECT * FROM usersettings");

while($row = mysqli_fetch_assoc($result)) {
    echo $row['first_name'] . "<br>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRM Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="flex h-screen">
  
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
            <div class="flex-1 overflow-auto py-4 px-3">
                <nav class="space-y-1">
                  <a href="./customer.html" class="flex items-center px-3 py-2 pl-0 bg-blue-50 text-blue-700 rounded-md">
                    <i  class="h-5 w-5 mr-3"></i>
                    Dashboard
                  </a>
                  <a href="./Orders.html" class="flex items-center px-3 py-2 pl-0 text-gray-700 hover:bg-gray-100 rounded-md">
                    <i  class="h-5 w-5 mr-3"></i>
                    Orders
                  </a>
                  <a href="./invoice.html" class="flex items-center px-3 py-2 pl-0 text-gray-700 hover:bg-gray-100 rounded-md">
                    <i  class="h-5 w-5 mr-3"></i>
                    Invoices
                  </a>
                  <a href="./support.html" class="flex items-center px-3 py-2 pl-0 text-gray-700 hover:bg-gray-100 rounded-md">
                    <i  class="h-5 w-5 mr-3"></i>
                    Support
                  </a>
                 
                  <a href="./customerset.php" class="flex items-center px-3 pl-0 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
                    <i  class="h-5 w-5 mr-3"></i>
                    Settings
                  </a>
                </nav>
              </div>
            <div class="border-t border-gray-200 p-4">
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full  flex items-center justify-center mr-3">
                  <span class=""></span>
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
          <div class="flex-1 p-8 overflow-auto">
            <div class="max-w-6xl mx-auto">
              <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 translatable">Settings</h1>
              </div>
    
              <!-- Settings Tabs -->
              <div class="flex border-b mb-6">
                <button class="px-4 py-2 font-medium border-b-2 border-blue-600 text-blue-600 translatable">Account</button>
              </div>
    
             <!-- Replace your current form with this one -->
<form id="settingsForm" method="POST" action="save_settings.php" class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4 translatable">Account Information</h2>
    
    <?php
    include 'db_connect.php';
    $result = mysqli_query($conn, "SELECT * FROM usersettings LIMIT 1");
    $settings = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    ?>
    
    <input type="text" name="first_name" value="<?php echo isset($settings['first_name']) ? htmlspecialchars($settings['first_name']) : ''; ?>" placeholder="First Name" class="w-full border rounded px-3 py-2 mb-2">
    <input type="text" name="last_name" value="<?php echo isset($settings['last_name']) ? htmlspecialchars($settings['last_name']) : ''; ?>" placeholder="Last Name" class="w-full border rounded px-3 py-2 mb-2">
    <input type="email" name="email" value="<?php echo isset($settings['email']) ? htmlspecialchars($settings['email']) : ''; ?>" placeholder="Email" class="w-full border rounded px-3 py-2 mb-2">
    <input type="tel" name="phone" value="<?php echo isset($settings['phone']) ? htmlspecialchars($settings['phone']) : ''; ?>" placeholder="Phone Number" class="w-full border rounded px-3 py-2 mb-2">
    
    <input type="text" name="company_name" value="<?php echo isset($settings['company_name']) ? htmlspecialchars($settings['company_name']) : ''; ?>" placeholder="Company Name" class="w-full border rounded px-3 py-2 mb-2">
    
    <select name="industry" class="w-full border rounded px-3 py-2 mb-2">
        <option value="" disabled>Select Industry</option>
        <option value="Technology" <?php echo (isset($settings['industry']) && $settings['industry'] == 'Technology' ? 'selected' : ''); ?>>Technology</option>
        <option value="Finance" <?php echo (isset($settings['industry']) && $settings['industry'] == 'Finance' ? 'selected' : ''); ?>>Finance</option>
        <option value="Healthcare" <?php echo (isset($settings['industry']) && $settings['industry'] == 'Healthcare' ? 'selected' : ''); ?>>Healthcare</option>
        <option value="Education" <?php echo (isset($settings['industry']) && $settings['industry'] == 'Education' ? 'selected' : ''); ?>>Education</option>
        <option value="Other" <?php echo (isset($settings['industry']) && $settings['industry'] == 'Other' ? 'selected' : ''); ?>>Other</option>
    </select>
    
    <input type="text" name="address1" value="<?php echo isset($settings['address1']) ? htmlspecialchars($settings['address1']) : ''; ?>" placeholder="Address Line 1" class="w-full border rounded px-3 py-2 mb-2">
    <input type="text" name="address2" value="<?php echo isset($settings['address2']) ? htmlspecialchars($settings['address2']) : ''; ?>" placeholder="Address Line 2" class="w-full border rounded px-3 py-2 mb-2">
    
    <input type="text" name="city" value="<?php echo isset($settings['city']) ? htmlspecialchars($settings['city']) : ''; ?>" placeholder="City" class="w-full border rounded px-3 py-2 mb-2">
    <input type="text" name="state" value="<?php echo isset($settings['state']) ? htmlspecialchars($settings['state']) : ''; ?>" placeholder="State" class="w-full border rounded px-3 py-2 mb-2">
    <input type="text" name="zip" value="<?php echo isset($settings['zip']) ? htmlspecialchars($settings['zip']) : ''; ?>" placeholder="ZIP Code" class="w-full border rounded px-3 py-2 mb-2">
    
    <input type="text" name="company_address" value="<?php echo isset($settings['company_address']) ? htmlspecialchars($settings['company_address']) : ''; ?>" placeholder="Company Address" class="w-full border rounded px-3 py-2 mb-2">
    <input type="text" name="suite" value="<?php echo isset($settings['suite']) ? htmlspecialchars($settings['suite']) : ''; ?>" placeholder="Suite/Unit" class="w-full border rounded px-3 py-2 mb-4">
    
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Save Info</button>
</form>
            
            
      </div>
        </div>
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
        <!-- Danger Zone -->
      
 
    <script>
        const apiKey = 'AIzaSyAOan6AJhCDRKjC6S8rrhgI9qz16n9AyQo'; // your actual key
      
        async function translateText(text, targetLang) {
          try {
            const response = await fetch(`https://translation.googleapis.com/language/translate/v2?key=${apiKey}`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                q: text,
                target: targetLang,
                format: 'text'
              })
            });
      
            const data = await response.json();
            return data.data.translations[0].translatedText;
          } catch (err) {
            console.error("Translation error:", err);
            return null;
          }
        }
      
        async function translatePage(lang) {
          const elements = document.querySelectorAll('.translatable');
          for (let el of elements) {
            const originalText = el.getAttribute('data-original') || el.innerText;
            el.setAttribute('data-original', originalText);
            const translatedText = await translateText(originalText, lang);
            if (translatedText) el.innerText = translatedText;
          }
        }
      
        document.addEventListener('DOMContentLoaded', () => {
          const langSelect = document.getElementById('languageSelect');
          const savedLang = localStorage.getItem('selectedLanguage') || 'en';
          if (langSelect) langSelect.value = savedLang;
          translatePage(savedLang);
      
          if (langSelect) {
            langSelect.addEventListener('change', async function () {
              const selected = this.value;
              localStorage.setItem('selectedLanguage', selected);
              translatePage(selected);
            });
          }
        });
      document.getElementById("settingsForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("save_settings.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === 'success') {
            alert(response.message);
            // Optional: You can update the page content here if needed
        } else {
            alert(response.message);
        }
    })
    .catch(error => {
        alert("Error updating settings. Please try again.");
        console.error(error);
    });
});

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