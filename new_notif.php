<?php
header('Content-Type: application/json');
include '../Project in WST/server/server.php';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get count of unread messages
    $stmt = $pdo->prepare("SELECT COUNT(*) as unreadMessagesCount FROM contact_us WHERE status = 'unread'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Return the count of unread messages as JSON
    echo json_encode(["unreadMessagesCount" => $result['unreadMessagesCount']]);
} catch (PDOException $e) {
    // Return error message as JSON
    echo json_encode(["error" => $e->getMessage()]);
}
?>
