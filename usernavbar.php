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
        