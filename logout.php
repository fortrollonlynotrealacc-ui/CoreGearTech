<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Return a JSON response indicating the user has logged out successfully
echo json_encode(['message' => 'Logged out successfully']);
exit();
