<?php
session_start();
include '../Project in WST/server/server.php';
// Check if the logout button was clicked
if (isset($_GET['logout'])) {
    // Unset all session variables and destroy the session
    session_unset();
    session_destroy();
    // Redirect to the login page
    header("Location: /Project in WST/Website.php");
    exit;
}
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

// Query to fetch product details for the selected product
$sql = "SELECT * FROM cgt_products WHERE product_id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();}


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
    <title> Core Gear Tech</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>

    <style>
        html {scroll-behavior: smooth;}
        /* Modal styles */
        #logout-modal {display: none;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.5);z-index: 1000;
          }
          #logout-modal .modal-content {position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);background-color: #0f1d2e;
          color:white;padding: 20px;border-radius: 10px;text-align: center;box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);width: 80%; /* Adjusts the width */
          max-width: 300px; /* Ensures it doesn't get too wide */}
          #logout-modal button {margin: 10px;padding: 10px 20px;border: none;border-radius: 5px;cursor: pointer;font-size: 16px;}
          #logout-modal .confirm {background-color: blue;color: white;}
          #logout-modal .cancel { background-color: gray; color: white;}
        .navbarLinks{gap: 10px;margin-right: 120px}
        /* Suggestions Styling */
    .suggestions {position: absolute;top: 100%;left: 0;width: 100%;max-height: 300px;overflow-y: auto;background-color: #fff;
                color:black;z-index: 1000;}
    .suggestion-item {display: flex;align-items: center;padding: 10px;cursor: pointer;}
    .suggestion-item img {width: 40px;height: 40px;object-fit: cover;margin-right: 10px;}
    .suggestion-item:hover {background-color: #f0f0f0;}
    
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
       margin-right: 250px;
   }

   .navbarIcons {
       margin-left: 20px;
   }

   .navbarLinks a:hover,
   .navbarIcons a:hover {
       color: rgba(64, 135, 148, 0.7); 
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
   /* General body styling */
   body {
       font-family: Arial, sans-serif;
       margin: 0;
       padding: 0;
       background-color: #1c1c1c;
       color: #fff;
   }

   /* Product Container */
   .main-container {
       display: flex;
       flex-direction: column;
       margin: 100px auto 50px auto;
       padding: 20px;
       max-width: 1200px;
       gap: 20px;
   }

   .product-main {
       display: flex;
       gap: 30px;
       align-items: flex-start;
   }

   /* Product Image */
   .product-image {
       flex: 1;
       display: flex;
       justify-content: center;
   }

   .product-image img {
       max-width: 100%;
       height: auto;
       border-radius: 8px;
   }

   /* Product Details */
   .product-content {
       flex: 1.5;
   }

   .product-title {
       font-size: 2.5rem;
       color: #5cdb95;
       margin-bottom: 8px;
   }

   .product-price {
       font-size: 1.8rem;
       color: #f4f4f4;
       font-weight: bold;
       margin-bottom: 12px;
   }

   .product-desc {
       font-size: 1.1rem;
       color: #bbb;
       margin-bottom: 20px;
   }

   .button-container {
       display: flex;
       gap: 15px;
       margin-top: 10px;
   }

   .add-to-cart, .buy-now {
       padding: 12px 20px;
       font-size: 1.1rem;
       color: #fff;
       background-color:  #1c475c;;
       border: none;
       border-radius: 5px;
       cursor: pointer;
   }

   .buy-now {
       background-color:  #1c475c;
   }

   /* Description and Feedback */
   .product-tabs {
       display: flex;
       margin-top: 30px;
       border-bottom: 2px solid #3d3d3d;
   }

   .product-tab {
       display: inline-block;
       padding: 15px;
       cursor: pointer;
       font-size: 1.2rem;
       color: #fff;
       background-color: #3d3d3d;
       border-radius: 5px 5px 0 0;
   }

   .product-tab:hover {
       background-color:  #1c475c;
   }

   .product-tab.active {
       background-color:  #1c475c;;
   }

   .tab-content {
       display: none;
       margin-top: 15px;
       padding: 20px;
       background-color: #2c2c2c;
       border-radius: 8px;
   }

   .tab-content.active {
       display: block;
   }

   /* Feedback Form */
   .feedback-form {
       background-color: #2c2c2c;
       border-radius: 8px;
       padding: 20px;
       margin-top: 20px;
   }

   .feedback-form label {
       color: #fff;
       font-size: 1.1rem;
       margin-bottom: 5px;
   }

   .feedback-form textarea {
       width: 100%;
       height: 100px;
       margin-top: 10px;
       padding: 10px;
       border-radius: 5px;
       border: 1px solid #555;
       background-color: #444;
       color: #fff;
   }

   .feedback-form .rating {
       margin-top: 10px;
   }

   .feedback-form .rating input {
       margin-right: 10px;
       cursor: pointer;
   }

   .feedback-form button {
       margin-top: 15px;
       padding: 10px 15px;
       background-color: #1c475c;
       color: #fff;
       border: none;
       border-radius: 5px;
       cursor: pointer;
   }

   /* Displaying Feedback */
   .feedback-list {
       margin-top: 30px;
   }

   .feedback-item {
       background-color: #333;
       padding: 15px;
       border-radius: 8px;
       margin-bottom: 15px;
   }

   .feedback-item p {
       margin: 5px 0;
   }

   .feedback-item .rating {
       color: #f4b400;
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
       right: 413px; /* Adjust this value to position the badge horizontally */
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
    </style>
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


document.addEventListener("DOMContentLoaded", function () {
    const cartIcon = document.getElementById('cart-icon');
    const cartDropdown = document.getElementById('cart-dropdown');

    let isMouseInsideDropdown = false;
    let cartCloseTimeout;

    function showCartDropdown() {
        cartDropdown.style.display = 'block';
        clearTimeout(cartCloseTimeout);
    }

    function hideCartDropdown() {
        cartCloseTimeout = setTimeout(() => {
            if (!isMouseInsideDropdown) {
                cartDropdown.style.display = 'none';
            }
        }, 2000);
    }

    cartIcon.addEventListener('mouseenter', showCartDropdown);
    cartIcon.addEventListener('mouseleave', hideCartDropdown);

    cartDropdown.addEventListener('mouseenter', () => {
        isMouseInsideDropdown = true;
        clearTimeout(cartCloseTimeout);
    });

    cartDropdown.addEventListener('mouseleave', () => {
        isMouseInsideDropdown = false;
        hideCartDropdown();
    });
});


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
    <script src="script.js"></script>
    <script src="navbar.js"></script>
    <div class="video-container">
        <div class="video-background"> </div>
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
    </div>
   

    <!-- Scrollable content (starting after the video) -->
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
            <div class="navLink"><a href="javascript:void(0);" onclick="scrollToTop()">Home</a></div>
            <div class="navLink"><a href="userproduct-catalog.php">Products</a></div>
            <div class="navLink"><a href="javascript:void(0);" onclick="scrollToSection('about')">About</a></div>
            <div class="navLink"><a href="javascript:void(0);" onclick="scrollToSection('contact')">Contact Us</a></div>
            <div class="navLink"><a href="javascript:void(0);" onclick="scrollToSection('team')">Meet the Team</a></div>
        </div>

        <!-- Right side section (Search, Social Icons, Log Out, Notification) -->

    <!-- Search Box -->
                    <div class="search-container">
                        <div class="search-box">
                        <input type="text" id="search-input" class ="search-input" placeholder="Search for products...">
                        <div class="suggestions" id="suggestions"></div>
                            <button class="search-btn" id="search-btn">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <script src="search.js"></script>
    <!-- Social Media Icons -->
    <div class="navbarIcons ms-3">
                        <a href="usercart_items.php" class="cart-icon" id="cart-icon">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <span class="cart-badge" id="cart-count">0</span>
                        </a>
        <a href="/Project in WST/profile.php"><i class="fa fa-user" aria-hidden="true"></i></a>
        <a href="message.php" class="ms-3"><i class="fa fa-comment" aria-hidden="true"></i></a>
    
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
<!--</div>ERE TATANGGALIN MO PAG BABALIK MO SA DATING NAVBAR -->

    <!-- Log Out -->
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
<!--Cover-->

    <!-- Radio Buttons (Center) -->
    <div class="radio-buttons">
        <input type="radio" id="monitorRadio" name="itemRadio" value="monitor" onclick="scrollToItem('monitor')">
        <label for="monitorRadio"></label> <!-- Empty label to remove text -->

        <input type="radio" id="keyboardRadio" name="itemRadio" value="keyboard" onclick="scrollToItem('keyboard')">
        <label for="keyboardRadio"></label>

        <input type="radio" id="processorRadio" name="itemRadio" value="processor" onclick="scrollToItem('processor')">
        <label for="processorRadio"></label>

        <input type="radio" id="headsetRadio" name="itemRadio" value="headset" onclick="scrollToItem('headset')">
        <label for="headsetRadio"></label>
    </div>

    <!-- Image Slider -->
    
    <div class="image-container">
        <div class="slider-wrapper">
            <!-- Monitor -->
            <div class="items" id="monitor">
                <div class="image-content">
                    <h1><p class="item-name">Monitor</p></h1>
                    <pre class="item-descriptions">High-definition monitor with vibrant colors.</pre> 
                    <div class = "button_con d-flex">
                        <button class="btn1">Buy Now</button>
                        <button class="btn2">Contact Us</button>
                    </div>
                    
                </div>
                <div class="image-wrapper">
                    <div class="img">
                        <img src="m11.png" alt="Monitor" class="item-img">  
                    </div>
                </div>
            </div>

            <!-- Keyboard -->
            <div class="items" id="keyboard">
                <div class="image-content">
                    <h1><p class="item-name">Keyboard</p></h1>
                    <pre class="item-descriptions">Mechanical keyboard for improved typing.</pre> 
                    <div class = "button_con d-flex">
                        <button class="btn1">Buy Now</button>
                        <button class="btn2">Contact Us</button>
                    </div>
                </div>
                <div class="image-wrapper">
                    <div class="img">
                        <img src="k44.png" alt="Keyboard" class="item-img">  
                    </div>
                </div>
            </div>

            <!-- Processor -->
            <div class="items" id="processor">
                <div class="image-content">
                    <h1><p class="item-name">Processor</p></h1>
                    <pre class="item-descriptions">Fast processor for seamless performance.</pre> 
                    <div class = "button_con d-flex">
                        <button class="btn1">Buy Now</button>
                        <button class="btn2">Contact Us</button>
                    </div>
                </div>
                <div class="image-wrapper">
                    <div class="img">
                        <img src="p11.png" alt="Processor" class="item-img">  
                    </div>
                </div>
            </div>

             <!-- Headset -->
             <div class="items" id="headset">
                <div class="image-content">
                    <h1><p class="item-name">Headset</p></h1>
                    <pre class="item-descriptions">Headset for good gaming experience.</pre> 
                    <div class = "button_con d-flex">
                        <button class="btn1">Buy Now</button>
                        <button class="btn2">Contact Us</button>
                    </div>
                </div>
                <div class="image-wrapper">
                    <div class="img">
                        <img src="Headset/h3.png" alt="Headset" class="item-img">  
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
    function scrollToSection(sectionId) {
    document.getElementById(sectionId).scrollIntoView({
      behavior: "smooth"
    });
  }

  function scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: "smooth" // Smooth scroll to the top
    });
  }

  if (window.location.hash) {
            const targetId = window.location.hash.substring(1); // Remove the '#' from the hash
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        }

    
    
    // Global variables to keep track of states
        let currentSlideIndex = 0;
        let slideInterval;
        let lastScrollTop = 0;
        
        // Function to handle navbar scroll behavior
        function handleNavbarScroll() {
            const navbar = document.querySelector('.navbar'); // Select the navbar
            window.addEventListener('scroll', function () {
                let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        
                if (currentScroll > lastScrollTop) {
                    navbar.style.top = "-80px"; // Hide the navbar (adjust based on your navbar height)
                } else {
                    navbar.style.top = "0";
                }
        
                lastScrollTop = Math.max(currentScroll, 0);
            });
        }
        
        // Function to handle the item/carousel behavior
        function handleCarouselBehavior() {
            // Define the custom order for the slides
            const slideOrder = [0, 1, 2, 3, 2, 1, 0];
        
            // Function to show an item by changing visibility
            function showSlide(itemId) {
                const items = document.querySelectorAll('.items');
                items.forEach(item => {
                    item.classList.remove('active'); // Hide all slides
                });
        
                const element = document.getElementById(itemId);
                if (element) {
                    element.classList.add('active'); // Show the target slide
                    checkRadioButton(itemId); // Update radio button
                } else {
                    console.log("Item with ID '" + itemId + "' not found.");
                }
            }
        
            // Function to check the appropriate radio button based on the current visible item
            function checkRadioButton(itemId) {
                var radios = document.querySelectorAll('input[name="itemRadio"]');
                radios.forEach(radio => {
                    radio.checked = false;
                });
        
                var radioToCheck = document.getElementById(itemId + 'Radio');
                if (radioToCheck) {
                    radioToCheck.checked = true;
                }
            }
        
            // Function to automatically go to the next slide every 3 seconds, following custom order
            function autoSlide() {
                const items = document.querySelectorAll('.items');
                slideInterval = setInterval(() => {
                    currentSlideIndex = (currentSlideIndex + 1) % slideOrder.length; // Cycle through the custom order
                    const nextItemId = items[slideOrder[currentSlideIndex]].id;
                    showSlide(nextItemId);
                }, 3000); // 3000 milliseconds = 3 seconds
            }
        
            // Function to reset the slide interval when a radio button is clicked
            function resetSlideInterval() {
                clearInterval(slideInterval); // Clear existing interval
                autoSlide(); // Restart the interval
            }
        
            // Add click listeners to all radio buttons to reset the interval and change the slide
            function addRadioClickListeners() {
                const radios = document.querySelectorAll('input[name="itemRadio"]');
                radios.forEach((radio, index) => {
                    radio.addEventListener('click', () => {
                        currentSlideIndex = slideOrder.indexOf(index);
                        const nextItemId = radio.value;
                        showSlide(nextItemId);
                        resetSlideInterval(); // Reset the timer when a radio is clicked
                    });
                });
            }
        
            addRadioClickListeners(); // Add listeners to radio buttons
            autoSlide(); // Start the automatic sliding
            showSlide('monitor'); // Initially show the first slide (or any default)
        }
        
        // Function to initialize both behaviors when the document is loaded
        document.addEventListener('DOMContentLoaded', (event) => {
            handleNavbarScroll(); // Initialize navbar scroll behavior
            handleCarouselBehavior(); // Initialize carousel/item behavior
        });
        

        </script>


<!-- Core Gear Tech Description Section -->
 <div id="about">

<div class="core-gear-tech-description1">
    <div class="core-gear-tech-description">
        <div class="text-overlay"> <!-- Dark overlay box -->
            <h2 class="description-title">About Core Gear Tech</h2>
            <p>
                Core Gear Tech is dedicated to providing high-quality computer hardware and accessories tailored for gamers and tech enthusiasts alike. We believe in offering products that enhance performance and bring innovation to your gaming and productivity experience. Our carefully curated selection includes the latest monitors, processors, headphones, keyboards, and more, ensuring that you have the best tools at your disposal.
            </p>
            <p>
                With a commitment to excellence, we source our products from the most reputable manufacturers in the industry. Our knowledgeable team is always ready to assist you in finding the perfect components to build or upgrade your gaming rig.
            </p>
            <p>
                Join us in exploring a world of cutting-edge technology designed to meet your needs. Whether you're a casual gamer or a seasoned pro, Core Gear Tech has something for everyone. Stay ahead of the curve with our innovative products and unmatched customer service!
            </p>
        </div>
    </div>
    <div class="image-overlay-container">
        <img src="pc1.jpg" alt="Full PC Setup" class="fullset-image">
    </div>
</div>
</div>

<!-- New Products Carousel Section -->
<section class="new-products py-5">
    <div class="container">
        <hr class="section-divider" style="background-color: #007bff;">
        <h2 class="text-center mb-5">Featured Items</h2>
        <!-- Carousel Container -->
        <div id="newProductsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">

            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row justify-content-center">
                        <!-- Product Item with HOT Overlay - Only 3 items -->
                        <div class="col-md-4 product-item">
                            <img src="Headset/h2.png" class="d-block w-100" alt="Product Image">
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                        <div class="col-md-4 product-item">
                            <img src="Keyboards/k2.png" class="d-block w-100" alt="Product Image">
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                        <div class="col-md-4 product-item">
                            <img src="Monitors/m4.png" class="d-block w-100" alt="Product Image">
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <!-- Another set of Product Items - Only 3 items -->
                        <div class="col-md-4 product-item">
                            <img src="Processor/p5.png" class="d-block w-100" alt="Product Image">
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                        <div class="col-md-4 product-item">
                            <img src="Keyboards/k4.png" class="d-block w-100" alt="Product Image">
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                        <div class="col-md-4 product-item">
                            <img src="Headset/h5.png" class="d-block w-100" alt="Product Image">
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#newProductsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#newProductsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <hr class="section-divider" style="background-color: #007bff;">
    </div>
</section>

            <!-- Items Section -->
            <div class="items-section1">
                <div class="video-container1">
                    <div class="video-background1"></div>
                    <video autoplay muted loop id="bg-video1">
                        <source src="products_bg.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    
                </div>
                
                <h2 class="items-title" style="text-align: center; padding-top: 5%;">Our Products</h2>
                <div class="items-container">
                    <div class="item">
                        <img src="Monitors/m1.png" alt="Monitor" class="item-img">
                        <div class="info-overlay">More Info</div>
                        <p class="item-description">High-definition monitor with vibrant colors.</p>
                    </div>
                    <div class="item">
                        <img src="Processor/p1.png" alt="Processor" class="item-img">
                        <div class="info-overlay">More Info</div>
                        <p class="item-description">Fast processor for seamless performance.</p>
                    </div>
                    <div class="item">
                        <img src="Headset/h2.png" alt="Headphones" class="item-img">
                        <div class="info-overlay">More Info</div>
                        <p class="item-description">Noise-canceling headphones for a better experience.</p>
                    </div>
                    <div class="item">
                        <img src="Keyboards/k5.png" alt="Keyboard" class="item-img">
                        <div class="info-overlay">More Info</div>
                        <p class="item-description">Mechanical keyboard for improved typing.</p>
                    </div>
                    <div class="item">
                        <img src="Monitors/m4.png" alt="Monitor" class="item-img">
                        <div class="info-overlay">More Info</div>
                        <p class="item-description">Ultra-wide monitor for multitasking.</p>
                    </div>
                    <div class="item">
                        <img src="Processor/p5.png" alt="Processor" class="item-img">
                        <div class="info-overlay">More Info</div>
                        <p class="item-description">High-performance processor for gaming.</p>
                    </div>
                    <div class="item">
                        <img src="Headset/h3.png" alt="Headphones" class="item-img">
                        <div class="info-overlay">More Info</div>
                        <p class="item-description">Wireless headphones with long battery life.</p>
                    </div>
                    <div class="item">
                        <img src="Keyboards/k4.png" alt="Keyboard" class="item-img">
                        <div class="info-overlay">More Info</div>
                        <p class="item-description">RGB keyboard for a stylish look.</p>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <!-- Wrap button in a link pointing to the catalog page -->
                    <a href="product-catalog.php">
                        <button class="btn btn-primary" style="margin-top: 2%; margin-bottom: 5%;">Shop Now</button>
                    </a>
                </div>  
          
<!--Services-->


<!-- Meet The Team -->
<div id="team">
<div class="core-gear-tech-description1">
    <hr class="section-divider" style="background-color: #007bff;"> <!-- Decorative line -->
    <h2 class="description-title">Meet the Team</h2>
    <p>
      At Core Gear Tech, our team consists of passionate IT students dedicated to merging technology and innovation. We are a group of tech enthusiasts who understand the needs of gamers and professionals alike. Our collective expertise drives us to curate high-quality computer hardware and accessories designed to enhance your gaming and productivity experiences. From the latest monitors to powerful processors and essential peripherals, we strive to provide the best tools for your success. Join us as we explore cutting-edge technology and share our journey in the ever-evolving world of IT!
    </p>
   
    <div class="team-members">
        <div class="team-member">
            <img src="zil.jpg" alt="Team Member 1">
            <div class="member-info">
                <h5>Zildian Benedict Tablan</h5>
                <p>Back-End Developer.</p>
            </div>
        </div>
        <div class="team-member">
            <img src="jc.jpg" alt="Team Member 2">
            <div class="member-info">
                <h5>Justfer Carabuena</h5>
                <p>Full Stack Developer <br> 
                    and Team Leader</p>
            </div>
        </div>
        <div class="team-member">
            <img src="tan.jpg" alt="Team Member 3">
            <div class="member-info">
                <h5>Gillian Raven Tan</h5>
                <p>Front-End Developer</p>
            </div>
        </div>
        <div class="team-member">
            <img src="tamayo.jpg" alt="Team Member 4">
            <div class="member-info">
                <h5>Joshua Tamayo</h5>
                <p>Front-End Developer</p>
            </div>
        </div>
    </div>

    <hr class="section-divider" style="background-color: #007bff;"> <!-- Decorative line below team members -->
</div>
</div>
    </div>


<!-- Contact Form -->
<!--<div class="video-container">
    <div class="video-background">
    <video autoplay muted loop id="bg-video">
        <source src="videobg.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    
</div>-->
<div id="contact">
<div class="contact-form-container" style="position: relative; background-color: #2f5f76; padding: 20px; border-radius: 10px; max-width: 600px; margin: 50px auto; color: rgb(209, 239, 252); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);">
    
    <!-- Left floating logos -->
    <div class="floating-logo-left" style="position: absolute; left: -150px; top: 45%; transform: translateY(-50%); display: flex; flex-direction: column; gap: 50px;">
        <a href="#" class="icon-circle" style="animation: float-left-1 5s ease-in-out infinite;"><i class="fa fa-envelope" aria-hidden="true"></i></a>
        <a href="https://www.facebook.com/zildian.tablan" class="icon-circle" style="animation: float-left-2 6s ease-in-out infinite;"><i class="fa fa-facebook" aria-hidden="true"></i></a>
        <a href="#" class="icon-circle" style="animation: float-left-3 7s ease-in-out infinite;"><i class="fa fa-twitter" aria-hidden="true"></i></a>
        <a href="#" class="icon-circle" style="animation: float-left-4 8s ease-in-out infinite;"><i class="fa fa-instagram" aria-hidden="true"></i></a>
    </div>

    <!-- Right floating logos -->
    <div class="floating-logo-right" style="position: absolute; right: -150px; top: 45%; transform: translateY(-50%); display: flex; flex-direction: column; gap: 40px;">
        <a href="#" class="icon-circle" style="animation: float-right-1 7s ease-in-out infinite;"><i class="fa fa-home" aria-hidden="true"></i></a>
        <a href="#" class="icon-circle" style="animation: float-right-2 6.5s ease-in-out infinite;"><i class="fa fa-globe" aria-hidden="true"></i></a>
        <a href="#" class="icon-circle" style="animation: float-right-3 5.5s ease-in-out infinite;"><i class="fa fa-phone" aria-hidden="true"></i></a>
        <a href="#" class="icon-circle" style="animation: float-right-4 8s ease-in-out infinite;"><i class="fa fa-comment" aria-hidden="true"></i></a>
    </div>

    <!-- Decorative horizontal lines -->
    <div style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); height: 2px; width: 100%; background-color: rgba(255, 255, 255, 0.2); z-index: -1;"></div>

    <!-- Contact form content -->
    <h2 class="description-title text-center" style="margin-bottom: 20px;">Contact Us</h2>
    <form class="contact-form mt-4" id="contactForm" style="color: black; background-color: #8cbfc3; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Your name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="Your email" required>
        </div>
        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" placeholder="Subject">
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" rows="4" placeholder="Your message" required></textarea>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-success glowing-btn" style="padding: 10px 20px; background-color: #1c475c; border: none; color: white;">Send Message</button>
        </div>
    </form>
</div>
</div>

<!-- CSS for styling icons -->
<style>
.icon-circle {
    width: 100px;
    height: 100px;
    background-color: #1c475c;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 8px 16px rgba(3, 31, 41, 0.4);  /* Stronger shadow */
    font-size: 45px;  /* Larger icon */
    color: white;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.icon-circle:hover {
    transform: scale(1.2);  /* Increased hover effect */
    box-shadow: 0 12px 24px rgba(27, 81, 115, 0.6);  /* Deeper hover shadow */
}

/* Floating animations with more subtle movement */
@keyframes float-left-1 {
    0% { transform: translate(0, 0); }
    50% { transform: translate(10px, -10px); } /* Subtle Vertical + Horizontal */
    100% { transform: translate(0, 0); }
}

@keyframes float-left-2 {
    0% { transform: translate(0, 0); }
    50% { transform: translate(-10px, 10px); } /* Subtle Vertical + Horizontal */
    100% { transform: translate(0, 0); }
}

@keyframes float-left-3 {
    0% { transform: translate(0, 0); }
    50% { transform: translate(7px, -7px); } /* Subtle Vertical + Horizontal */
    100% { transform: translate(0, 0); }
}

@keyframes float-left-4 {
    0% { transform: translate(0, 0); }
    50% { transform: translate(-7px, 7px); } /* Subtle Vertical + Horizontal */
    100% { transform: translate(0, 0); }
}

@keyframes float-right-1 {
    0% { transform: translate(0, 0); }
    50% { transform: translate(10px, 10px); } /* Subtle Vertical + Horizontal */
    100% { transform: translate(0, 0); }
}

@keyframes float-right-2 {
    0% { transform: translate(0, 0); }
    50% { transform: translate(-10px, -10px); } /* Subtle Vertical + Horizontal */
    100% { transform: translate(0, 0); }
}

@keyframes float-right-3 {
    0% { transform: translate(0, 0); }
    50% { transform: translate(7px, 7px); } /* Subtle Vertical + Horizontal */
    100% { transform: translate(0, 0); }
}

@keyframes float-right-4 {
    0% { transform: translate(0, 0); }
    50% { transform: translate(-7px, -7px); } /* Subtle Vertical + Horizontal */
    100% { transform: translate(0, 0); }
}

.icon-circle::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    top: 0;
    left: 0;
    z-index: -1;
    box-shadow: 0 0 15px rgba(61, 91, 104, 0.6);
    animation: pulse 3s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        opacity: 0.7;
    }
    50% {
        transform: scale(1.1);
        opacity: 1;
    }
    100% {
        transform: scale(0.95);
        opacity: 0.7;
    }
}

</style>





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
    </div>
</div>



  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- Custom Script -->
</body>
</html>
