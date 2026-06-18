<?php
session_start();
include '../Project in WST/server/server.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="navbar.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap.bundle.min.js"></script>
    <title>Chat System</title>
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0% !important;
        }
        #chatbox {
            width: 100%;
            max-width: 600px;
            margin: auto;
            border: 1px solid #007bff;
            margin-top: 30px;
            padding: 10px;
            border-radius: 8px;
            background-color: rgba(25, 24, 24, 0.9);
        }

        #chat-messages {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #007bff;
            margin-bottom: 10px;
            background-color: #2c2c2c;
        }

        .user-message {
            background-color:darkblue;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            text-align: left;
        }

        .admin-message {
            background-color: #1A1A1A;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            text-align: right;
        }

        #chat-input {
            width: 100%;
            height: 50px;
            margin: 10px 0;
            padding: 10px;
            background-color: #333;
            border: 1px solid #007bff;
            color: white;
            font-size: 1em;
        }

        .b1 {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .b1:hover {
            background-color: #0056b3;
        }

        .footer {
            background-color: black;
            margin-top: 20px;
            color: white;
            padding: 50px 0;
            width: 100%;
            text-align: center;
            position: relative;
        }
        .footer-logo {
            margin-bottom: 10px;
        }
        .footer-contents a {
            color: white;
            text-decoration: none;
            text-align: center;
        }
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
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
    .admin-reply {border-left: 4px solid #007bff;padding: 10px;margin-top: 10px;font-style: italic;color:white;}
    .navbarLinks {display: flex;position:absolute;margin-right: 250px;margin-left: 100px;gap: 20px;}
    .cart-badge {position: absolute;top: 29px; right: 362px;background-color:rgba(64, 135, 148, 0.7);
       color: white;border-radius: 50%;padding: 1px 5px;font-size: 10px;display: none;}
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
    </script>
</head>
<body>
    <?php include ('usernavbar.php') ?>
    <h1 style ="text-align:center; margin-top:6%;">Message to admin</h1>
<div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <!-- <button class="btn-return" style="margin-top: 5%; color: white;" onclick="window.history.back();">
        <i class="fa fa-reply"></i>
    </button> -->
<div id="chatbox">
    <div id="chat-messages"></div>
    <form id="chat-form">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>"> <!-- Hidden input for user ID -->
        <textarea id="chat-input" name="message" placeholder="Type your message..."></textarea>
        <input type="hidden" name="message_type" value="user"> <!-- Always user when sending -->
        <button type="submit" class = "b1">Send</button>
    </form>
</div>
<?php include 'userfooter.php'?>
<script>
    document.getElementById('chat-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = document.getElementById('chat-input').value;

        if (message.trim() === '') {
            alert('Message cannot be empty');
            return;
        }

        // Send the message
        const response = await fetch('send_message.php', {
            method: 'POST',
            body: new URLSearchParams({
                user_id: document.querySelector('input[name="user_id"]').value,
                message: message,
                message_type: 'user',
            }),
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById('chat-input').value = '';
            loadMessages();
        } else {
            alert(result.error);
        }
    });

    // Load messages
    async function loadMessages() {
        const response = await fetch('get_messages.php');
        const messages = await response.json();
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.innerHTML = messages.map(msg => `
            <div class="${msg.message_type === 'admin' ? 'admin-message' : 'user-message'}">
                <p>${msg.message_type === 'admin' ? '<strong>Admin Reply:</strong> ' : ''}${msg.message}</p>
                <span>${msg.created_at}</span>
            </div>
        `).join('');
    }

    // Initial load
    loadMessages();
    setInterval(loadMessages, 5000); // Refresh every 5 seconds


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
