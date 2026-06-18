<?php
session_start();
include '../Project in WST/server/server.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Query to fetch messages intended for the specific user
$sql = "SELECT * 
        FROM chat_messages 
        WHERE user_id = ? OR (message_type = 'admin' AND user_id = ?) 
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $user_id); // Bind user_id twice for admin filtering
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
