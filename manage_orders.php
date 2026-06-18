<?php
session_start();
include '../Project in WST/server/server.php';

// Initialize success and error message variables
$success_message = '';
$error_message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $purchase_id = $_POST['purchase_id'];
    $order_status = $_POST['order_status'];

    // Get the current product_id and quantity for the purchase
    $select_query = "SELECT product_id, quantity FROM cgt_user_purchase WHERE purchase_id = ?";
    $stmt = $conn->prepare($select_query);
    $stmt->bind_param("i", $purchase_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $purchase = $result->fetch_assoc();
    $product_id = $purchase['product_id'];
    $quantity = $purchase['quantity'];

    // Update order status
    $update_query = "UPDATE cgt_user_purchase SET order_status = ? WHERE purchase_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $order_status, $purchase_id);

    if ($stmt->execute()) {
        $success_message = "Order status updated successfully!";

        // If the order was cancelled, update the product stock
        if ($order_status === 'Cancelled') {
            // Update the product quantity in cgt_products
            $update_product_query = "UPDATE cgt_products SET quantity = quantity + ? WHERE product_id = ?";
            $stmt = $conn->prepare($update_product_query);
            $stmt->bind_param("ii", $quantity, $product_id);

            if ($stmt->execute()) {
                $success_message = "Product quantity updated successfully!";
            } else {
                $error_message = "Error updating product quantity: " . $stmt->error;
            }
        }
    } else {
        $error_message = "Error updating order status: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all orders with sorting by purchase date
$query = "SELECT p.purchase_id, p.user_id, p.product_id, pr.product_name, p.quantity, p.purchase_date, p.price, p.payment_method, p.account_number, p.order_status 
          FROM cgt_user_purchase p
          JOIN cgt_accounts u ON p.user_id = u.id
          JOIN cgt_products pr ON p.product_id = pr.product_id
          ORDER BY p.purchase_date DESC"; // Orders by date in ascending order
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td{
            color:white;
            background-color: rgba(52, 52, 52, 0.8);
        }
        th {
            background-color: #f4f4f4;
        }
        form {
            display: inline-block;
        }
        button:hover{
            color: darkblue;
        }
        .btn-return { position: absolute; top: 20px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
        .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
        /* Video Background Styling */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            z-index: -1;
        }

        #bg-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));
            z-index: 1;
        }
    </style>
    <script>
        // Confirmation for cancel status update
        function confirmAction(form) {
        const selectedStatus = form.querySelector('select[name="order_status"]').value;

        if (selectedStatus === 'Cancelled') {
            return confirm("Are you sure you want to cancel this order?");
        } else if (selectedStatus === 'Already Received') {
            return confirm("Are you sure this product is already received?");
        }

        return true; // Allow submission for other statuses
    }
        // Show alert for successful or error messages
        function showAlert(message, type) {
            if (type === 'success') {
                alert(message);
            } else if (type === 'error') {
                alert("Error: " + message);
            }
        }
    </script>
</head>
<body>
<div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
</div>
<button class="btn-return" onclick="window.location.href='adminwebsite.php';">
        <i class="fa fa-reply"></i>
    </button>
    <h1 style ="color:white;text-align:center;">Manage Orders</h1>
    <table>
        <thead>
            <tr>
                <!-- <th>Purchase ID</th> -->
                <th>User</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Purchase Date</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Account </th>
                <th>Order Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <!-- <td><?php echo htmlspecialchars($row['purchase_id']); ?></td> -->
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($row['price'] * $row['quantity'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($row['account_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                    <td>
                    <form method="post" onsubmit="return confirmAction(this);">
                        <input type="hidden" name="purchase_id" value="<?php echo $row['purchase_id']; ?>">

                         <!-- Disable form if status is 'Already Received' or 'Cancelled' -->
                        <?php if ($row['order_status'] == 'Already Received' || $row['order_status'] == 'Cancelled'): ?>
                          <select name="order_status" disabled>
                           <option value="Pending" <?php echo $row['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                           <option value="Shipped" <?php echo $row['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                           <option value="On Delivery" <?php echo $row['order_status'] === 'On Delivery' ? 'selected' : ''; ?>>On Delivery</option>
                           <option value="Already Received" <?php echo $row['order_status'] === 'Already Received' ? 'selected' : ''; ?>>Already Received</option>
                           <option value="Cancelled" <?php echo $row['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                         </select>
                           <button type="button" disabled>Update</button>
                        <?php else: ?>
                            <select name="order_status" required>
                                <option value="Pending" <?php echo $row['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Shipped" <?php echo $row['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="On Delivery" <?php echo $row['order_status'] === 'On Delivery' ? 'selected' : ''; ?>>On Delivery</option>
                                <option value="Already Received" <?php echo $row['order_status'] === 'Already Received' ? 'selected' : ''; ?>>Already Received</option>
                                <option value="Cancelled" <?php echo $row['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        <button type="submit" name="update_status">Update</button>
                    <?php endif; ?>
                    </form>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- PHP Success or Error Messages -->
    <?php if (!empty($success_message)): ?>
        <script>showAlert("<?php echo $success_message; ?>", 'success');</script>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <script>showAlert("<?php echo $error_message; ?>", 'error');</script>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>
