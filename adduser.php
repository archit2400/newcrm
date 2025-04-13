<?php
include 'db_connect.php'; // Make sure this file connects to your database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Check if the email already exists in the newuser table
    $check_email_query = "SELECT * FROM newuser WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_email_query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Email already exists
        echo "<script>alert('Email already exists. Please use a different email.'); window.history.back();</script>";
    } else {
        // Email doesn't exist, proceed to insert the user into newuser table
        $insert_query = "INSERT INTO newuser (name, email, role) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $role);

        if (mysqli_stmt_execute($stmt)) {
            // Successfully inserted, redirect to user management page with a cache-clear option
            header('Location: user_management.php?status=success');
            exit(); // Make sure to call exit after the redirect
        } else {
            // Error inserting user
            echo "<script>alert('Error adding user. Please try again.'); window.history.back();</script>";
        }
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
}

// Close the database connection
mysqli_close($conn);
?>
