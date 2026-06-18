<?php
session_start(); // Start the session to access user data

// Check if user data exists in the session
if (!isset($_SESSION['fname'])) {
    echo '<script> alert("Please login first!")</script>';
    header('refresh:.5;url=../login.php'); // Redirect to login if not logged in
    exit();
}

$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];
$age = $_SESSION['age'];
$address = $_SESSION['address'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo $fname . ' ' . $lname; ?>!</h1>
    <p>Age: <?php echo $age; ?></p>
    <p>Address: <?php echo $address; ?></p>
</body>
</html>
