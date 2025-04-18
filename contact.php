<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Document</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    table, th, td {
      border: none;
      border-collapse: collapse;
    }
    td {
      padding-top: 8px;
      padding-bottom: 20px;
      padding-left: 0px;
      padding-right: 40px;
    }
    button {
      border: 2px solid;
    }
  </style>
</head>
<body class="bg-white text-black">

<header>
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <!-- Left Section -->
        <div class="flex items-center space-x-9 pl-10">
          <div class="text-2xl font-bold text-blue-600">
           
          </div>
          <nav class="space-x-4">
            <a class="text-black hover:text-pink-300 translatable" href="./index1.html">Home</a>
            <a class="text-black hover:text-pink-300 translatable" href="#">Features</a>
            <a class="text-black hover:text-pink-300 translatable" href="./about.html">About Us</a>
          </nav>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-4 pr-6">
          <!-- Language Dropdown -->
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

          <!-- Login Button -->
        
         
        </div>
      </div>
    </header>
  <div class="box">
    <div class="flex justify-between items-center p-4 pb-0">
      <h1 class="font-bold text-3xl translatable">Contacts</h1>
      <!-- Added id to the button -->
      <button id="openModal" class="bg-blue-400 px-2 py-2 font-bold rounded-lg translatable">Add New Customer</button>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4 text-black translatable">Add New User</h2>
        <form id="addUserForm">
          <input type="text" name="name" placeholder="Name" required class="w-full mb-2 border p-2 rounded" />
          <input type="email" name="email" placeholder="Email" required class="w-full mb-2 border p-2 rounded" />
          <input type="text" name="company" placeholder="Company" required class="w-full mb-2 border p-2 rounded" />
          <input type="text" name="phone" placeholder="Phone No." required class="w-full mb-2 border p-2 rounded" />
          <input type="text" name="tag" placeholder="Tag" required class="w-full mb-4 border p-2 rounded" />
          <div class="flex justify-between">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded translatable">Add</button>
            <button type="button" id="closeModal" class="bg-red-500 text-white px-4 py-2 rounded translatable">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <p class="p-4 pt-0 translatable">A list of all the Customer in your account including their name, title, email and role.</p>
    <table style="width: 100%;">
      <tr>
        <th class="text-left font-bold text-2xl pl-4 translatable">Name</th>
        <th class="text-left font-bold text-2xl translatable">Email</th>
        <th class="text-left font-bold text-2xl translatable">Company</th>
        <th class="text-left font-bold text-2xl translatable">Phone No.</th>
        <th class="text-left font-bold text-2xl translatable">Tags</th>
      </tr>
      <tr>
        <td colspan="5"><hr></td>
      </tr>

      <!-- Your static rows here -->
      <?php
  // Connect to DB
  include 'db_connect.php'; // This is your file that has mysqli_connect

  // Fetch users from DB
  $query = "SELECT * FROM adduser ORDER BY id DESC";
  $result = mysqli_query($conn, $query);

  while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td class='pl-4'>{$row['name']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>{$row['company']}</td>";
    echo "<td>{$row['phone']}</td>";
    echo "<td>{$row['tag']}</td>";

    echo "</tr>";
  }
?>

      <!-- Add the rest similarly -->
    </table>
  </div>

  <!-- JavaScript to toggle modal -->
  <script>
    const openModal = document.getElementById("openModal");
    const closeModal = document.getElementById("closeModal");
    const modal = document.getElementById("addUserModal");
    const form = document.getElementById("addUserForm");
  
    openModal.addEventListener("click", () => {
      modal.classList.remove("hidden");
    });
  
    closeModal.addEventListener("click", () => {
      modal.classList.add("hidden");
    });
  
    form.addEventListener("submit", function (e) {
      e.preventDefault();
  
      const formData = new FormData(form);
  
      fetch("add_usercontact.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        if (data.trim() === "success") {
          alert("User added successfully!");
          modal.classList.add("hidden");
          form.reset();
          location.reload(); // Optional: refresh to show new data
        } else {
          alert("Something went wrong: " + data);
        }
      });
    });
  </script>
  
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
</script> 
  
</body>
</html>
