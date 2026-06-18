<?php
session_start();

$index = isset($_GET['index']) ? (int)$_GET['index'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($index !== null && isset($_SESSION['cartItems'][$index])) {
    if ($action === 'increase') {
        $_SESSION['cartItems'][$index]['quantity']++;
    } elseif ($action === 'decrease' && $_SESSION['cartItems'][$index]['quantity'] > 1) {
        $_SESSION['cartItems'][$index]['quantity']--;
    } elseif ($action === 'remove') {
        array_splice($_SESSION['cartItems'], $index, 1);
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
