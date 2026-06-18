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
