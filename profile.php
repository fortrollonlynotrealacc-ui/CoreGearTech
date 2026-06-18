<?php
session_start();
include '../Project in WST/server/server.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data, including the new fields
$sql = "SELECT fname, mname, lname, email, age, phonenumber, address, avatar FROM cgt_accounts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$avatar_path = $user['avatar'] ? $user['avatar'] : 'avatar.png';

// Fetch previous purchases with quantity and purchase date from the `cgt_user_purchase` table
$purchases_sql = "
    SELECT p.product_id, p.product_name, p.product_price, up.quantity, p.product_image, up.purchase_date, up.order_status
    FROM cgt_user_purchase AS up
    JOIN cgt_products AS p ON up.product_id = p.product_id
    WHERE up.user_id = ?
    ORDER BY up.purchase_date DESC";
    
$purchases_stmt = $conn->prepare($purchases_sql);
$purchases_stmt->bind_param("i", $user_id);
$purchases_stmt->execute();
$purchases_result = $purchases_stmt->get_result();
$purchases = $purchases_result->fetch_all(MYSQLI_ASSOC);
$purchases_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="navbar.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap.bundle.min.js"></script>
    <style>
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
        .video-background {
          position: fixed;
          top: 0;
          left: 0;
          width: 100vw;
          height: 100vh;
          overflow: hidden;
          z-index: -1; /* Lower than other elements */
        }

        #bg-video {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          object-fit: cover; /* Cover the entire viewport */
          z-index: -1; /* Lower than other elements */
        }

        /* Video background overlay */
        .video-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));
            z-index: 1; 
            pointer-events: none;
        }


        .profile-container {
            width: 80%;
            height: 90%; /*px gamitin para gumana*/ 
            margin: 20px auto;
            background-color: rgba(25, 24, 24, 0.9);
            padding: 30px;
            border-radius: 8px;
            color: white;
            display: flex;
            align-items: center;
        }
        .avatar-wrapper {
            margin-right: 20px;
        }
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }
        .info-wrapper {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .labels-container {
             display: flex;
             gap: 144px; 
        }
        .label {
            font-size: 0.8em;
            color: #ccc;
        }
        .input-field {
            border: none;
            background: none;
            color: white;
            font-size: 1.2em;
            font-weight: bold;
            max-width: 200px;
        }

        .input-field:disabled {
            color: white;
        }
        /* New border style for editable fields */
        .editable {
            border: 1px solid #007bff;
            padding: 2px;
            background-color: #333;
        }
        .edit-button {
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
        /* Footer styles */
        .footer {
            background-color: black;
            margin-top:auto;
            color: white;
            padding: 20px 0;
            width: 100%;
            text-align: center;
            position: relative;
 
        }

        .footer-logo {
            margin-bottom: 10px; 
        }
        .footer-contents{
            margin-top: 4%; 
        }
        .footer-contents a {
            color: white;
            text-decoration: none;
            text-align: center;
        }
        /*return button*/
        .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
        .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
        .modal-content {
        background-color: #002244; /* Dark blue background */
        color: #f0f8ff; /* Light blue text */
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
    </script>
</head>
<body>
    <?php include ('usernavbar.php');?>
    <div class="video-container">
        <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>     
    </div>
    <!-- <button class="btn-return" style="margin-top: 5%; color: white;" onclick="window.history.back();">
        <i class="fa fa-reply"></i> 
    </button> -->
    <div class="profile-container" style ="margin-top:8%;">
        <div class="avatar-wrapper">
            <img src="<?= htmlspecialchars($avatar_path); ?>" alt="Profile Avatar" class="avatar" id="avatar-preview">
            <input type="file" id="avatar-input" style="display: none;" onchange="previewAvatar(event)">
        </div>
        <div class="info-wrapper">
            <div class="labels-container">
                <div class="label">Lastname:</div>
                <div class="label">Firstname:</div>
                <div class="label">Middlename:</div>
                <div class="label">Age:</div>
            </div>
            <div>
                <input type="text" id="lname" class="input-field" value="<?= htmlspecialchars($user['lname']); ?>" disabled>
                <input type="text" id="fname" class="input-field" value="<?= htmlspecialchars($user['fname']); ?>" disabled>
                <input type="text" id="mname" class="input-field" value="<?= htmlspecialchars($user['mname']); ?>" disabled>
                <input type="number" id="age" class="input-field" style = "margin-left:10px;" value="<?= htmlspecialchars($user['age']); ?>" disabled>
            </div>
            
            <!-- Address and Phone Number -->
            <div class="labels-container" style="margin-top: 10px;">
                <div class="label">Address:</div>
            </div>
            <div>
                <input type="text" id="address" class="input-field" style = "max-width:100%; width: 200%" value="<?= htmlspecialchars($user['address']); ?>" disabled>
            </div>
            
            <div class="labels-container" style="margin-top: 10px;">
                <div class="label">Phone Number:</div>
            </div>
            <div>
            +63<input type="number" id="phonenumber" class="input-field" pattern="\+63\d{10}" title="Phone number must be +63 followed by exactly 10 digits." style = "max-width:100%;"value="<?= htmlspecialchars($user['phonenumber']); ?>" disabled>
            </div>
            
            <div class="button-container">
    <button class="edit-button" onclick="toggleEdit()">Edit Profile</button>
    <button class="edit-button" id="save-button" style="display: none;" onclick="saveChanges()">Save</button>
    <button class="edit-button" id="cancel-button" style="display: none;" onclick="cancelEdit()">Cancel</button>
</div>

        </div>
    </div>

<!-- PREVIOUS PURCHASE -->
<div class="purchases-container">
    <h3 style="color: white;">Previous Purchases</h3>
    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Purchase Date</th>
                <th>Order Status</th> 
                <th>Feedback Options</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($purchases)) : ?>
                <?php foreach ($purchases as $purchase) : ?>
                    <tr>
                        <td>
                        <a href="user-product copy.php?product_id=<?= htmlspecialchars($purchase['product_id']); ?>">
                                <img src="<?= htmlspecialchars($purchase['product_image']); ?>" alt="Product Image" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                            </a>
                        </td>
                        <td><a style = "text-decoration:none; color:white;"href="user-product copy.php?product_id=<?= htmlspecialchars($purchase['product_id']); ?>">
                            <?= htmlspecialchars($purchase['product_name']); ?></a></td>
                        <td>$<?= htmlspecialchars(number_format($purchase['product_price'], 2)); ?></td>
                        <td><?= htmlspecialchars($purchase['quantity']); ?></td>
                        <td><?= htmlspecialchars($purchase['quantity'] * number_format($purchase['product_price'], 2)); ?></td>
                        <td><?= htmlspecialchars(date("Y-m-d", strtotime($purchase['purchase_date']))); ?></td>
                        <td><?= htmlspecialchars($purchase['order_status']); ?></td>
                        <?php if($purchase['order_status']==='Already Received'): ?>
                        <td>
                         <a href="user-product copy.php?product_id=<?= htmlspecialchars($purchase['product_id']); ?>" class="btn btn-primary">Add Feedback</a>
                        </td>
                        <?php elseif($purchase['order_status']==='Cancelled') : ?>
                            <td>None</td>
                        <?php else : ?>
                            <td>Pending</td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" class="text-center">No previous purchases</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>
<!-- SUCCESS MODAL -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Profile updated successfully!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ERROR MODAL -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="errorMessage">An error occurred. Please try again.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



    <script>
        function previewAvatar(event) {
    const avatarPreview = document.getElementById('avatar-preview');
    const file = event.target.files[0];
    if (file) {
        avatarPreview.src = URL.createObjectURL(file);
    }
}

       function toggleEdit() {
    const inputs = document.querySelectorAll('.input-field');
    const editButton = document.querySelector('.edit-button');
    const saveButton = document.getElementById('save-button');
    const cancelButton = document.getElementById('cancel-button');
    const avatarInput = document.getElementById('avatar-input');

    if (editButton.textContent === 'Edit Profile') {
        // Enable inputs for editing and add editable style
        inputs.forEach(input => {
            input.disabled = false;
            input.classList.add('editable');
            input.dataset.originalValue = input.value; // Store the original value
        });
        avatarInput.style.display = 'block';
        editButton.style.display = 'none'; // Hide Edit button
        saveButton.style.display = 'inline-block'; // Show Save button
        cancelButton.style.display = 'inline-block'; // Show Cancel button
    }
}

function saveChanges() {
    const inputs = document.querySelectorAll('.input-field');
    const saveButton = document.getElementById('save-button');
    const avatarInput = document.getElementById('avatar-input');
    const cancelButton = document.getElementById('cancel-button');
    const editButton = document.querySelector('.edit-button');
    const avatarPreview = document.getElementById('avatar-preview');

    // Validate inputs
    const age = document.getElementById('age').value;
    const phoneNumber = document.getElementById('phonenumber').value;

    if (isNaN(age) || age < 12 || age > 120) {
        showErrorModal('Invalid Age');
        return;
    }

    if (!/^\d{10}$/.test(phoneNumber)) {
        showErrorModal('Phone number must be exactly 10 digits.');
        return;
    }

    // Prepare FormData
    const formData = new FormData();
    inputs.forEach(input => {
        formData.append(input.id, input.value);
    });
    const avatar = avatarInput.files[0];
    if (avatar) formData.append('avatar', avatar);

    // Disable Save button to prevent duplicate submissions
    saveButton.disabled = true;

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

            // Disable inputs and reset UI state
            inputs.forEach(input => {
                input.disabled = true;
                input.classList.remove('editable');
            });
            avatarInput.style.display = 'none';
            editButton.style.display = 'inline-block'; // Show Edit button
            saveButton.style.display = 'none'; // Hide Save button
            cancelButton.style.display = 'none'; // Hide Cancel button

            // Update avatar preview
            if (avatar) {
                const objectURL = URL.createObjectURL(avatar);
                avatarPreview.src = objectURL;

                // Revoke the temporary object URL after a short delay
                setTimeout(() => URL.revokeObjectURL(objectURL), 100);
            }
        } else {
            showErrorModal(data.message || 'Failed to update profile');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal('An error occurred while updating your profile. Please try again.');
    })
    .finally(() => {
        saveButton.disabled = false; // Re-enable Save button
    });
}

function showErrorModal(message) {
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    document.getElementById('errorMessage').textContent = message;
    errorModal.show();
}
function cancelEdit() {
    const inputs = document.querySelectorAll('.input-field');
    const editButton = document.querySelector('.edit-button');
    const saveButton = document.getElementById('save-button');
    const cancelButton = document.getElementById('cancel-button');
    const avatarInput = document.getElementById('avatar-input');

    // Reset the values of the inputs to their original state
    inputs.forEach(input => {
        input.disabled = true;
        input.classList.remove('editable');
        input.value = input.dataset.originalValue; // Restore original value
    });

    avatarInput.style.display = 'none'; // Hide avatar input
    editButton.style.display = 'inline-block'; // Show Edit button
    saveButton.style.display = 'none'; // Hide Save button
    cancelButton.style.display = 'none'; // Hide Cancel button
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
</body>

</html>
