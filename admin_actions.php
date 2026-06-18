<?php
session_start();
include '../Project in WST/server/server.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure user ID is provided
    if (isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']); // Sanitize input

        // Prepare SQL statement and action feedback
        $stmt = null;
        $action = '';

        if (isset($_POST['block'])) {
            // Block user
            $stmt = $conn->prepare("UPDATE cgt_accounts SET status = 'blocked' WHERE id = ?");
            $action = 'blocked';
        } elseif (isset($_POST['delete'])) {
            // Permanently delete user from database
            // Delete related purchases
$delete_purchases_sql = "DELETE FROM cgt_user_purchase WHERE user_id = ?";
$stmt = $conn->prepare($delete_purchases_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Delete cart_items
$delete_purchases_sql = "DELETE FROM user_cart_items WHERE user_id = ?";
$stmt = $conn->prepare($delete_purchases_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

            // Delete chat_messages
            $delete_purchases_sql = "DELETE FROM chat_messages WHERE user_id = ?";
            $stmt = $conn->prepare($delete_purchases_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

// Now delete the user account
$delete_user_sql = "DELETE FROM cgt_accounts WHERE id = ?";
$stmt = $conn->prepare($delete_user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
        } elseif (isset($_POST['unblock'])) {
            // Unblock user
            $stmt = $conn->prepare("UPDATE cgt_accounts SET status = 'active' WHERE id = ?");
            $action = 'unblocked';
        }

        if ($stmt) {
            // Bind parameter, execute statement, and close
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                // Set success message in session
                $_SESSION['message'] = "User successfully $action.";
                $_SESSION['message_type'] = 'success';
            } else {
                // Set error message in session
                $_SESSION['message'] = "Failed to $action user. Please try again.";
                $_SESSION['message_type'] = 'error';
            }
            $stmt->close();
        } else {
            // Set error message for invalid action
            $_SESSION['message'] = "Invalid action. Please try again.";
            $_SESSION['message_type'] = 'error';
        }
    } else {
        // Set error message for missing user ID
        $_SESSION['message'] = "User ID is required.";
        $_SESSION['message_type'] = 'error';
    }

    // Redirect back to the users list
    header("Location: users.php");
    exit;
}

// If not POST request, redirect to users list
header("Location: users.php");
exit;
?>
