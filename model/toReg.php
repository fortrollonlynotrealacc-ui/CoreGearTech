<?php
session_start();
include '../server/server.php';

// Retrieve form data from POST request
$fname = isset($_POST['fname']) ? $_POST['fname'] : '';
$mname = isset($_POST['mname']) ? $_POST['mname'] : '';
$lname = isset($_POST['lname']) ? $_POST['lname'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$age = isset($_POST['age']) ? $_POST['age'] : '';
$phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber'] : '';
$address = isset($_POST['address']) ? $_POST['address'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Validate required fields
if ($fname && $lname && $email && $age && $phonenumber && $address && $password && $confirm_password) {
    // Check if password and confirm password match
    if ($password === $confirm_password) {
        // Check if the email already exists
        $email_check_sql = "SELECT * FROM cgt_accounts WHERE email = '$email'";
        $email_check_result = $conn->query($email_check_sql);
        
        if ($email_check_result->num_rows > 0) {
            // Email already exists
            $_SESSION['error_message'] = "This email is already registered. Please use a different email address.";
            header("Location: /Project in WST/register.php");
            exit();
        }

        // Check if the phone number already exists
        $phone_check_sql = "SELECT * FROM cgt_accounts WHERE phonenumber = '$phonenumber'";
        $phone_check_result = $conn->query($phone_check_sql);

        if ($phone_check_result->num_rows > 0) {
            // Phone number already exists
            $_SESSION['error_message'] = "This phone number is already registered. Please use a different phone number.";
            header("Location: /Project in WST/register.php");
            exit();
        }

        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the data into the registration table
        $sql = "INSERT INTO cgt_accounts (fname, mname, lname, email, age, phonenumber, address, password)
                VALUES ('$fname', '$mname', '$lname', '$email', '$age', '$phonenumber', '$address', '$hashed_password')";

        // Execute the query
        if ($conn->query($sql) === TRUE) {
            $_SESSION['register_message'] = "Rigestration Successful!";
            header("Location: /Project in WST/register.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "Passwords do not match. Please try again.";
            header("Location: /Project in WST/register.php");
            exit();
    }
} else {
    $_SESSION['error_message'] = "Please fill in all required fields.";
            header("Location: /Project in WST/register.php");
            exit();
}

$conn->close();
?>
