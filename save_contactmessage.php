<?php
include '../Project in WST/server/server.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Insert data into contact_us table with status set to 'unread'
    $sql = "INSERT INTO contact_us (name, email, subject, message, status) VALUES ('$name', '$email', '$subject', '$message', 'unread')";

    if ($conn->query($sql) === TRUE) {
        // Redirect with a success message
        echo "<script>
                alert('Message sent successfully!');
                window.location.href = 'website.php'; // Adjust this to your desired redirect location
              </script>";
    } else {
        // Redirect with an error message
        echo "<script>
                alert('Error: Could not send message. Please try again.');
                window.location.href = 'website.php'; // Adjust this to your desired redirect location
              </script>";
    }
}

// Close connection
$conn->close();
?>
