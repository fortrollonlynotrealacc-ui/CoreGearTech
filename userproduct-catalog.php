<?php
session_start();
include '../Project in WST/server/server.php';

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Gear Tech - Product Catalog</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <!--LOGOUT-->
    <script>
        function showLogoutModal() {
            const modal = document.getElementById("logout-modal");
            modal.style.display = "block";
        }
        function closeModal() {
            const modal = document.getElementById("logout-modal");
            modal.style.display = "none";
        }
        function logout() {
            window.location.href = "Website.php"; // Redirect to logout page
        }
    </script>
    <style>
          /* Modal styles */
          #logout-modal {display: none;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.5);z-index: 1000;
          }
          #logout-modal .modal-content {position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);background-color: #0f1d2e;
          color:white;padding: 20px;border-radius: 10px;text-align: center;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);width: 80%; /* Adjusts the width */
          max-width: 300px; /* Ensures it doesn't get too wide */}
          #logout-modal button {margin: 10px;padding: 10px 20px;border: none;border-radius: 5px;cursor: pointer;font-size: 16px;}
          #logout-modal .confirm {background-color: blue;color: white;}
          #logout-modal .cancel { background-color: gray; color: white;}

        /*card*/
        .card-img-top {
            height: 200px;
            object-fit: contain;
        }
        .card-body {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        /* Navbar styles */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background-color: #343a40;
            transition: top 0.3s;
            height: 80px;
            display: flex;
            align-items: center;
        }

        .navLink a,
        .navbarIcons a {
            color: #ffffff; 
            text-decoration: none;
            margin: 6px;
        }

        .navbarLinks {
            display: flex;
            position:absolute;
            margin-right: 250px;
            margin-left: 100px;
            gap: 20px;
        }

        .navbarIcons {
            margin-left: 20px;
        }

        .navbarLinks a:hover,
        .navbarIcons a:hover {
            color: rgba(64, 135, 148, 0.7); 
        }

        /* Content below video */
        .scrollable-container {
            margin-top: 80px; /* Ensure content starts right below navbar */
            flex: 1;
        }

        /* Dropdown Menu Styles */
        .dropdown {
            position: relative; /* Positioning context for the dropdown */
        }

        .dropdown-menu {
            display: none; /* Initially hidden */
            position: absolute; /* Positioning the dropdown relative to the parent */
            background-color: #343a40; /* Same background color as navbar */
            border-radius: 5px;
            margin-top: 10px; /* Space between dropdown and navbar */
            z-index: 1000; /* Ensure dropdown is above other content */
        }

        .dropdown-item {
            color: white; /* White text for dropdown items */
            text-decoration: none;
            padding: 10px 20px; /* Padding for dropdown items */
            display: block; /* Make the dropdown items block-level elements */
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Change background on hover */
        }

        /* Show the dropdown when it has the 'show' class */
        .dropdown-menu.show {
            display: block; /* Show the dropdown */
        }

        /* Search Bar Styles */
        .search-container {
            position: relative;
        }

        .search-box {
            display: flex;
            align-items: center;
        }

        .search-input {
            width: 0; /* Start hidden */
            opacity: 0; /* Make it invisible */
            padding: 5px;
            margin-left: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: width 0.5s, opacity 0.5s; /* Smooth transition */
        }

        .search-input.active {
            width: 200px; /* Set width for the search input */
            opacity: 1; /* Make it visible */
        }

        .search-btn {
            background-color: transparent;
            border: none;
            color: white;
            cursor: pointer;
        }

        .search-btn:hover {
            color: rgba(64, 135, 148, 0.7);
            transition: color 0.3s; 
        }

        /* Sign Up and Register */
        .sign-register a {
            font-size: 14px;
            text-decoration: none;
            color: white; /* Text color */
            padding: 8px 16px; /* Padding for better clickability */
            border-radius: 5px; /* Rounded corners */
            transition: color 0.3s, border-bottom 0.3s; /* Smooth transition for hover effects */
        }

        .sign-register a:hover {
            color: rgba(64, 135, 148, 0.7);
            border-bottom: 1px solid white; /* Underline effect on hover */
        }

        /* Cart Badge */
        .cart-badge {
            position: absolute;
            top: 29px; 
            right: 362px; 
            background-color:rgba(64, 135, 148, 0.7);
            color: white;
            border-radius: 50%;
            padding: 1px 5px;
            font-size: 10px;
            display: none;
        }
        /*Cart dropdown*/
.cart-dropdown {
    display: none;
    position: absolute;
    right: 20;
    top: 57px; /* Position below the cart icon */
    width: 320px;
    background-color: rgb(255, 255, 255);
    color: #2b556c;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    padding: 15px;
    z-index: 1000;
    border-radius: 5px;
}

.cart-icon:hover + .cart-dropdown,
.cart-dropdown:hover {
    display: block; 
}

.cart-dropdown ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    max-height: 180px;
    overflow-y: auto;
}

.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.cart-item img {
    width: 50px;
    height: 50px;
    margin-right: 10px;
}

.cart-item-name {
    flex-grow: 1;
    margin-right: 10px;
}

.cart-item-quantity {
    margin-right: 10px;
}

.cart-item-remove {
    color: rgb(20, 80, 112);
    cursor: pointer;
}

.cart-total {
    text-align: right;
    font-weight: bold;
    margin-top: 10px;
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
}

        /* Category Heading */
        .category-heading {
            color: #ffffff;
            padding-top: 20px;
            padding-bottom: 10px;
            font-size: 24px;
            text-align: center;
        }
 
    .video-container1 {
    position: fixed; /* Ensure it's fixed to the viewport */
    top: 0; /* Align to the top */
    left: 0; /* Align to the left */
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: hidden; /* Hide overflow */
    z-index: -1; /* Position it behind other content */
}

#bg-video1 {
    min-width: 100%; /* Ensure video covers the full width */
    min-height: 100%; /* Ensure video covers the full height */
    width: auto; /* Auto width */
    height: auto; /* Auto height */
    position: absolute; /* Position absolute to cover container */
    top: 50%; /* Center vertically */
    left: 50%; /* Center horizontally */
    transform: translate(-50%, -50%); /* Center video */
    object-fit: cover; /* Cover the entire container */
}
.btn-success{
background-color: #343a40;
}

    </style>
</head>
<body>
    <div class="scrollable-container">
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
            <div class="navLink"><a href="#products">Products</a></div>
            <div class="navLink"><a href="userwebsite.php#about">About</a></div>
            <div class="navLink"><a href="userwebsite.php#contact">Contact Us</a></div>
            <div class="navLink"><a href="userwebsite.php#team">Meet the Team</a></div>
                </div>

                <!-- Right side section (Search, Social Icons, Sign Up, Register) -->
                <div class="d-flex align-items-center">
                    <!-- Search Box -->
                    <div class="search-container">
                        <div class="search-box">
                            <input type="text" class="search-input" id="search-input" placeholder="Search for products...">
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
        <div class="video-container1">
            <div class="video-background1"></div>
            <video autoplay muted loop id="bg-video1">
                <source src="products_bg.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="container mt-4">
            <!-- Monitors Section -->
            <h2 class="category-heading">Monitors</h2>
            <div class="row mt-4" id="monitor-list">
        
            <?php
    // Query to get products from the database
    $sql = "SELECT * FROM cgt_products WHERE product_category = 'Monitors'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {         
        while ($row = $result->fetch_assoc()) {             
            echo '<div class="col-md-3 mb-4 product-card" data-id="'. htmlspecialchars($row["product_id"]) .'" data-name="'. htmlspecialchars($row["product_name"]) .'">                     
                    <div class="card h-100">                         
                        <a href="user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                            <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        </a>                         
                        <div class="card-body text-center">                             
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>                             
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>                             
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>';
            
            // Check product quantity
            if ($row["quantity"] > 0) {
                echo '<button class="btn btn-success mb-2" onclick="addToCart(\''. htmlspecialchars($row["product_id"]) .'\', \''. htmlspecialchars($row["product_name"]) .'\', \''. htmlspecialchars($row["product_image"]) .'\', '. $row["product_price"] .')">Add to Cart</button>';
                echo '<button class="btn btn-primary" onclick="window.location.href=\'user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>';
            } else {
                echo '<p style =" color: red; font-weight:bold; font-size:25px;"disabled>Sold Out</p>';
            }
    
            echo '      </div>                     
                    </div>                 
                  </div>';         
        }     
    } else {         
        echo "<p>No products found.</p>";     
    }
    
    
?>
            </div>
<!-- Keyboards Section -->
<h2 class="category-heading">Keyboards</h2>
<div class="row mt-4" id="keyboard-list">
<?php
    // Query to get products from the database
    $sql = "SELECT * FROM cgt_products WHERE product_category = 'Keyboards'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {         
        while ($row = $result->fetch_assoc()) {             
            echo '<div class="col-md-3 mb-4 product-card" data-id="'. htmlspecialchars($row["product_id"]) .'" data-name="'. htmlspecialchars($row["product_name"]) .'">                     
                    <div class="card h-100">                         
                        <a href="user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                            <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        </a>                         
                        <div class="card-body text-center">                             
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>                             
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>                             
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>';
            
            // Check product quantity
            if ($row["quantity"] > 0) {
                echo '<button class="btn btn-success mb-2" onclick="addToCart(\''. htmlspecialchars($row["product_id"]) .'\', \''. htmlspecialchars($row["product_name"]) .'\', \''. htmlspecialchars($row["product_image"]) .'\', '. $row["product_price"] .')">Add to Cart</button>';
                echo '<button class="btn btn-primary" onclick="window.location.href=\'user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>';
            } else {
                echo '<p style =" color: red; font-weight:bold; font-size:25px;"disabled>Sold Out</p>';
            }
    
            echo '      </div>                     
                    </div>                 
                  </div>';         
        }     
    } else {         
        echo "<p>No products found.</p>";     
    }
?>    
</div>

<!-- Headsets Section -->
<h2 class="category-heading">Headsets</h2>
<div class="row mt-4" id="headset-list">
<?php
    // Query to get products from the database
    $sql = "SELECT * FROM cgt_products WHERE product_category = 'Headsets'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {         
        while ($row = $result->fetch_assoc()) {             
            echo '<div class="col-md-3 mb-4 product-card" data-id="'. htmlspecialchars($row["product_id"]) .'" data-name="'. htmlspecialchars($row["product_name"]) .'">                     
                    <div class="card h-100">                         
                        <a href="user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                            <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        </a>                         
                        <div class="card-body text-center">                             
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>                             
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>                             
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>';
            
            // Check product quantity
            if ($row["quantity"] > 0) {
                echo '<button class="btn btn-success mb-2" onclick="addToCart(\''. htmlspecialchars($row["product_id"]) .'\', \''. htmlspecialchars($row["product_name"]) .'\', \''. htmlspecialchars($row["product_image"]) .'\', '. $row["product_price"] .')">Add to Cart</button>';
                echo '<button class="btn btn-primary" onclick="window.location.href=\'user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>';
            } else {
                echo '<p style =" color: red; font-weight:bold; font-size:25px;"disabled>Sold Out</p>';
            }
    
            echo '      </div>                     
                    </div>                 
                  </div>';         
        }     
    } else {         
        echo "<p>No products found.</p>";     
    }
?>
</div>

<!-- Processors Section -->
<h2 class="category-heading">Processors</h2>
<div class="row mt-4" id="processor-list">
<?php
    // Query to get products from the database
    $sql = "SELECT * FROM cgt_products WHERE product_category = 'Processors'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {         
        while ($row = $result->fetch_assoc()) {             
            echo '<div class="col-md-3 mb-4 product-card" data-id="'. htmlspecialchars($row["product_id"]) .'" data-name="'. htmlspecialchars($row["product_name"]) .'">                     
                    <div class="card h-100">                         
                        <a href="user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                            <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        </a>                         
                        <div class="card-body text-center">                             
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>                             
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>                             
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>';
            
            // Check product quantity
            if ($row["quantity"] > 0) {
                echo '<button class="btn btn-success mb-2" onclick="addToCart(\''. htmlspecialchars($row["product_id"]) .'\', \''. htmlspecialchars($row["product_name"]) .'\', \''. htmlspecialchars($row["product_image"]) .'\', '. $row["product_price"] .')">Add to Cart</button>';
                echo '<button class="btn btn-primary" onclick="window.location.href=\'user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>';
            } else {
                echo '<p style =" color: red; font-weight:bold; font-size:25px;"disabled>Sold Out</p>';
            }
    
            echo '      </div>                     
                    </div>                 
                  </div>';         
        }     
    } else {         
        echo "<p>No products found.</p>";     
    }
?>
</div>
<!-- Graphics Card Section -->
<h2 class="category-heading">Graphics Card</h2>
<div class="row mt-4" id="graphics-list">
<?php
    // Query to get products from the database
    $sql = "SELECT * FROM cgt_products WHERE product_category = 'Graphics Card'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {         
        while ($row = $result->fetch_assoc()) {             
            echo '<div class="col-md-3 mb-4 product-card" data-id="'. htmlspecialchars($row["product_id"]) .'" data-name="'. htmlspecialchars($row["product_name"]) .'">                     
                    <div class="card h-100">                         
                        <a href="user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                            <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        </a>                         
                        <div class="card-body text-center">                             
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>                             
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>                             
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>';
            
            // Check product quantity
            if ($row["quantity"] > 0) {
                echo '<button class="btn btn-success mb-2" onclick="addToCart(\''. htmlspecialchars($row["product_id"]) .'\', \''. htmlspecialchars($row["product_name"]) .'\', \''. htmlspecialchars($row["product_image"]) .'\', '. $row["product_price"] .')">Add to Cart</button>';
                echo '<button class="btn btn-primary" onclick="window.location.href=\'user-product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>';
            } else {
                echo '<p style =" color: red; font-weight:bold; font-size:25px;"disabled>Sold Out</p>';
            }
    
            echo '      </div>                     
                    </div>                 
                  </div>';         
        }     
    } else {         
        echo "<p>No products found.</p>";     
    }
?>
</div>

        </div>
        <div>
            <h1 id="no-products-message" style="display: none; color: lightblue; text-align: center; margin-bottom:50px; padding-bottom:200px;">
                There is no available product like that.
            </h1>
        </div>
        <!-- JavaScript for Search and Cart Functionality -->
        <script>
let cartItems = [];
let cartCloseTimeout;
let isMouseInsideDropdown = false; // Track if the mouse is inside the dropdown

function addToCart(id, name, image, price) {
    const existingItem = cartItems.find(item => item.name === name);
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
/*function viewCart() {
    fetch('', {
        method: 'POST',
        body: JSON.stringify(cartItems),
        headers: { 'Content-Type': 'application/json' }
    }).then(response => response.json())
      .then(data => {
          if (data.message === 'User not logged in') {
              // Handle not logged in case (e.g., redirect to login)
              window.location.href = 'login.php'; // Redirect to login page
          } else {
              window.location.href = 'usercart_items.php'; // Redirect to cart page
          }
      });
}*/
/*
function proceedToCheckout() {
    // Save cart items to session or storage
    fetch('usercart_items.php', {
        method: 'POST',
        body: JSON.stringify(cartItems),
        headers: { 'Content-Type': 'application/json' }
    }).then(response => response.json())
      .then(data => {
          if (data.message === 'User not logged in') {
              // Handle not logged in case (e.g., redirect to login)
              window.location.href = 'login.php'; // Redirect to login page
          } else {
              window.location.href = 'checkout.php'; // Redirect to checkout page
          }
      });
}*/

/*function proceedToCheckout() {
    saveCartToDatabase(); // Save the current cart items
    window.location.href = 'checkout.php'; // Redirect to the checkout page
}*/



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

/*function viewCart() {
    fetch('usercart_items.php', {
        method: 'POST',
        body: JSON.stringify(cartItems),
        headers: { 'Content-Type': 'application/json' }
    }).then(response => {
        window.location.href = 'usercart_items.php'; 
    });
}

function proceedToCheckout() {
    // Save cart items to session or storage
    fetch('usercart_items.php', {
        method: 'POST',
        body: JSON.stringify(cartItems),
        headers: { 'Content-Type': 'application/json' }
    }).then(response => {
        window.location.href = 'usercart_items.php'; // Redirect to checkout page
    });
}*/


        // Function to enable live search as the user types
function searchProduct() {
    const searchInput = document.getElementById('search-input').value.toLowerCase(); // Get the search input
    const productCards = document.querySelectorAll('.product-card'); // Get all product cards
    const categories = {
        'monitors': document.getElementById('monitor-list'),
        'keyboards': document.getElementById('keyboard-list'),
        'headsets': document.getElementById('headset-list'),
        'processors': document.getElementById('processor-list'),
        'graphics card': document.getElementById('graphics-list'),
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
        
        
    </div>
</body>
</html>
