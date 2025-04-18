<?php
include 'db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare the data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $industry = mysqli_real_escape_string($conn, $_POST['industry']);
    $address1 = mysqli_real_escape_string($conn, $_POST['address1']);
    $address2 = mysqli_real_escape_string($conn, $_POST['address2']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $company_address = mysqli_real_escape_string($conn, $_POST['company_address']);
    $suite = mysqli_real_escape_string($conn, $_POST['suite']);

    // Check if user settings already exist
    $check_query = "SELECT * FROM usersettings LIMIT 1";
    $result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($result) > 0) {
        // Update existing record
        $query = "UPDATE usersettings SET 
                  first_name = '$first_name',
                  last_name = '$last_name',
                  email = '$email',
                  phone = '$phone',
                  company_name = '$company_name',
                  industry = '$industry',
                  address1 = '$address1',
                  address2 = '$address2',
                  city = '$city',
                  state = '$state',
                  zip = '$zip',
                  company_address = '$company_address',
                  suite = '$suite'";
    } else {
        // Insert new record
        $query = "INSERT INTO usersettings (first_name, last_name, email, phone, company_name, industry, address1, address2, city, state, zip, company_address, suite)
                  VALUES ('$first_name', '$last_name', '$email', '$phone', '$company_name', '$industry', '$address1', '$address2', '$city', '$state', '$zip', '$company_address', '$suite')";
    }

    // Execute the query
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Settings updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating settings: ' . mysqli_error($conn)]);
    }
    
    mysqli_close($conn);
    exit;
}
?>