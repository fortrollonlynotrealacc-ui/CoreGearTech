<?php




// Check if the request is an AJAX call for cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['message' => 'User not logged in']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Get the JSON input from JavaScript
    $input = file_get_contents('php://input');

    // Check if any data was received
    if (!$input) {
        echo json_encode(['message' => 'No data received']);
        exit();
    }

    // Decode the JSON data
    $cartItems = json_decode($input, true);

    // Check if JSON decoding was successful
    if ($cartItems === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['message' => 'Invalid JSON input']);
        exit();
    }

    // Insert or update each item in the user_cart_items table
    foreach ($cartItems as $item) {
        $product_id = $item['id'];
        $product_name = $item['name'];
        $product_image = $item['image'];
        $product_price = $item['price'];
        $quantity = $item['quantity'];
        $added_at = date('Y-m-d H:i:s'); // Current timestamp

        // Check if the item already exists in the user's cart
        $sql_check = "SELECT quantity FROM user_cart_items WHERE user_id = ? AND product_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("is", $user_id, $product_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // If item exists, set the quantity to the incoming quantity instead of adding
            $sql_update = "UPDATE user_cart_items SET quantity = ?, added_at = ? WHERE user_id = ? AND product_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("isis", $quantity, $added_at, $user_id, $product_id);
            if (!$stmt_update->execute()) {
                echo json_encode(['message' => 'Failed to update cart item: ' . $conn->error]);
                exit();
            }
        
        } else {
            // If item doesn't exist, insert it
            $sql_insert = "INSERT INTO user_cart_items (user_id, product_id, product_name, product_image, product_price, quantity, added_at)
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isssdis", $user_id, $product_id, $product_name, $product_image, $product_price, $quantity, $added_at);
            if (!$stmt_insert->execute()) {
                echo json_encode(['message' => 'Failed to insert cart item: ' . $conn->error]);
                exit();
            }
        }
    }

    // If everything was successful
    echo json_encode(['message' => 'Cart saved successfully.']);
    exit();
}
?>
<!-- Navbar -->
<div class="navbar bg-dark fixed-top">
            <div class="container d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="myLogo">
                <a href="Website.php"><img src="logo.png" alt="myLogo" width="80" height="80"></a>
                </div>

                <!-- Navbar Links -->
            <div class="navbarLinks">
            <div class="navLink"><a href="Website.php">Home</a></div>
            <div class="navLink"><a href="product-catalog.php">Products</a></div>
            <div class="navLink"><a href="website.php#about">About</a></div>
            <div class="navLink"><a href="website.php#contact">Contact Us</a></div>
            <div class="navLink"><a href="website.php#team">Meet the Team</a></div>
                </div>

                <!-- Right side section (Search, Social Icons, Sign Up, Register) -->
                <div class="d-flex align-items-center">
                    <!-- Search Box -->
                    <div class="search-container">
                        <div class="search-box">
                        <input type="text" id="search-input" class ="search-input" placeholder="Search for products...">
                        <script src="search.js"></script>
                        <div class="suggestions" id="suggestions"></div>
                            <button class="search-btn" id="search-btn">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Social Media Icons -->
                    <div class="navbarIcons ms-3">
                        <a href="#" class="cart-icon" id="cart-icon">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <span class="cart-badge" id="cart-count">0</span>
                        </a>
                        
                        
                        <!-- Cart Dropdown -->
                        
<div class="cart-dropdown" id="cart-dropdown">
    <!-- Empty Cart Message -->
    <p id="empty-cart-message" style="display: block; color: rgb(37, 67, 96); text-align: center;">
        Your already in the cart.
    </p>

    <!-- Cart items list (hidden initially) -->
    <!-- <ul id="cart-items-list" style="display: none;"></ul>
    <div id="cart-total" style="display: none;">Total: $0.00</div>
    <div class="cart-actions" style="display: none;">
        <button onclick="viewCart()">Go to Cart</button>
        <button onclick="proceedToCheckout()">Proceed to Checkout</button>
    </div> -->
</div>

                    </div>

                    <!-- Sign Up and Register -->
                    <div class="sign-register ms-4">
                        <a href="login.php" class="sign-link me-2">Sign In</a>
                        <a href="register.php" class="sign-link">Register</a>
                    </div>
                </div>
            </div>
        </div>