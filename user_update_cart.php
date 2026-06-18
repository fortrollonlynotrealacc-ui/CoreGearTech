<?php
session_start();
require_once '../Project in WST/server/server.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON data sent by the JavaScript
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $cartItems = $data['cartItems'] ?? [];

    $success = true;

    foreach ($cartItems as $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];

        // Check if item already exists in the cart
        $checkSql = "SELECT * FROM user_cart WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the quantity if it exists
            $updateSql = "UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("iii", $quantity, $user_id, $product_id);
            $updateStmt->execute();

            if ($updateStmt->affected_rows === 0) {
                $success = false;
            }
        } else {
            // Insert new item if it doesn't exist
            $insertSql = "INSERT INTO user_cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iii", $user_id, $product_id, $quantity);
            $insertStmt->execute();

            if ($insertStmt->affected_rows === 0) {
                $success = false;
            }
        }
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
