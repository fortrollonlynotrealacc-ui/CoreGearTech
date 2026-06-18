<?php
include '../Project in WST/server/server.php';

// Ensure a valid user_id is passed
if (!isset($_GET['user_id'])) {
    die("No user specified.");
}
$user_id = intval($_GET['user_id']);

// Fetch messages for the user
$sql = "SELECT message, message_type, created_at FROM chat_messages WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($message, $message_type, $created_at);

$messages = [];
while ($stmt->fetch()) {
    $messages[] = [
        'message' => $message,
        'message_type' => $message_type,
        'created_at' => $created_at
    ];
}
$stmt->close();

// Return the messages as JSON
echo json_encode($messages);

?>