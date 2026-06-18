<?php
session_start();
include '../Project in WST/server/server.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>You must be logged in to proceed to checkout.</p>";
    exit;
}

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;
$quantity = isset($_GET['quantity']) ? $_GET['quantity'] : 1;
$errorMessage = "";
if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}

$user_id = $_SESSION['user_id'];

// Get the product details
if ($product_id) {
    $product_query = "SELECT p.product_id, p.product_name, p.product_price, p.product_image
                      FROM cgt_products AS p
                      WHERE p.product_id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();

    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
    } else {
        echo "<p>Product not found.</p>";
        exit;
    }
    $stmt->close();
}

// Get user details
$user_query = "SELECT fname, mname, lname, email, address, phonenumber FROM cgt_accounts WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
} else {
    echo "<p>User details not found.</p>";
    exit;
}

$stmt->close();
$conn->close();
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
    <script>
    // Function to display the payment method fields based on selection
    // Function to display the payment method fields based on selection and handle validation
    function togglePaymentFields() {
        var paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        var paypalField = document.getElementById("paypal-field");
        var gcashField = document.getElementById("gcash-field");
        var paypalInput = document.getElementById("paypal_number");
        var gcashInput = document.getElementById("gcash_number");

        // Hide both payment fields and reset "required" attribute
        paypalField.style.display = 'none';
        gcashField.style.display = 'none';
        paypalInput.required = false;
        gcashInput.required = false;

        // Show and require the relevant payment field
        if (paymentMethod === 'paypal') {
            paypalField.style.display = 'block';
            paypalInput.required = true; // Make PayPal input required
        } else if (paymentMethod === 'gcash') {
            gcashField.style.display = 'block';
            gcashInput.required = true; // Make GCash input required
        }
        // If Cash on Delivery is selected, no fields should be displayed or required
    }
    

    // Add event listener on page load to ensure fields reset properly
    document.addEventListener("DOMContentLoaded", function () {
        var paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        paymentMethods.forEach(function (radio) {
            radio.addEventListener("change", togglePaymentFields);
        });
    });
    </script>
</head>
<body>
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <button class="btn-return" onclick="window.location.href='user-product copy.php?product_id=<?php echo htmlspecialchars($product_id); ?>'">
    <i class="fa fa-reply"></i> 
    </button>
    <!-- <h1 class="page-title"><i class="fa fa-credit-card"></i> Checkout</h1> -->

    <div class="checkout-container">
    <h1 class="page-title" style ="text-align:center;"><i class="fa fa-credit-card"></i> Checkout</h1>
        <h2>User Details</h2>
        <p><strong>Name:</strong> <?= isset($user) ? htmlspecialchars($user['fname'] . ' ' . $user['mname'] . ' ' . $user['lname']) : 'N/A'; ?></p>
        <p><strong>Email:</strong> <?= isset($user) ? htmlspecialchars($user['email']) : 'N/A'; ?></p>
        <p><strong>Address:</strong> <?= isset($user) ? htmlspecialchars($user['address']) : 'N/A'; ?></p>
        <p><strong>Phone Number: +63</strong> <?= isset($user) ? htmlspecialchars($user['phonenumber']) : 'N/A'; ?></p>

        <h2>Order Summary</h2>
        <table>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <tr>
                <td><img src="<?= isset($product) ? htmlspecialchars($product['product_image']) : ''; ?>" alt="<?= isset($product) ? htmlspecialchars($product['product_name']) : ''; ?>" width="50" height="50"></td>
                <td><?= isset($product) ? htmlspecialchars($product['product_name']) : ''; ?></td>
                <td>
    <span id="quantity"><?= isset($_GET['quantity']) ? htmlspecialchars($_GET['quantity']) : '1'; ?></span>
</td>
                <td>$<?= isset($product) ? number_format($product['product_price'], 2) : '0.00'; ?></td>
                <td>$<?= isset($product) ? number_format($product['product_price'], 2)*$quantity: '0.00'; ?></td>
            </tr>
            <tr>
                <td colspan="4"><strong>Grand Total</strong></td>
                <td>$<?= isset($product) ? number_format($product['product_price'], 2)*$quantity : '0.00'; ?></td>
            </tr>
        </table>
        
        <h2>Select Payment Method</h2>
<form action="process_payment2.php" method="POST">
    <label>
        <input type="radio" name="payment_method" value="paypal" required>
        PayPal
    </label><br>
    <label>
        <input type="radio" name="payment_method" value="gcash" required>
        GCash
    </label><br>
    <label>
        <input type="radio" name="payment_method" value="cash_on_delivery" required>
        Cash on Delivery
    </label><br><br>

    <!-- PayPal number field -->
    <div id="paypal-field" style="display:none;">
        <label for="paypal_number">Enter PayPal Account:</label>
        <input type="email" name="paypal_number" id="paypal_number" placeholder="Enter PayPal Account">
    </div>

    <!-- GCash number field -->
    <div id="gcash-field" style="display:none;">
        <label for="gcash_number">Enter GCash Number:</label>
        <input type="text" name="gcash_number" id="gcash_number" placeholder="Enter GCash number">
    </div>

    <input type="hidden" name="user_id" value="<?= isset($user) ? $user_id : ''; ?>">
    <input type="hidden" name="product_id" value="<?= isset($product) ? $product['product_id'] : ''; ?>">
    <input type="hidden" name="product_name" value="<?= isset($product) ? htmlspecialchars($product['product_name']) : ''; ?>">
    <input type="hidden" name="product_price" value="<?= isset($product) ? $product['product_price'] : ''; ?>">
    <input type="hidden" name="quantity" value="<?= isset($_GET['quantity']) ? $_GET['quantity'] : '1'; ?>">
    <button type="submit" class="btn-primarys">Confirm / Buy</button>
</form>

    </div>
    <?php include ('userfooter.php') ?>
    <?php if (isset($_GET['error'])): ?>
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Transaction Failed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
</script>
<?php endif; ?>
    <style>
        /* Styling for checkout page */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        .checkout-container { width: 50%; margin: 0 auto; background-color: rgba(25, 24, 24,0.7); padding: 20px; border-radius: 8px; color: white;}
        .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;font-size: 2rem; cursor: pointer;}
        table { width: 100%; margin-top: 20px; background-color: #343a40; color: #ffffff; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #444; text-align: center; }
        th { background-color: rgba(64, 135, 148, 0.7); }
        .btn-primarys { padding: 10px 20px; background-color: #408794; color: #ffffff; border: none; cursor: pointer; margin-top: 20px; }
        .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
        .btn-primarys:hover{
            background-color:#007bff;
        }
        .modal-content {background-color: #002244; color: #f0f8ff; }
    </style>
</body>
</html>
