<?php
session_start();
include '../Project in WST/server/server.php';

// Check if the request is an AJAX call and if user is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Check if product_id is set in the input
    if (!isset($data['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID not provided']);
        exit();
    }

    $product_id = $data['product_id'];

    // Prepare and execute the delete query
    $sql = "DELETE FROM user_cart_items WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
