<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);
$_SESSION['cartItems'] = $data;
echo json_encode(['status' => 'success']);
?>