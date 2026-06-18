<?php
session_start();
include '../Project in WST/server/server.php'; // Include your database connection

// Get the product ID from the URL
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

// Query to fetch product details for the selected product
$sql = "SELECT * FROM cgt_products WHERE product_id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();


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
$product_id = intval($product['id']); 

$sql = "SELECT f.rating, f.feedback_message, 
               CONCAT(u.fname, ' ', u.lname) AS user_full_name 
        FROM feedback f
        JOIN cgt_accounts u ON f.user_id = u.id
        WHERE f.product_id = ?
        ORDER BY f.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<div class="feedback-item">';
    echo '<p><strong>' . htmlspecialchars($row['user_full_name']) . ':</strong> ';
    echo '<span class="rating">' . str_repeat('⭐', $row['rating']) . '</span></p>';
    echo '<p>' . htmlspecialchars($row['feedback_message']) . '</p>';
    echo '</div>';
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="navbar.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap.bundle.min.js"></script>

    <title>Product Details</title>
<style>
    html {scroll-behavior: smooth;}
    body{background-color: rgb(25, 24, 24);color:white;}
    /*return button*/
    .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
    .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
    /* Suggestions Styling */
    .suggestions {position: absolute;top: 100%;left: 0;width: 100%;max-height: 300px;overflow-y: auto;background-color: #fff;
                color:black;z-index: 1000;}
    .suggestion-item {display: flex;align-items: center;padding: 10px;cursor: pointer;}
    .suggestion-item img {width: 40px;height: 40px;object-fit: cover;margin-right: 10px;}
    .suggestion-item:hover {background-color: #f0f0f0;}
    .rating input[type="radio"] {margin-left: 10px; }
    .rating label {margin-left: 20px;}
    .admin-reply {border-left: 4px solid #007bff;padding: 10px;margin-top: 10px;font-style: italic;color:white;}
    .navbarLinks {display: flex;position:absolute;margin-right: 250px;margin-left: 100px;gap: 20px;}
    .cart-badge {position: absolute;top: 29px; right: 362px;background-color:rgba(64, 135, 148, 0.7);
       color: white;border-radius: 50%;padding: 1px 5px;font-size: 10px;display: none;}
    /*specs style*/ 
    .product-specs {font-size: 1.1rem;color: #ddd;}
    .spec-list {list-style-type: circle; padding-left: 20px;}
    .spec-list li {margin-bottom: 8px; }
    /*RELATED PRODUCTS */
    .related-products {margin-top: 20px;}
    .related-products h3 {font-size: 1.5rem;margin-bottom: 10px;color: #ddd;}
    .product-suggestions {display: flex;justify-content: space-between;gap: 15px;}
    .suggestion-product {flex: 1;text-align: center;background-color: #333;padding: 10px;border-radius: 5px;transition: transform 0.2s;}
    .suggestion-product:hover {transform: scale(1.05);}
    .suggestion-product img {max-width: 100%;height: 100px;object-fit: cover;border-radius: 5px;margin-bottom: 10px;}
    .suggestion-product p {color: white;font-size: 1rem;margin: 0;}
    .suggestion-product a {text-decoration: none; color: inherit; }
    .rating {
    display: flex;
    align-items: center;
    justify-content: start;
    gap: 10px; /* Adjust spacing between stars */
}

.rating input {
    display: none; /* Hide the radio buttons */
}

.rating label {
    font-size: 2rem;
    color: #ccc; /* Default color for stars */
    cursor: pointer;
    transition: color 0.3s ease-in-out, transform 0.2s ease;
}

.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
    color: gold; /* Glowing effect for stars */
}

.rating label:hover ~ label {
    color: #ccc; /* Reset stars after hovered star */
}

.rating input:checked + label {
    transform: scale(1.2); /* Slight enlargement for selected star */
}

.rating label span {
    font-size: 1rem;
    color: white;
    margin-left: 5px;
    position: relative;
    top: -5px;
    display: inline-block;
}
input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

/* .feedback-form {
    border: solid green 1px;
} */

.errorMessage {
    margin-left: 31%;
    display: none;
    position: absolute;
    /* color: lightblue; */
    color: rgba(64, 135, 148); 
    font-size: 20px;
}
 /* Social icons for "Share With" */
 .social-icon {
    color: white;
    text-decoration: none;
    margin-right: 20px;
    margin-left: 10px;
    font-size: 20px;
    margin-bottom: 20px;
}

.social-icon:hover {
    /*color: rgba(64, 135, 148, 0.7);*/
    color: lightblue;
    /* font-size: 21px; */
}
.copy-link:hover{
color:lightblue;
}
.modal-content {
        background-color: #002244; 
        color: #f0f8ff; 
    }
    .decrease, .increase{
            background-color: transparent; /* Make background transparent */
            border:none;
            color:white;
            font-size: 20px;
            margin: 15px;
        }
        .decrease:hover,.increase:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Slight hover effect */
        }
        .add-to-cart:hover{
            color: #1c475c;
        }
</style>
</head>

<body>
<script src="navbar.js"></script>
    <!-- <button class="btn-return" style="margin-top: 5%;" onclick="window.location.href='userproduct-catalog.php';">
        <i class="fa fa-reply"></i>
    </button> -->
        <!-- Navbar -->
        <div class="navbar bg-dark fixed-top">
            <div class="container d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="myLogo">
                <a href="userwebsite.php"><img src="logo.png" alt="myLogo" width="80" height="80"></a>
                </div>

                <!-- Navbar Links -->
            <div class="navbarLinks">
            <div class="navLink"><a href="userwebsite.php">Home</a></div>
            <div class="navLink"><a href="userproduct-catalog.php">Products</a></div>
            <div class="navLink"><a href="userwebsite.php#about">About</a></div>
            <div class="navLink"><a href="userwebsite.php#contact">Contact Us</a></div>
            <div class="navLink"><a href="userwebsite.php#team">Meet the Team</a></div>
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
                        <a href="profile.php"><i class="fa fa-user" aria-hidden="true"></i></a>
                        <a href="message.php" class="ms-3"><i class="fa fa-comment" aria-hidden="true"></i></a>
                        <!-- Cart Dropdown -->
                        
<div class="cart-dropdown" id="cart-dropdown">
    <!-- Empty Cart Message -->
    <p id="empty-cart-message" style="display: block; color: rgb(37, 67, 96); text-align: center;">
        Your cart is empty.
    </p>

    <!-- Cart items list (hidden initially) -->
    <ul id="cart-items-list" style="display: none;"></ul>
    <div id="cart-total" style="display: none;">Total: $0.00</div>
    <div class="cart-actions" style="display: none;">
        <button onclick="viewCart()">Go to Cart</button>
        <button onclick="proceedToCheckout()">Proceed to Checkout</button>
    </div>
</div>
                </div>
                    <!-- Sign Up and Register -->
                    <div class="sign-register ms-4" style ="margin-right:60px;">
                    <!-- Logout Link -->
                    <a href="javascript:void(0);" onclick="showLogoutModal()" class="sign-link">Log Out</a>
                    <!-- Logout Modal -->
                    <div id="logout-modal">
                       <div class="modal-content">
                       <p>Are you sure you want to log out?</p>
                       <button class="confirm" onclick="logout()">Yes</button>
                       <button class="cancel" onclick="closeModal()">No</button>
                     </div>
                </div> 
    </div>
                </div>
            </div>
        </div>
        <script src="logout.js"></script>
    <!-- Main Content -->
    <div class="main-container">
        <div class="product-main">
            <!-- Product Image -->
            <div class="product-image">
                <img src="<?php echo $product['product_image']; ?>" alt="Product Image">
            </div>

            <!-- Product Details -->
            <div class="product-content">
                <h1 class="product-title" style="color:lightblue"><?php echo $product['product_name']; ?></h1>
                <p class="product-price">$<?php echo number_format($product['product_price'], 2); ?></p>
                <p class="product-specs">
                    <?php 
                         if (!empty($product['product_specs'])) {
                       // Split product_specs into separate lines
                         $specifications = explode("\n", $product['product_specs']);
                         echo '<ul class="spec-list">';
                         foreach ($specifications as $spec) {
                         echo '<li>' . htmlspecialchars($spec) . '</li>';
                         }
                         echo '</ul>';
                        } else {
                            echo 'No specifications available.';
                        }
                    ?>
                </p>
                <div class="button-container">
                    <?php if ($product['quantity'] > 0): ?>
                        
                        <form action="buynow.php" method="GET">
    <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
    <input type="hidden" name="quantity" id="hiddenQuantity" value="1"> <!-- Hidden input to store quantity -->

    <!-- Quantity selection -->
    <div  class="quantity">
        <button  type="button" class="decrease"><i class="fa fa-minus"></i></button>
        <span id="quantity" style = "font-size:20px;font-weight:bold;">1</span> <!-- Display quantity here -->
        <button  type="button" class="increase"><i class="fa fa-plus"></i></button>
    

    <button type="submit" class="buy-now"><i class="fa fa-credit-card"></i> Buy Now</button></div></form>
    <button class="add-to-cart" style="font-size: 30px;background-color:transparent;boder:none;" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo $product['product_name']; ?>', '<?php echo $product['product_image']; ?>', <?php echo $product['product_price']; ?>)"><i class="fa fa-cart-plus" aria-hidden="true"></i></button>

                    <?php else: ?>
                         <p class="sold-out-message" style="color:rgb(49, 68, 118); font-weight: bold; font-size:30px;">Sold Out</p>
                    <?php endif; ?>
                </div>
                <?php echo '<p class="product-stocks" style="margin-top:20px;">Available Stocks: ' . htmlspecialchars($product['quantity']) . '</p>'; ?>
                <div class="con">
    <br>Share With: 
    <a href="https://www.facebook.com/profile.php?id=61569578181980" target="_blank"><i class="fa fa-facebook social-icon" aria-hidden="true"></i></a>
    <a href="https://x.com/gear_core33" target="_blank"><i class="fa fa-twitter social-icon" aria-hidden="true"></i></a>
    <a href="https://www.instagram.com/coregeartech/" target="_blank"><i class="fa fa-instagram social-icon" aria-hidden="true"></i></a>
    <a href="#" class="copy-link" onclick="copyLink(); return false;" style="text-decoration: none; color: white; margin-left: 10px;"><i class="fa fa-clipboard social-icon" style = "font-size:15px;"aria-hidden="true">Copy Link</i></a>
</div>
            </div>
        </div>
      
<script>
    // Get the quantity span, buttons, and hidden input
    const quantitySpan = document.getElementById('quantity');
    const hiddenQuantity = document.getElementById('hiddenQuantity');
    const decreaseButton = document.querySelector('.decrease');
    const increaseButton = document.querySelector('.increase');

    // Decrease quantity
    decreaseButton.addEventListener('click', () => {
        let currentQuantity = parseInt(quantitySpan.innerText);
        if (currentQuantity > 1) {
            quantitySpan.innerText = currentQuantity - 1;
            hiddenQuantity.value = quantitySpan.innerText; // Update hidden input value
        }
    });

    // Increase quantity
    increaseButton.addEventListener('click', () => {
        let currentQuantity = parseInt(quantitySpan.innerText);
        quantitySpan.innerText = currentQuantity + 1;
        hiddenQuantity.value = quantitySpan.innerText; // Update hidden input value
    });

    // Initialize hidden input with current quantity
    hiddenQuantity.value = quantitySpan.innerText;
</script>
        <!-- Product Description and Feedback -->
        <div class="product-tabs">
            <div class="product-tab active" id="description-tab" onclick="toggleTab('description')">Description</div>
            <div class="product-tab" id="feedback-tab" onclick="toggleTab('feedback')">Feedback</div>
        </div>

        <div class="tab-content active" id="description">
            <h3>Description</h3>
            <p><?php echo $product['product_desc']; ?></p>
        </div>
<?php
$product_id = $_GET['product_id']; // Or however you retrieve the product ID
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Check if the user has purchased the product and retrieve the order status
$query = "SELECT order_status FROM cgt_user_purchase WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

$has_purchased = $result->num_rows > 0;
$order_status = '';

if ($has_purchased) {
    // Fetch the order status if purchase record is found
    $row = $result->fetch_assoc();
    $order_status = $row['order_status'];
}

$stmt->close();
$conn->close();
?>


        <div class="tab-content" id="feedback">
            <h3>Feedback</h3>

            <!-- Feedback Form -->
            <div class="feedback-form">
        <?php if ($has_purchased && $order_status === 'Already Received'):?>
        <!-- <h3>Leave Your Feedback</h3> -->
        <p class="errorMessage">Please Select a Rating</p>
        <form id="feedback-form" method="POST" action="submit_feedback.php" onsubmit="return validateForm()">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <label for="rating">Rating:</label>
            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5" required>
                <label for="star5"><i class="fa fa-star"></i><span>5</span></label>
                <input type="radio" id="star4" name="rating" value="4" required>
                <label for="star4"><i class="fa fa-star"></i><span>4</span></label>
                <input type="radio" id="star3" name="rating" value="3" required>
                <label for="star3"><i class="fa fa-star"></i><span>3</span></label>
                <input type="radio" id="star2" name="rating" value="2" required>
                <label for="star2"><i class="fa fa-star"></i><span>2</span></label>
                <input type="radio" id="star1" name="rating" value="1" required>
                <label for="star1"><i class="fa fa-star"></i><span>1</span></label>
            </div>
            <p id="rating-error" style="color: red; display: none;">Please select a rating.</p>
            <label for="feedback-message">Your Feedback:</label>
            <textarea name="feedback_message" id="feedback-message" placeholder="Enter your feedback..." required></textarea>
            <button id="submitFeedback" type="submit">Submit Feedback</button>
        </form>
        <?php else:?>
            <p style = "color:lightblue";>Purchase the product to add a feedback</p>
            
            <button id="submitFeedback" type="submit" style = "display:none;">Submit Feedback</button>
            <!-- </form> -->
        <?php endif?>
</div>

<script>
function validateForm() {
    const ratingSelected = document.querySelector('input[name="rating"]:checked');
    const ratingError = document.getElementById('rating-error');

    if (!ratingSelected) {
        ratingError.style.display = "block"; // Show error message
        return false; // Prevent form submission
    }

    ratingError.style.display = "none"; // Hide error message if valid
    return true; // Allow form submission
}
</script>


            <!-- Feedback List -->
            <div class="feedback-list">
<?php
include '../Project in WST/server/server.php'; 

$product_id = intval($product['product_id']); 

$sql = "SELECT f.rating, f.feedback_message, f.admin_reply, 
               CONCAT(u.fname, ' ', u.lname) AS user_full_name 
        FROM feedback f
        JOIN cgt_accounts u ON f.user_id = u.id
        WHERE f.product_id = ?
        ORDER BY f.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="feedback-item">';
        echo '<p><strong>' . htmlspecialchars($row['user_full_name']) . ':</strong> ';
        echo '<span class="rating">' . str_repeat('⭐', $row['rating']) . '</span></p>';
        echo '<p>' . htmlspecialchars($row['feedback_message']) . '</p>';

        // Only display the admin's reply if it exists
        if (!empty($row['admin_reply'])) {
            echo '<p class="admin-reply"><strong>Admin Reply:</strong> ' . htmlspecialchars($row['admin_reply']) . '</p>';
        }
        
        echo '</div>';
    }
} else {
    echo '<p>No feedback yet. Be the first to leave a review!</p>';
}

$stmt->close();
?>

</div>
        </div>
        <div class="related-products">
    <h3>Products You May Like</h3>
    <div class="product-suggestions">
        <?php
        // Query to fetch 4 random products
        $sql_related = "SELECT product_id, product_name, product_image FROM cgt_products WHERE product_id != ? ORDER BY RAND() LIMIT 4";
        $stmt_related = $conn->prepare($sql_related);
        $stmt_related->bind_param('i', $product_id);
        $stmt_related->execute();
        $result_related = $stmt_related->get_result();

        if ($result_related->num_rows > 0) {
            while ($related = $result_related->fetch_assoc()) {
                echo '<div class="suggestion-product">';
                echo '<a href="user-product copy.php?product_id=' . $related['product_id'] . '">';
                echo '<img src="' . htmlspecialchars($related['product_image']) . '" alt="' . htmlspecialchars($related['product_name']) . '">';
                echo '<p>' . htmlspecialchars($related['product_name']) . '</p>';
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No related products available.</p>';
        }
        $stmt_related->close();
        ?>
    </div>
</div>
    </div>
    <!-- Footer -->
<div class="footer">
    <div class="container text-center">
        <div class="footer-contents">
            <p><a class="c">CONTACT US: </a></p>
            <p><a href="#"><i class="fa fa-envelope" aria-hidden="true"></i>: CoreGearTech@gmail.com</a></p>
            <p><a href="#"><i class="fa fa-home" aria-hidden="true"></i>: Manila Philippines</a></p>
            <p><a href="#"><i class="fa fa-phone" aria-hidden="true"></i>: 09983499383</a></p>
        </div>
        <div class="footer-logo">
            <a href="userwebsite.php"><img src="logo.png" alt="Core Gear Tech Logo" width="20%"></a>
        </div>
        <p style ="color:gray;">© 2024 Core Gear Tech | All Rights Reserved</p>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Link copied to clipboard successfully!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <script>
    function copyLink() {
        // Get the current URL
        let currentURL = new URL(window.location.href);

        // Replace 'user-' in the pathname
        let pathname = currentURL.pathname.replace('user-', '');

        // Construct the base URL without 'user-' and with only the 'product_id' query parameter
        let baseURL = `${currentURL.origin}${pathname}`;
        let productId = currentURL.searchParams.get('product_id');

        // Append only the 'product_id' to the new URL
        if (productId) {
            baseURL += `?product_id=${productId}`;
        }

        // Copy the updated URL to the clipboard
        navigator.clipboard.writeText(baseURL).then(() => {
            // Display the success modal
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        }).catch(err => {
            console.error('Could not copy text: ', err);
        });
    }
    function buynow(id, name, image, price){
        const existingItem = cartItems.find(item => item.id === id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cartItems.push({ id, name, image, price, quantity: 1 });
    }

    updateCart();
    // Save item to the database
    fetch('', {
        method: 'POST',
        body: JSON.stringify(cartItems),
        headers: { 'Content-Type': 'application/json' }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log("Item saved to database.");
          } else {
              console.error("Failed to save item to database.");
          }
      });
      window.location.href = 'user_checkout.php';
    }
    function checkStarInput() {

        let isClick = false;

        const stars = document.querySelectorAll(".rating > input");
        const btn = document.querySelector("#submitFeedback");
        const errorMessage = document.querySelector(".errorMessage");

        console.log(btn)

        stars.forEach(star => {
            star.addEventListener('click', e => {
                isClick = true;
                console.log(isClick)
            })
        })

        btn.addEventListener("click", e=> {
            if (isClick === false) {
                errorMessage.style.display = "block"
                setTimeout(() => {
                errorMessage.style.display = "none";
            }, 2000); 
            }
        });

    }

    checkStarInput()

    document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-input");
    const suggestionsContainer = document.getElementById("suggestions");

    searchInput.addEventListener("input", function () {
        const query = searchInput.value.trim();
        if (!query) {
            suggestionsContainer.innerHTML = ''; // Clear suggestions if input is empty
            return;
        }

        // Fetch suggestions from backend
        fetch(`search_suggestions.php?query=${encodeURIComponent(query)}`)
            .then((response) => response.json())
            .then((data) => {
                suggestionsContainer.innerHTML = ''; // Clear previous suggestions

                data.forEach((product) => {
                    const suggestionItem = document.createElement("div");
                    suggestionItem.className = "suggestion-item";
                    suggestionItem.setAttribute("data-product-id", product.id);

                    suggestionItem.innerHTML = `
                        <img src="${product.image}" alt="${product.name}">
                        ${product.name}
                    `;

                    suggestionsContainer.appendChild(suggestionItem);
                });
            })
            .catch((error) => console.error("Error fetching suggestions:", error));
    });

    suggestionsContainer.addEventListener("click", function (event) {
        const target = event.target.closest(".suggestion-item");
        if (target) {
            const selectedProductId = target.getAttribute("data-product-id");
            if (selectedProductId) {
                window.location.href = `user-product copy.php?product_id=${selectedProductId}`;
            }
        }
    });
});

        function toggleTab(tabName) {
            var tabs = document.querySelectorAll('.product-tab');
            var contents = document.querySelectorAll('.tab-content');

            tabs.forEach(function (tab) {
                tab.classList.remove('active');
            });
            contents.forEach(function (content) {
                content.classList.remove('active');
            });

            document.getElementById(tabName + '-tab').classList.add('active');
            document.getElementById(tabName).classList.add('active');
        }



let cartItems = [];
let cartCloseTimeout;
let isMouseInsideDropdown = false; // Track if the mouse is inside the dropdown

function addToCart(id, name, image, price) {
    const existingItem = cartItems.find(item => item.id === id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cartItems.push({ id, name, image, price, quantity: 1 });
    }

    updateCart();
    showCartDropdown();

    // Save item to the database
    fetch('', {
        method: 'POST',
        body: JSON.stringify(cartItems),
        headers: { 'Content-Type': 'application/json' }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log("Item saved to database.");
          } else {
              console.error("Failed to save item to database.");
          }
      });
}

document.addEventListener("DOMContentLoaded", function() {
});

function loadCartItems() {
    fetch('load_cart.php')
        .then(response => response.json())
        .then(data => {
            cartItems = data.cartItems || [];
            updateCart(); 
        });
}
document.addEventListener("DOMContentLoaded", function() {
    loadCartItems(); // Load items from the database on page load
});

function viewCart() {
    window.location.href = 'usercart_items.php';
}
function proceedToCheckout() {
    window.location.href = 'user_checkout.php';
}

function removeFromCart(id) {
    fetch('remove_item.php', {
        method: 'POST',
        body: JSON.stringify({ id }),
        headers: { 'Content-Type': 'application/json' }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              // Update the cart only if removal from the database was successful
              cartItems = cartItems.filter(item => item.id !== id);
              loadCartItems(); // Update the UI with the removed item
              console.log("Item removed from database.");
          } else {
              console.error("Failed to remove item from database.");
          }
      });
}


function updateCart() {
    const cartItemsList = document.getElementById('cart-items-list');
    const cartTotal = document.getElementById('cart-total');
    const emptyCartMessage = document.getElementById('empty-cart-message');
    const cartActions = document.querySelector('.cart-actions');
    const cartBadge = document.getElementById('cart-count'); // Badge for item count
    let totalAmount = 0;

    // Clear previous items in the list
    cartItemsList.innerHTML = '';

    // Update the badge visibility and count based on cart items
    if (cartItems.length === 0) {
        // Show "Your cart is empty" message and hide cart contents
        emptyCartMessage.style.display = 'block';
        cartItemsList.style.display = 'none';
        cartTotal.style.display = 'none';
        cartActions.style.display = 'none';
        cartBadge.style.display = 'none'; // Hide badge when no items
    } else {
        // Hide empty message, show cart contents
        emptyCartMessage.style.display = 'none';
        cartItemsList.style.display = 'block';
        cartTotal.style.display = 'block';
        cartActions.style.display = 'flex';
        cartBadge.style.display = 'inline-block'; // Show badge when items exist
        cartBadge.innerText = cartItems.length; // Update badge count

        // Populate the cart list with updated items
        cartItems.forEach(item => {
            const cartItem = document.createElement('li');
            cartItem.classList.add('cart-item');
            cartItem.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <span class="cart-item-name">${item.name}</span>
                <span class="cart-item-quantity">Qty: ${item.quantity}</span>
                <span class="cart-item-remove" onclick="removeFromCart('${item.id}')"><i class="fa fa-times-circle-o" aria-hidden="true"></i>
</span>
            `;
            cartItemsList.appendChild(cartItem);

            // Calculate total amount
            totalAmount += item.price * item.quantity;
        });

        // Update the total price
        cartTotal.innerText = `Total: $${totalAmount.toFixed(2)}`;
    }
}



function showCartDropdown() {
    const cartDropdown = document.getElementById('cart-dropdown');
    cartDropdown.style.display = 'block';

    clearTimeout(cartCloseTimeout);
    // Start a new 2-second timer to close the dropdown
    cartCloseTimeout = setTimeout(() => {
        if (!isMouseInsideDropdown) { 
            cartDropdown.style.display = 'none';
        }
    }, 2000);
}

function hideCartDropdown() {
    const cartDropdown = document.getElementById('cart-dropdown');
    cartDropdown.style.display = 'none';
}

const cartIcon = document.getElementById('cart-icon');
cartIcon.addEventListener('mouseenter', showCartDropdown);
cartIcon.addEventListener('mouseleave', () => {
    cartCloseTimeout = setTimeout(() => {
        if (!isMouseInsideDropdown) {
            hideCartDropdown();
        }
    }, 2000);
});

const cartDropdown = document.getElementById('cart-dropdown');

// Set flag when mouse enters or leaves the dropdown
cartDropdown.addEventListener('mouseenter', () => {
    isMouseInsideDropdown = true;
    clearTimeout(cartCloseTimeout); // Prevent auto-close when hovering
});

cartDropdown.addEventListener('mouseleave', () => {
    isMouseInsideDropdown = false;
    cartCloseTimeout = setTimeout(() => {
        if (!isMouseInsideDropdown) {
            hideCartDropdown();
        }
    }, 2000);
});

function searchProduct() {
    const searchInput = document.getElementById('search-input').value.toLowerCase(); // Get the search input
    const productCards = document.querySelectorAll('.product-card'); // Get all product cards
    const categories = {
        'monitors': document.getElementById('monitor-list'),
        'keyboards': document.getElementById('keyboard-list'),
        'headsets': document.getElementById('headset-list'),
        'processors': document.getElementById('processor-list'),
    };

    const noProductsMessage = document.getElementById('no-products-message'); // Get the no products message element
    let hasMatches = false; // Flag to track if there are matches

    // Check if search input is empty
    if (searchInput.trim() === '') {
        // If the search input is empty, show all product cards
        productCards.forEach(card => {
            card.style.display = 'block'; // Show all cards
        });
        noProductsMessage.style.display = 'none'; // Hide the message

        // Show all category headings
        for (const element of Object.values(categories)) {
            element.previousElementSibling.style.display = 'block'; // Show each category heading
        }

        return; // Exit the function
    }

    // Iterate through each product card to see if it matches the search input
    productCards.forEach(card => {
        const productName = card.getAttribute('data-name').toLowerCase(); // Get product name

        // If product name matches search input, show the card
        if (productName.includes(searchInput)) {
            card.style.display = 'block'; // Show the card
            hasMatches = true; // Set flag to true if there's a match
        } else {
            card.style.display = 'none'; // Hide the card
        }
    });

    // Control category visibility based on matches
    for (const [category, element] of Object.entries(categories)) {
        const visibleCards = element.querySelectorAll('.product-card[style*="display: block"]');
        if (visibleCards.length > 0) {
            element.previousElementSibling.style.display = 'block'; // Show category heading if there are visible cards
        } else {
            element.previousElementSibling.style.display = 'none'; // Hide category heading if no visible cards
        }
    }

    // Show or hide the no products message based on matches
    if (!hasMatches) {
        noProductsMessage.style.display = 'block'; // Show the message if no matches
    } else {
        noProductsMessage.style.display = 'none'; // Hide the message if there are matches
    }
}

// Add event listener for search input
document.getElementById('search-input').addEventListener('input', searchProduct);

// Search Input Activation
document.addEventListener("DOMContentLoaded", function() {
    const searchButton = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-input');

    searchButton.addEventListener('click', function() {
        // Toggle the 'active' class on the search input field
        searchInput.classList.toggle('active'); 

        // Focus on the input if it's active (visible)
        if (searchInput.classList.contains('active')) {
            searchInput.focus();
        } else {
            searchInput.blur(); // Remove focus if hiding the search bar
            searchInput.value = ''; // Clear the search input
            searchProduct(); // Reset the product list
        }
    });
});
//Drop down for category
document.addEventListener("DOMContentLoaded", function() {
    const productsDropdown = document.getElementById("productsDropdown");
    const dropdownMenu = document.getElementById("dropdownMenu");

    productsDropdown.addEventListener("click", function(event) {
        event.preventDefault(); // Prevent default link behavior
        dropdownMenu.classList.toggle("show"); // Toggle the 'show' class
    });

    // Close dropdown if clicking outside
    window.addEventListener("click", function(event) {
        if (!event.target.closest('.dropdown')) {
            dropdownMenu.classList.remove("show"); // Hide dropdown
        }
    });
});

    </script>
</body>
</html>
<?php
} else {
    echo "<p>Product not found.</p>";
}
?>
