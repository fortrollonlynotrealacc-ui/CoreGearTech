<?php
session_start();
include '../Project in WST/server/server.php'; 

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id) {
    $query = "SELECT product_id AS id, product_name AS name, product_image AS image, product_price AS price, quantity FROM user_cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cartItems = [];
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
    echo json_encode(['cartItems' => $cartItems]);
} else {
    echo json_encode(['cartItems' => []]);
}
?>
