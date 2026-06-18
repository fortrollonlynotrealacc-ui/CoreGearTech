<?php
include '../Project in WST/server/server.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_id = intval($_POST['feedback_id']);
    $admin_reply = trim($_POST['admin_reply']);

    $sql = "UPDATE feedback SET admin_reply = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $admin_reply, $feedback_id);

    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['HTTP_REFERER']); // Redirect back to the same page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
