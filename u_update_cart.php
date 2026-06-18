<?php
session_start();
include '../Project in WST/server/server.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to update cart items.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get data from the request
$data = json_decode(file_get_contents('php://input'), true);
$product_id = isset($data['id']) ? intval($data['id']) : 0;
$action = isset($data['action']) ? $data['action'] : '';

if (!$product_id || !$action) {
    echo json_encode(['success' => false, 'message' => 'Invalid request parameters.']);
    exit;
}

// Fetch the current quantity of the item
$query = "SELECT quantity FROM user_cart_items WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found in cart.']);
    exit;
}

$row = $result->fetch_assoc();
$current_quantity = $row['quantity'];

// Update quantity based on action
if ($action === 'increase') {
    $new_quantity = $current_quantity + 1;
} elseif ($action === 'decrease') {
    $new_quantity = max(1, $current_quantity - 1); // Ensure quantity does not go below 1
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    exit;
}

// Update the quantity in the database
$update_query = "UPDATE user_cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("iii", $new_quantity, $user_id, $product_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Quantity updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity.']);
}

$stmt->close();
$conn->close();
?>
