<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cgt database';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get user_id from the request
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id > 0) {
    // Fetch messages between the admin and the specified user
    $stmt = $conn->prepare("
        SELECT message_type, message, created_at 
        FROM chat_messages 
        WHERE user_id = ? 
        ORDER BY created_at ASC
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
} else {
    // If no valid user_id is provided, return an empty array
    echo json_encode([]);
}

$conn->close();
?>
