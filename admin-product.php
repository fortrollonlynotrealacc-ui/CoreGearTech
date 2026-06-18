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
    .feedback-item {border: 1px solid #ccc;padding: 15px;margin-bottom: 10px;}
    .admin-reply {background-color: #f9f9f9;border-left: 4px solid #007bff;padding: 10px;margin-top: 10px;font-style: italic;color:black;}
    .reply-btn {background-color: #007bff;color: white;border: none;padding: 5px 10px;margin-top: 10px;cursor: pointer;}
    .reply-form {margin-top: 10px;display: none; }
    .reply-form textarea {width: 100%;height: 50px;margin-bottom: 10px;resize: none;}
    .reply-form button {background-color: #28a745;color: white;border: none;padding: 5px 10px;cursor: pointer;margin-right: 5px;}
    .reply-form button[type="button"] {background-color: #dc3545;}
    .navbarLinks {display: flex;position:absolute;margin-right: 250px;margin-left: 100px;gap: 20px;}
    /*specs style*/ 
    .product-specs {font-size: 1.1rem;color: #ddd;}
    .spec-list {list-style-type: circle; padding-left: 20px;}
    .spec-list li {margin-bottom: 8px; }
    .related-products {margin-top: 20px;}
    .related-products h3 {font-size: 1.5rem;margin-bottom: 10px;color: #ddd;}
    .product-suggestions {display: flex;justify-content: space-between;gap: 15px;}
    .suggestion-product {flex: 1;text-align: center;background-color: #333;padding: 10px;border-radius: 5px;transition: transform 0.2s;}
    .suggestion-product:hover {transform: scale(1.05);}
    .suggestion-product img {max-width: 100%;height: 100px;object-fit: cover;border-radius: 5px;margin-bottom: 10px;}
    .suggestion-product p {color: white;font-size: 1rem;margin: 0;}
    .suggestion-product a {text-decoration: none; color: inherit; }
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
    <script>
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

    </script>
<script src="navbar.js"></script>
    <button class="btn-return" style="margin-top: 5%;" onclick="window.location.href='admin_product-catalog.php';">
        <i class="fa fa-reply"></i> <!-- Return icon -->
    </button>
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
                        <input type="text" id="search-input" class ="search-input" placeholder="Search for products...">
                        <script src="search.js"></script>
                        <div class="suggestions" id="suggestions"></div>
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
                
                <button class="buy-now" onclick="location.href='adminproducts.php'">Edit Product</button>
                </div>

            </div>
        </div>

        <!-- Product Description and Feedback -->
        <div class="product-tabs">
            <div class="product-tab active" id="description-tab" onclick="toggleTab('description')">Description</div>
            <div class="product-tab" id="feedback-tab" onclick="toggleTab('feedback')">Feedback</div>
        </div>

        <div class="tab-content active" id="description">
            <h3>Description</h3>
            <p><?php echo $product['product_desc']; ?></p>
        </div>

        <div class="tab-content" id="feedback">
            <h3>Feedback</h3>

            <!-- Feedback Form
            <div class="feedback-form">
                <form id="feedback-form" method="POST" action="submit_feedback.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>"> 
                    <label for="rating">Rating:</label>
                     <div class="rating">
                         <input type="radio" name="rating" value="1" required> 1
                         <input type="radio" name="rating" value="2"> 2
                         <input type="radio" name="rating" value="3"> 3
                         <input type="radio" name="rating" value="4"> 4
                         <input type="radio" name="rating" value="5"> 5
                     </div>
                    <label for="feedback-message">Your Feedback:</label>
                    <textarea name="feedback_message" id="feedback-message" placeholder="Enter your feedback..." required></textarea>
                    <button type="submit">Submit Feedback</button>
                </form>
            </div> -->

            <!-- Feedback List -->
            <div class="feedback-list">
            
            <?php
    include '../Project in WST/server/server.php'; 

    $product_id = intval($product['product_id']); 

    $sql = "SELECT f.id AS feedback_id, f.rating, f.feedback_message, f.admin_reply, 
            CONCAT(u.fname, ' ', u.lname) AS user_full_name, f.created_at 
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
            echo '<div class="feedback-item" id="feedback-' . $row['feedback_id'] . '">';
            echo '<p><strong>' . htmlspecialchars($row['user_full_name']) . ':</strong> ';
            echo '<span class="rating">' . str_repeat('⭐', $row['rating']) . '</span></p>';
            echo '<p>' . htmlspecialchars($row['feedback_message']) . '</p>';
            
            // Display admin reply if exists
            if (!empty($row['admin_reply'])) {
                echo '<p class="admin-reply"><strong>Admin Reply:</strong> ' . htmlspecialchars($row['admin_reply']) . '</p>';
            }

            // Add Reply button
            echo '<button class="reply-btn" onclick="showReplyForm(' . $row['feedback_id'] . ')">Reply</button>';
            
            // Add hidden reply form
            echo '<form method="POST" action="submit_reply.php" class="reply-form" id="reply-form-' . $row['feedback_id'] . '" style="display: none;">';
            echo '<input type="hidden" name="feedback_id" value="' . $row['feedback_id'] . '">';
            echo '<textarea name="admin_reply" placeholder="Write your reply here..." required></textarea>';
            echo '<button type="submit">Submit</button>';
            echo '<button type="button" onclick="hideReplyForm(' . $row['feedback_id'] . ')">Back</button>';
            echo '</form>';

            // Add Delete button for admin
            echo '<button class="delete-btn" onclick="deleteFeedback(' . $row['feedback_id'] . ')">Delete</button>';

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
                echo '<a href="admin-product.php?product_id=' . $related['product_id'] . '">';
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

    <script>
    function deleteFeedback(feedbackId) {
        // Confirm before deletion
        if (confirm("Are you sure you want to delete this feedback?")) {
            // Send AJAX request to delete feedback
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_feedback.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status == 200) {
                    // If deletion is successful, remove the feedback from the page
                    var feedbackItem = document.getElementById("feedback-" + feedbackId);
                    if (feedbackItem) {
                        feedbackItem.remove();
                    }
                    alert("Feedback deleted successfully.");
                } else {
                    alert("Error deleting feedback.");
                }
            };
            xhr.send("feedback_id=" + feedbackId);
        }
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
                window.location.href = `admin-product.php?product_id=${selectedProductId}`;
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

function showReplyForm(feedbackId) {
    // Hide the Reply button
    const replyButton = document.querySelector(`#feedback-${feedbackId} .reply-btn`);
    replyButton.style.display = 'none';

    // Show the Reply form
    const replyForm = document.getElementById(`reply-form-${feedbackId}`);
    replyForm.style.display = 'block';
}

function hideReplyForm(feedbackId) {
    // Show the Reply button
    const replyButton = document.querySelector(`#feedback-${feedbackId} .reply-btn`);
    replyButton.style.display = 'block';

    // Hide the Reply form
    const replyForm = document.getElementById(`reply-form-${feedbackId}`);
    replyForm.style.display = 'none';
}
    </script>
</body>
</html>
<?php
} else {
    echo "<p>Product not found.</p>";
}
?>
