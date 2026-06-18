<?php
session_start();
include '../Project in WST/server/server.php'; // Adjust path if necessary

// Check if the ID is set in the query string
if (isset($_GET['id'])) {
    $message_id = $_GET['id'];

    // Update the message status to 'read'
    $update_stmt = $conn->prepare("UPDATE contact_us SET status = 'read' WHERE id = ?");
    $update_stmt->bind_param("i", $message_id);
    $update_stmt->execute();

    // Prepare and execute a query to fetch the message details
    $stmt = $conn->prepare("SELECT id, name, email, subject, message FROM contact_us WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the message exists
    if ($result->num_rows > 0) {
        $message = $result->fetch_assoc();
    } else {
        echo "<p>Message not found.</p>";
        exit;
    }
} else {
    echo "<p>No message selected.</p>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Details</title>
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; padding: 20px; }
        .message-details { width: 50%; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #1c475c; }
        p { font-size: 16px; line-height: 1.6; }
        .label { font-weight: bold; color: #1c475c; }
        .back-button { display: block; margin-top: 20px; text-align: center; text-decoration: none; color: white; background-color: #1c475c; padding: 10px 20px; border-radius: 4px; }
        .back-button:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="message-details">
        <h2>Message Details</h2>
        <p><span class="label">ID:</span> <?php echo htmlspecialchars($message['id']); ?></p>
        <p><span class="label">Name:</span> <?php echo htmlspecialchars($message['name']); ?></p>
        <p><span class="label">Email:</span> <?php echo htmlspecialchars($message['email']); ?></p>
        <p><span class="label">Subject:</span> <?php echo htmlspecialchars($message['subject']); ?></p>
        <p><span class="label">Message:</span><br><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
        <a href="admin_notifs.php" class="back-button">Back to Notifications</a>
    </div>
</body>
</html>
