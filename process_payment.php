<?php
session_start();
include '../Project in WST/server/server.php';

// Set the default timezone to avoid issues with server's timezone settings
date_default_timezone_set('Asia/Manila'); // Change to your desired timezone

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>You must be logged in to proceed with the purchase.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'];
$payment_account = $_POST['account_number'] ?? null;
$purchase_date = date("Y-m-d H:i:s"); // Current date and time
$order_status = 'Pending'; // Initial order status

// Validate payment method and account number
if ($payment_method === 'cash_on_delivery') {
    $payment_account = 'N/A'; 
} elseif ($payment_method === 'gcash') {
    $payment_account = $_POST[$payment_method . '_number'] ?? null;
    if (!preg_match('/^\d{11}$/', $payment_account)) {
        $errorMessage = "Invalid {$payment_method} number. It must be 11 digits long.";
        header("Location: user_checkout.php?error=" . urlencode($errorMessage) . "&product_id=" . urlencode($product_id) . "&quantity=" . urlencode($quantity));
        exit;
    }
} elseif ($payment_method === 'paypal') {
    $payment_account = $_POST[$payment_method . '_number'] ?? null;
    if (!filter_var($payment_account, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid PayPal email address.";
        header("Location: user_checkout.php?error=" . urlencode($errorMessage) . "&product_id=" . urlencode($product_id) . "&quantity=" . urlencode($quantity));
        exit;
    }
} else {
    echo "<p>Invalid payment method selected.</p>";
    exit;
}


// Fetch cart items
$cart_query = "SELECT product_id, quantity, product_price FROM user_cart_items WHERE user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Your cart is empty.</p>";
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $price = $row['product_price'];

        // Check stock availability in `cgt_products`
        $stock_query = "SELECT product_name, quantity FROM cgt_products WHERE product_id = ?";
        $stmt_stock = $conn->prepare($stock_query);
        $stmt_stock->bind_param("i", $product_id);
        $stmt_stock->execute();
        $stock_result = $stmt_stock->get_result();
        
        if ($stock_result->num_rows === 0) {
            throw new Exception("Product ID $product_id does not exist.");
        }

        $product_data = $stock_result->fetch_assoc();
        $current_stock = $product_data['quantity'];
        $product_name = $product_data['product_name'];
        $stmt_stock->close();

        if ($quantity > $current_stock) {
            throw new Exception("Insufficient stock for product: $product_name. Stock left: $current_stock");
        }

        // Insert purchase details into `cgt_user_purchases`
        $insert_query = "INSERT INTO cgt_user_purchase (user_id, product_id, quantity, purchase_date, price, payment_method, account_number, order_status) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_query);
        $stmt_insert->bind_param("iiisssss", $user_id, $product_id, $quantity, $purchase_date, $price, $payment_method, $payment_account, $order_status);

        // Check if the query execution was successful
        if (!$stmt_insert->execute()) {
            throw new Exception("Error inserting purchase: " . $stmt_insert->error);
        }
        $stmt_insert->close();

        // Update product stock
        $update_query = "UPDATE cgt_products SET quantity = quantity - ? WHERE product_id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("ii", $quantity, $product_id);
        
        // Check if the query execution was successful
        if (!$stmt_update->execute()) {
            throw new Exception("Error updating stock: " . $stmt_update->error);
        }
        $stmt_update->close();
    }

    // Clear the cart after successful purchase
    $clear_cart_query = "DELETE FROM user_cart_items WHERE user_id = ?";
    $stmt_clear = $conn->prepare($clear_cart_query);
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();
    $stmt_clear->close();

    // Commit the transaction
    $conn->commit();

    // Redirect to the thank-you page
    header("Location: user_thankyou.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $errorMessage = $e->getMessage();
    
    // Redirect to the user checkout page with an error message
    header("Location: user_checkout.php?error=" . urlencode("Transaction failed: " . $errorMessage));
    
} finally {
    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>

