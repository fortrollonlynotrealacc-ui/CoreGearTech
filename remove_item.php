<?php
session_start();
include '../Project in WST/server/server.php';

// Check if the request is an AJAX call and if product_id is provided
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['id'] ?? null; // Retrieve product_id from incoming data

// Check if user is logged in and product_id is set
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id && $product_id) {
    // Prepare the delete query
    $query = "DELETE FROM user_cart_items WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        // Debugging: check if statement preparation failed
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }

    // Bind parameters to the query
    $stmt->bind_param("ii", $user_id, $product_id);

    // Execute the statement and check if successful
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete item from database: ' . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    // Debugging: Check if user_id or product_id is missing
    error_log("Delete attempt failed: user_id = " . $user_id . ", product_id = " . $product_id);
    echo json_encode(['success' => false, 'message' => 'User not logged in or invalid product ID.']);
}
?>
