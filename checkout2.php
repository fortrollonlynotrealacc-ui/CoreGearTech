<?php
session_start();
include '../Project in WST/server/server.php';

// Retrieve product information from URL parameters
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;
$product_name = isset($_GET['product_name']) ? $_GET['product_name'] : '';
$product_image = isset($_GET['product_image']) ? $_GET['product_image'] : '';
$product_price = isset($_GET['product_price']) ? $_GET['product_price'] : 0;
$quantity = isset($_GET['quantity']) ? $_GET['quantity'] : 1;
$totalAmount = $product_price * $quantity; // Calculate total price
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($product_id)) {
    // Capture guest information
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $paypalAccount = $_POST['paypalAccount'];
    $purchaseDate = date('Y-m-d H:i:s'); // Get the current date for purchase

    // Validate phone number format
    if (!preg_match('/^\+63\d{10}$/', $phone)) {
        echo "<script>alert('Invalid phone number. The format must be +63 followed by exactly 10 digits.');</script>";
        exit(); // Stop further processing if validation fails
    }
    
    $conn->begin_transaction();

    try {
        // Insert guest information into 'cgt_guests' table
        $stmt = $conn->prepare("INSERT INTO cgt_guests (guest_name, guest_email, guest_address, guest_number, paypal_accountnumber) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $address, $phone, $paypalAccount);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting guest information: " . $stmt->error);
        }
        $guest_id = $stmt->insert_id; // Get the last inserted ID (guest_id)
        $stmt->close();

        // Insert a new purchase record and get the purchase ID
        $purchaseStmt = $conn->prepare("INSERT INTO cgt_purchases (guest_id, purchase_date) VALUES (?, ?)");
        $purchaseStmt->bind_param("is", $guest_id, $purchaseDate);

        if (!$purchaseStmt->execute()) {
            throw new Exception("Error inserting purchase: " . $purchaseStmt->error);
        }
        $purchase_id = $purchaseStmt->insert_id; // Retrieve the purchase ID
        $purchaseStmt->close();

        // Insert product into 'cgt_guest_purchases' and update stock
        $insertProductStmt = $conn->prepare("INSERT INTO cgt_guest_purchases (guest_id, product_id, quantity, price, purchase_date) VALUES (?, ?, ?, ?, ?)");
        $updateStockStmt = $conn->prepare("UPDATE cgt_products SET quantity = quantity - ? WHERE product_id = ? AND quantity >= ?");

        // Log stock left before update
        $stockCheckStmt = $conn->prepare("SELECT quantity, product_name FROM cgt_products WHERE product_id = ?");
        $stockCheckStmt->bind_param("i", $product_id);

        if (!$stockCheckStmt->execute()) {
            throw new Exception("Error checking stock for product_id {$product_id}: " . $stockCheckStmt->error);
        }

        $stockCheckStmt->bind_result($currentStock, $product_name);
        $stockCheckStmt->fetch();
        $stockCheckStmt->close();

        // Check if enough stock is available
        if ($currentStock < $quantity) {
            throw new Exception("Insufficient stock for product: {$product_name}. Requested: {$quantity}, Available: {$currentStock}");
        }

        // Insert into cgt_guest_purchases
        $insertProductStmt->bind_param("iiids", $guest_id, $product_id, $quantity, $totalAmount, $purchaseDate);

        if (!$insertProductStmt->execute()) {
            throw new Exception("Error inserting product for product_id {$product_id}: " . $insertProductStmt->error);
        }

        // Update stock in cgt_products
        $updateStockStmt->bind_param("iii", $quantity, $product_id, $quantity);

        if (!$updateStockStmt->execute() || $updateStockStmt->affected_rows === 0) {
            throw new Exception("Stock update failed for product: {$product_name}. Stock available: {$currentStock}");
        }

        $insertProductStmt->close();
        $updateStockStmt->close();

        // Commit transaction if all operations were successful
        $conn->commit();

        echo "<script>alert('Guest information and product saved. Redirecting to payment...');</script>";
        header("Location: thank_you.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction if an error occurs
            $conn->rollback();
            $errorMessage = "Transaction failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="navbar.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap.bundle.min.js"></script>
</head>
<body>
    <style>
        .product-image { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
        table { width: 80%; margin: 20px auto; background-color: #343a40; color: #ffffff; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #444; text-align: center; color:white;}
        th { background-color: rgba(64, 135, 148, 0.7);}
        .form-group { margin: 10px 210px;}
        label { font-weight: bold; color:white;}
        input[type="text"], input[type="tel"], input[type="email"] { width: 100%; padding: 8px; box-sizing: border-box;}
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
            .btn-return:hover { color: #f0f0f0; text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
            .btn-primarys { padding: 10px 20px; background-color: #408794; color: #ffffff; border: none; cursor: pointer; margin-top: 20px; margin-left:45%;margin-bottom: 12%;}
            .modal-content {
        background-color: #002244; 
        color: #f0f8ff; }
        .btn-primarys:hover{
            background-color:#007bff;
        }
    </style>
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <button class="btn-return" onclick="window.location.href='product copy.php?product_id=<?php echo htmlspecialchars($product_id); ?>'">
        <i class="fa fa-reply"></i>
    </button> 
    <h1 class="page-title" style="text-align: center; color: white;"><i class="fa fa-credit-card"></i> Checkout</h1>

    <table>
        <tr>
            <th>Image</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <tr>
            <td><img src="<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>" class="product-image"></td>
            <td><?php echo htmlspecialchars($product_name); ?></td>
            <td><?php echo $quantity; ?></td>
            <td>$<?php echo number_format($product_price, 2); ?></td>
            <td>$<?php echo number_format($totalAmount, 2); ?></td>
        </tr>
    </table>

    <!-- Guest Information Form -->
    <form method="POST">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number: Include +63</label>
            <input type="tel" id="phone" name="phone" pattern="\+63\d{10}" title="Phone number must be +63 followed by exactly 10 digits." required>
        </div>
        <div class="form-group">
            <label for="paypalAccount">PayPal Account </label>
            <input type="email" id="paypalAccount" name="paypalAccount" required>
        </div>

        <button type="submit" class="btn-primarys">Confirm / Buy</button>
    </form>
<?php include ('footer.php')?>
<!-- Modal for error -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($errorMessage)) : ?>
<script>
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
</script>
<?php endif; ?>s
</body>
</html>
