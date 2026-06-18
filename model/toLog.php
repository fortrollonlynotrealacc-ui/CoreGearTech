<?php
session_start();

// Database connection details
$database = "cgt database";
$username = "root";
$host = "localhost";
$password = "";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize form data
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Check if the user is admin
if ($email === 'admin@gmail.com' && $password === 'adminjustfer123') {
    $_SESSION['user_id'] = 'admin';
    $_SESSION['user_name'] = 'Admin';
    header("Location: /Project in WST/adminwebsite.php");
    exit();
}

// If not admin, continue with regular user authentication
$sql = "SELECT * FROM cgt_accounts WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Check if the user account is blocked
        if ($user['status'] === 'blocked') {
            $_SESSION['error_message'] = "Your account is blocked.";
            header("Location: /Project in WST/login.php");
            exit();
        }

        // If not blocked, allow login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['fname'];
        header("Location: /Project in WST/userwebsite.php");
        exit();
    } else {
        // Incorrect password
        $_SESSION['error_message'] = "Invalid email or password.";
        header("Location: /Project in WST/login.php");
        exit();
    }
} else {
    // User not found
    $_SESSION['error_message'] = "Invalid email or password.";
    header("Location: /Project in WST/login.php");
    exit();
}

$conn->close();
?>
