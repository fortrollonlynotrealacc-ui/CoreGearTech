<?php
session_start();
include '../Project in WST/server/server.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $message = $conn->real_escape_string($_POST['message']);
    $message_type = 'admin';

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (user_id, message, message_type, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $message, $message_type);

        if ($stmt->execute()) {
            header("Location: admin_chat.php?user_id=$user_id");
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Message cannot be empty.";
    }
}
?>
