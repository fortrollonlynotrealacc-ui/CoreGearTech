<?php
session_start();
include '../Project in WST/server/server.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$lname = $_POST['lname'] ?? '';
$fname = $_POST['fname'] ?? '';
$mname = $_POST['mname'] ?? '';
$age = $_POST['age'] ?? null;
$address = $_POST['address'] ?? '';
$phonenumber = $_POST['phonenumber'] ?? '';

// Handle avatar upload if a file is provided
$avatar_path = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/avatars/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $avatar_name = uniqid() . '_' . basename($_FILES['avatar']['name']);
    $avatar_path = $upload_dir . $avatar_name;

    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
        echo json_encode(['success' => false, 'error' => 'Failed to upload avatar']);
        exit();
    }
}

// Update user information, including avatar if it was uploaded
$sql = $avatar_path 
    ? "UPDATE cgt_accounts SET lname = ?, fname = ?, mname = ?, age = ?, address = ?, phonenumber = ?, avatar = ? WHERE id = ?"
    : "UPDATE cgt_accounts SET lname = ?, fname = ?, mname = ?, age = ?, address = ?, phonenumber = ? WHERE id = ?";

$stmt = $conn->prepare($sql);

if ($avatar_path) {
    // Bind 8 parameters when avatar is included
    $stmt->bind_param("sssisisi", $lname, $fname, $mname, $age, $address, $phonenumber, $avatar_path, $user_id);
} else {
    // Bind 7 parameters when avatar is not included
    $stmt->bind_param("sssisii", $lname, $fname, $mname, $age, $address, $phonenumber, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
?>
