
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <button class="btn-return" style="margin-top: 5%; color: white;" onclick="window.history.back();">
        <i class="fa fa-reply"></i> <!-- Return icon color: #173a4a-->
    </button>
    <h1 class="page-title" style="text-align: center; color: white;"><i class="fa fa-credit-card"></i> Checkout</h1>

    <div class="checkout-container">
        <h2>User Details</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fname'] . ' ' . $user['mname'] . ' ' . $user['lname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
        <p><strong>Phone Number: +63</strong> <?php echo htmlspecialchars($user['phonenumber']); ?></p>

        <h2>Order Summary</h2>
        <table>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($cartItems as $item): 
                $subtotal = $item['product_price'] * $item['quantity'];
            ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-image" width="50" height="50"></td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4"><strong>Grand Total</strong></td>
                <td>$<?php echo number_format($grandTotal, 2); ?></td>
            </tr>
        </table>

        <h2>Select Payment Method</h2>
        <form action="process_payment.php" method="POST">
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

            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <button type="submit" class="btn-primary">Confirm / Buy</button>
        </form>
    </div>
    
    <style>
        /* Styling for checkout page */
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        .checkout-container { width: 50%; margin: 0 auto; background-color: rgb(25, 24, 24,0.7); padding: 20px; border-radius: 8px;color: white;}
        /*return button*/
        .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
            .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
        .product-image { object-fit: cover; border-radius: 5px; }
        table { width: 100%; margin-top: 20px; background-color: #343a40; color: #ffffff; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #444; text-align: center; }
        th { background-color: rgba(64, 135, 148, 0.7); }
        .btn-primary { padding: 10px 20px; background-color: #408794; color: #ffffff; border: none; cursor: pointer; margin-top: 20px; margin-left:40%;}
    </style>
</body>
</html>
