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

        .navbarLinks {display: flex;position:absolute;margin-right: 250px;margin-left: 100px;gap: 20px;}

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
            top: 29px; /* Adjust this value to position the badge vertically */
            right: 320px; /* Adjust this value to position the badge horizontally */
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
/* Burger icon style */
.burger {
            font-size: 24px;
            color: #ffffff;
            cursor: pointer;
        }

        /* Sidebar styles */
        .sidebar {
            /*margin-top:55px;*/ 
            height: 150%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: rgba(52, 58, 64, 0.5);
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 120px;
            color: #ffffff;
            z-index: 1000;
        }

        .sidebar a {
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #ffffff;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar .closebtn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 24px;
        }
.notif-badge {
            position: fixed;
            top: 28px; /* Adjust this value to position the badge vertically */
            right: 292px; /* Adjust this value to position the badge horizontally */
            background-color:rgba(64, 135, 148, 0.7);
            color: white;
            border-radius: 50%;
            padding: 1px 5px;
            font-size: 10px;
            display:none;
            /*display: inline-block;*/
}
    </style>
</head>
<body>
    <div class="scrollable-container">
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="toggleSidebar()">&times;</a>
        <a href="users.php">Users</a>
        <a href="adminproducts.php">Products</a>
        <a href="manage_orders.php">Manage Orders</a>
        <a href="purchaseHistory.php">Purchase History</a>
        <a href="#feedbacks">Feedbacks</a>
        <a href="view_user_messages.php">Messages</a>
    </div>
        <!-- Navbar -->
        <div class="navbar bg-dark fixed-top">
            <div class="container d-flex justify-content-between align-items-center">
                <!-- Burger Icon -->
                <div class="burger" onclick="toggleSidebar()">
                    <i class="fa fa-bars"></i>
                </div>

                <!-- Logo -->
                <div class="myLogo" style ="position:absolute; margin-left:15px;">
                    <a href="adminwebsite.php"><img src="logo.png" alt="myLogo" width="80" height="80"></a>
                </div>

                <!-- Navbar Links -->
                <div class="navbarLinks">
                    <div class="navLink"><a href="adminwebsite.php">Home</a></div>
                    <div class="navLink"><a href="admin_product-catalog.php">Products</a></div>
                    <div class="navLink"><a href="adminwebsite.php#about">About</a></div>
                    <div class="navLink"><a href="adminwebsite.php#contact">Contact Us</a></div>
                    <div class="navLink"><a href="adminwebsite.php#team">Meet the Team</a></div>
                </div>

                <!-- Right side section (Social Icons, Log Out, Notification) -->
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
                <div class="navbarIcons ms-3">
                    <!--<a href="#"><i class="fa fa-credit-card-alt" aria-hidden="true"></i></a>
                        <a href="/Project in WST/profile.php"><i class="fa fa-user" aria-hidden="true"></i></a>-->

                   <span class="notif-badge" id="notifBadge">0</span>
                    <a href="admin_notifs.php" class="ms-3"><i class="fa fa-bell" aria-hidden="true"></i></a>
                </div>
                
                <!-- Log Out -->
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
                        <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        <div class="card-body text-center">
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>
                            
                            <button class="btn btn-primary" onclick="window.location.href=\'admin-product.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">SEE INFO</button>
                        </div>
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
                        <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        <div class="card-body text-center">
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>
                            
                            <button class="btn btn-primary" onclick="window.location.href=\'admin-product.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">SEE INFO</button>
                        </div>
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
                        <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        <div class="card-body text-center">
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>
                            
                            <button class="btn btn-primary" onclick="window.location.href=\'admin-product.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">SEE INFO</button>
                        </div>
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
                        <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        <div class="card-body text-center">
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>
                            
                            <button class="btn btn-primary" onclick="window.location.href=\'admin-product.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">SEE INFO</button>
                        </div>
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
                        <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="card-img-top">
                        <div class="card-body text-center">
                            <h5 class="card-title">'. htmlspecialchars($row["product_name"]) .'</h5>
                            <p class="card-text">'. htmlspecialchars($row["product_desc"]) .'</p>
                            <p class="card-price">$'. number_format($row["product_price"], 2) .'</p>
                            
                            <button class="btn btn-primary" onclick="window.location.href=\'admin-product.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">SEE INFO</button>
                        </div>
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
function toggleSidebar() {
            const sidebar = document.getElementById("mySidebar");
            if (sidebar.style.left === "-250px") {
                sidebar.style.left = "0";
            } else {
                sidebar.style.left = "-250px";
            }
        }
        // Function to handle scrolling behavior
window.onscroll = function() {
    hideNotifBadgeOnScroll();
};

/*function hideNotifBadgeOnScroll() {
    const navbar = document.querySelector('.navbar');
    const notifBadge = document.getElementById('notifBadge');

    // Check if the navbar is scrolled off the screen
    if (navbar && notifBadge) {
        if (window.scrollY > navbar.offsetHeight) {
            // Hide notification badge when navbar is off-screen
            notifBadge.style.display = 'none';
        } else {
            // Show notification badge when navbar is on-screen
            notifBadge.style.display = 'inline-block';
        }
    }
}*/
     // Smooth scrolling to a specific section
function scrollToSection(sectionId) {
    document.getElementById(sectionId).scrollIntoView({
        behavior: "smooth"
    });
}

// Smooth scrolling to the top of the page
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: "smooth" // Smooth scroll to the top
    });
}
              // Initialize notifCount globally, assuming starting with 0 unread messages
              let notifCount = 0;

function checkForNewMessages() {
    fetch('new_notif.php')
        .then(response => response.json())
        .then(data => {
            console.log("Data received:", data); // Should log {unreadMessagesCount: 1}
            const newCount = data.unreadMessagesCount;
            console.log("New count from server:", newCount); // Log new count from PHP
            console.log("Current notifCount:", notifCount);   // Log current notifCount

            // Update notification badge if there are new unread messages
            if (newCount > notifCount) {
                notifCount = newCount;
                document.getElementById("notifBadge").textContent = notifCount;
                document.getElementById("notifBadge").style.display = "inline-block";
                console.log("Badge updated to:", notifCount); // Confirm badge update
                setInterval(checkForNewMessages, 10000);
            } else if (newCount === 0) {
                document.getElementById("notifBadge").style.display = "none";
                console.log("Badge hidden"); // Confirm badge hiding
            }
        })
        .catch(error => console.error("Fetch error:", error));
}
setTimeout(() => {
    checkForNewMessages();
    // Set up recurring poll every 10 seconds after the first call
    setInterval(checkForNewMessages, 10000);
}, 100);
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
                    <a href="adminwebsite.php"><img src="logo.png" alt="Core Gear Tech Logo" width="20%"></a>
                </div>
                <p style ="color:gray;">© 2024 Core Gear Tech® | All Rights Reserved</p>
            </div>
        </div>
        
        
    </div>
</body>
</html>
