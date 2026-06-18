<?php

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
    <style>
        /* Styling for full-screen, side-by-side product page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(25, 24, 24);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .navbar {
            margin-bottom: 5%;
            width: 100%;
        }

        .product-container {
            display: flex;
            margin-top: 5%;
            max-width: 2000px;
            width: 100%;
            background-color: rgb(30, 30, 30);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            height: 90vh;
        }

        .product-image {
            flex: 1;
            max-width: 50%;
            object-fit: contain;
            padding: 10px;
        }

        .product-details {
            flex: 1.5;
            margin-left: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .product-title {
            font-size: 2rem;
            color: #2f5f76;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1.5rem;
            color: #1c475c;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .product-description, .product-features ul, .product-specifications ul {
            font-size: 0.9rem;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .button-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            padding: 8px 16px;
            font-size: 0.9rem;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-add-to-cart, .btn-remove-from-cart {
            background-color: transparent; /* Make background transparent */
        }

        .btn-add-to-cart:hover, .btn-remove-from-cart:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Slight hover effect */
        }

        .btn-buy-now {
            background-color: #1c475c;
        }

        .btn-buy-now:hover {
            background-color: #173a4a;
        }

        .quantity-display {
            font-size: 1.2rem;
            margin: 0 10px;
            color: #ffffff;
        }

        /* Return Button Style */
        .btn-return {
            position: absolute; /* Position it at the upper left */
            top: 20px;
            left: 20px;
            background-color: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .btn-return:hover {
            color: #ccc; /* Change color on hover */
        }
    </style>
</head>
<body>
    
    <!-- Navbar -->
    <div class="navbar bg-dark fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <div class="myLogo">
                <img src="logo.png" alt="myLogo" width="80" height="80">
            </div>

            <!-- Navbar Links -->
            <div class="navbarLinks">
                <div class="navLink"><a href="Website.html">Home</a></div>
                <div class="navLink dropdown">
                    <a href="product-catalog.html" class="dropdown-toggle" id="productsDropdown" aria-expanded="false">Products</a>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a class="dropdown-item" href="#headphones">Headphones</a>
                        <a class="dropdown-item" href="#keyboards">Keyboards</a>
                        <a class="dropdown-item" href="#monitors">Monitors</a>
                        <a class="dropdown-item" href="#processors">Processors</a>
                    </div>
                </div>
                <div class="navLink"><a href="#about">About</a></div>
                <div class="navLink"><a href="#contact">Merchandise</a></div>
                <div class="navLink"><a href="#services">Services</a></div>
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
                    <a href="#"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span class="cart-badge" id="cart-count">0</span></a>
                    <a href="#"><i class="fa fa-user" aria-hidden="true"></i></a>
                </div>

                <!-- Sign Up and Register -->
                <div class="sign-register ms-4">
                    <a href="#signup" class="sign-link me-2">Sign In</a>
                    <a href="#register" class="sign-link">Register</a>
                </div>
            </div>
        </div>
    </div>

    

    <div class="product-container">
        <!-- Return Button -->
    <button class="btn-return" style="margin-top: 5%; color: #173a4a;" onclick="window.history.back();">
        <i class="fa fa-reply"></i> <!-- Return icon -->
    </button>
        <!-- Product Image -->
        <img src="Monitors/m3.png" alt="Product Image" class="product-image">

        <!-- Product Details -->
        <div class="product-details">
            <h1 class="product-title">Product Name</h1>
            <p class="product-price">$199.99</p>

            <div class="product-description">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus feugiat nunc sed libero ullamcorper, ut aliquet lacus vestibulum.</p>
                <p>This product is designed for users who value quality and performance. With a sleek design and advanced features, it meets the needs of the modern consumer.</p>
            </div>

            <div class="product-features">
                <h3>Key Features</h3>
                <ul>
                    <li>High-quality material for durability</li>
                    <li>Innovative design for ease of use</li>
                    <li>Compact and lightweight</li>
                    <li>Multiple color options</li>
                    <li>Energy-efficient and eco-friendly</li>
                </ul>
            </div>

            <div class="product-specifications">
                <h3>Specifications</h3>
                <ul>
                    <li>Dimensions: 10 x 8 x 2 inches</li>
                    <li>Weight: 1.5 pounds</li>
                    <li>Battery Life: Up to 10 hours</li>
                    <li>Material: Aluminum and plastic</li>
                    <li>Warranty: 2 years limited</li>
                </ul>
            </div>

            <!-- Button Container -->
            <div class="button-container">
                <button class="btn btn-remove-from-cart" onclick="changeQuantity(-1)"><i class="fa fa-minus"></i></button>
                <span class="quantity-display" id="quantity-display">0</span>
                <button class="btn btn-add-to-cart" onclick="changeQuantity(1)"><i class="fa fa-plus"></i></button>
                <button class="btn btn-buy-now" onclick="window.location.href='product.html'"><i class="fa fa-credit-card"></i> Buy Now</button>
            </div>
        </div>
    </div>

    <script>
        let quantity = 0;

        function changeQuantity(amount) {
            quantity += amount;
            if (quantity < 0) {
                quantity = 0;
            }
                        // Update the quantity display
                        document.getElementById("quantity-display").innerText = quantity;
            // Update cart count display (optional)
            document.getElementById("cart-count").innerText = quantity;
        }
    </script>
</body>
</html>

        <style>/* Set a fixed height for card images to keep them aligned */
            .card-img-top {
                height: 200px;
                object-fit: contain;
            }
        
            /* Set a minimum height for card body to align buttons */
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
                right: 347px; /* Adjust this value to position the badge horizontally */
                background-color:rgba(64, 135, 148, 0.7);
                color: white;
                border-radius: 50%;
                padding: 1px 5px;
                font-size: 10px;
                display: none;
            }
        
            /* Category Heading */
            .category-heading {
                color: #ffffff;
                padding-top: 20px;
                padding-bottom: 10px;
                font-size: 24px;
                text-align: center;
            }</style>
        <script>
            let cartCount = 0;
        
        // Function to add items to the cart
        function addToCart() {
        cartCount++; // Increment cart count
        document.getElementById('cart-count').innerText = cartCount; // Update cart count in navbar
        
        // Show or hide the cart badge based on cart count
        const cartBadge = document.querySelector('.cart-badge');
        cartBadge.style.display = cartCount > 0 ? 'block' : 'none'; // Show badge if count is more than 0
        }
        // Function to enable live search as the user types
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
