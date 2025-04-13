<?php
session_start();
session_destroy(); // This clears all session data
header("Location: index1.html"); // Change to your login page name
exit();
?>
