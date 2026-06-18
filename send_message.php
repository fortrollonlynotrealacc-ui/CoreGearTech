<?php
session_start();
include '../Project in WST/server/server.php';

// Get user_id from request
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : (isset($_GET['user_id']) ? intval($_GET['user_id']) : 0);

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid or missing user ID']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $message_type = trim($_POST['message_type']);

    // Validate message
    if (empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Message cannot be empty']);
        exit;
    }

    // Validate user ID in the database
    $result = $conn->query("SELECT id FROM cgt_accounts WHERE id = $user_id");
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'User ID does not exist in cgt_accounts']);
        exit;
    }

    // Validate message type
    $valid_message_types = ['user', 'admin'];
    if (!in_array($message_type, $valid_message_types)) {
        echo json_encode(['success' => false, 'error' => 'Invalid message type']);
        exit;
    }

    // Sanitize message
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Insert message into the database
    $sql = "INSERT INTO chat_messages (user_id, message_type, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $user_id, $message_type, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
}
?>
