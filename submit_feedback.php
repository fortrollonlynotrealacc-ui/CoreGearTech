<?php
session_start();
include '../Project in WST/server/server.php'; 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message' => 'You must log in first to submit feedback.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['user_id']; // From session
    $rating = intval($_POST['rating']);
    $feedback_message = trim($_POST['feedback_message']);
    $created_at = date('Y-m-d H:i:s');

    // Validate input
    if ($rating < 1 || $rating > 5 || empty($feedback_message)) {
        echo json_encode(['message' => 'Invalid input. Please provide valid feedback.']);
        exit;
    }

    // Insert feedback
    $stmt = $conn->prepare("INSERT INTO feedback (product_id, user_id, rating, feedback_message, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiss', $product_id, $user_id, $rating, $feedback_message, $created_at);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Feedback submitted successfully.']);
        header('Location: user-product copy.php?product_id=' . $product_id); 
    } else {
        echo json_encode(['message' => 'Failed to submit feedback: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>

