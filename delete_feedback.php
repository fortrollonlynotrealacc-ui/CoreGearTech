<?php
include '../Project in WST/server/server.php';

// Check if the request is a POST request and contains a feedback ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);

    // Delete the feedback from the database
    $sql = "DELETE FROM feedback WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $feedback_id);

    if ($stmt->execute()) {
        echo 'Feedback deleted successfully.';
    } else {
        echo 'Error deleting feedback: ' . $conn->error;
    }

    $stmt->close();
} else {
    echo 'Invalid request.';
}
?>
