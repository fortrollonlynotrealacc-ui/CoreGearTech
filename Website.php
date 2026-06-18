<?php
session_start();

include '../Project in WST/server/server.php';
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
    .items-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding-left: 0;
    padding-right: 0;
    margin: 0;
   
}

.item {
    width: 22%; /* Adjusted to ensure cards fit in a row */
    margin-bottom: 20px;
   
   
}

.item-image {
    width: 100%;
    height: 100%; /* Ensure the image takes up the full space */
}

.card-body {
    padding: 10px; /* Adjust padding if needed */
}


.card {
    border: none; /* Remove borders */
    box-shadow: none; /* Remove shadows */
}

    .image-wrapper {
    flex: 1;
    max-width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
   
}
.item-descriptions{

    max-width: 460px; /* You can adjust this to a size you want */
    width: 100%;
    padding: 10px;
    box-sizing: border-box; /* Ensures padding is within the element's width */
    height: auto; /* Adjusts the height depending on content */
    overflow: hidden; /* Ensures long content doesn't overflow */
    word-wrap: break-word; /* Breaks long words to wrap */
    white-space: normal; /* Ensures text wraps to the next line */
}

.btn1,.btn2{
    margin-top: 33%;

}
        .navbarLinks {
            display: flex;
            position:absolute;
            margin-right: 250px;
            margin-left: 100px;
            gap: 20px;
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
    display: block; /* Show dropdown when hovering over cart icon or dropdown itself */
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
.suggestions {position: absolute;top: 100%;left: 0;width: 100%;max-height: 300px;overflow-y: auto;background-color: #fff;
                color:black;z-index: 1000;}
    .suggestion-item {display: flex;align-items: center;padding: 10px;cursor: pointer;}
    .suggestion-item img {width: 40px;height: 40px;object-fit: cover;margin-right: 10px;}
    .suggestion-item:hover {background-color: #f0f0f0;}
    
    </style>
</head>
<body>
    <script src="script.js"></script>
    <div class="video-container">
        <div class="video-background"></div>
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
                    <a href="Website.php"><img src="logo.png" alt="myLogo" width="80" height="80"></a>
                </div>

                <!-- Navbar Links -->
                <div class="navbarLinks">
            <div class="navLink"><a href="javascript:void(0);" onclick="scrollToTop()">Home</a></div>
            <div class="navLink"><a href="product-catalog.php">Products</a></div>
            <div class="navLink"><a href="javascript:void(0);" onclick="scrollToSection('about')">About</a></div>
            <div class="navLink"><a href="javascript:void(0);" onclick="scrollToSection('contact')">Contact Us</a></div>
            <div class="navLink"><a href="javascript:void(0);" onclick="scrollToSection('team')">Meet the Team</a></div>
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
                            <span class="cart-badge" id="cart-count" style="display:none;">0</span> 
                        </a>
                        
                        
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
                    <div class="sign-register ms-4">
                        <a href="login.php" class="sign-link me-2">Sign In</a>
                        <a href="register.php" class="sign-link">Register</a>
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
    <?php

$productConfig = include('product-config.php');


?>
<div class="image-container">
    <div class="slider-wrapper">
      
        <?php
        $monitor_id = isset($productConfig['monitor']) ? $productConfig['monitor'] : 10;
        $sql = "SELECT product_id, product_name, product_desc, product_image FROM cgt_products WHERE product_id = $monitor_id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo '
        <div class="items" id="monitor">
            <div class="image-content">
                <h1><p class="item-name">' . htmlspecialchars($row["product_name"]) . '</p></h1>
                <div class="desc-wrapper">
                    <pre class="item-descriptions">' . htmlspecialchars($row["product_desc"]) . '</pre>
                </div>
                <div class="button_con d-flex">
                    <button class="btn1"onclick="window.location.href=\'product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>
                    <button class="btn2" onclick="scrollToSection(\'contact\')">Contact Us</button>
                </div>
            </div>
            <div class="image-wrapper">
                <div class="img-container">
                   <a href="product copy.php?product_id=' . htmlspecialchars($row["product_id"]) . '">
                    <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="item-img">
                </a>
                </div>
            </div>
        </div>';
        ?>

      
        <?php
        $keyboard_id = isset($productConfig['keyboard']) ? $productConfig['keyboard'] : 5;
        $sql = "SELECT product_id, product_name, product_desc, product_image FROM cgt_products WHERE product_id = $keyboard_id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo '
        <div class="items" id="keyboard">
            <div class="image-content">
                <h1><p class="item-name">' . htmlspecialchars($row["product_name"]) . '</p></h1>
                <div class="desc-wrapper">
                    <pre class="item-descriptions">' . htmlspecialchars($row["product_desc"]) . '</pre>
                </div>
                <div class="button_con d-flex">
                    <button class="btn1"onclick="window.location.href=\'product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>
                    <button class="btn2" onclick="scrollToSection(\'contact\')">Contact Us</button>
                </div>
            </div>
            <div class="image-wrapper">
                <div class="img-container">
                    <a href="product copy.php?product_id=' . htmlspecialchars($row["product_id"]) . '">
                    <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="item-img">
                </a>
                </div>
            </div>
        </div>';
        ?>

        
        <?php
        $processor_id = isset($productConfig['processor']) ? $productConfig['processor'] : 3;
        $sql = "SELECT product_id, product_name, product_desc, product_image FROM cgt_products WHERE product_id = $processor_id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo '
        <div class="items" id="processor">
            <div class="image-content">
                <h1><p class="item-name">' . htmlspecialchars($row["product_name"]) . '</p></h1>
                <div class="desc-wrapper">
                    <pre class="item-descriptions">' . htmlspecialchars($row["product_desc"]) . '</pre>
                </div>
                <div class="button_con d-flex">
                    <button class="btn1"onclick="window.location.href=\'product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>
                    <button class="btn2" onclick="scrollToSection(\'contact\')">Contact Us</button>
                </div>
            </div>
            <div class="image-wrapper">
                <div class="img-container">
                    <a href="product copy.php?product_id=' . htmlspecialchars($row["product_id"]) . '">
                    <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="item-img">
                </a>
                </div>
            </div>
        </div>';
        ?>

        
        <?php
        $headset_id = isset($productConfig['headset']) ? $productConfig['headset'] : 7;
        $sql = "SELECT product_id,product_name, product_desc, product_image FROM cgt_products WHERE product_id = $headset_id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo '
        <div class="items" id="headset">
            <div class="image-content">
                <h1><p class="item-name">' . htmlspecialchars($row["product_name"]) . '</p></h1>
                <div class="desc-wrapper">
                    <pre class="item-descriptions">' . htmlspecialchars($row["product_desc"]) . '</pre>
                </div>
                <div class="button_con d-flex">
                    <button class="btn1"onclick="window.location.href=\'product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'\'">Buy Now</button>
                    <button class="btn2" onclick="scrollToSection(\'contact\')">Contact Us</button>
                </div>
            </div>
            <div class="image-wrapper">
                <div class="img-container">
                    <a href="product copy.php?product_id=' . htmlspecialchars($row["product_id"]) . '">
                    <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="item-img">
                </a>
                </div>
            </div>
        </div>';
        ?>
    </div>
</div>
    <script>// Global variables to keep track of states
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

<div id="about">
<!-- Core Gear Tech Description Section -->
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
<?php
$featuredproducts = include('featured-products-config.php');
?>


<section class="new-products py-5">
    <div class="container">
        <hr class="section-divider" style="background-color: #007bff;">
        <h2 class="text-center mb-5">Featured Items</h2>
        <!-- Carousel Container -->
        <div id="newProductsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
          

            <div class="carousel-inner">
            <?php
           
             $featured1 = isset($featuredproducts['featured1']) ? $featuredproducts['featured1'] : 1;
             $sql = "SELECT product_id,product_name, product_desc, product_image FROM cgt_products WHERE product_id = $featured1";
             $result = $conn->query($sql);
             $row = $result->fetch_assoc();
            echo'
                <div class="carousel-item active">
                    <div class="row justify-content-center">
                        <!-- Product Item with HOT Overlay - Only 3 items -->
                        <div class="col-md-4 product-item">
                        <a href="product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                            <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="d-block w-100" style="object-fit: contain; height: 320px;">
                        </a>
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                        ';?>
                        <?php
                        
             $featured2 = isset($featuredproducts['featured2']) ? $featuredproducts['featured2'] : 1;
             $sql = "SELECT product_id, product_name, product_desc, product_image FROM cgt_products WHERE product_id = $featured2";
             $result = $conn->query($sql);
             $row = $result->fetch_assoc();
            echo'
                        
                        <div class="col-md-4 product-item">
                        <a href="product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                           <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="d-block w-100" style="object-fit: contain; height: 320px;">
                        </a>
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                        ';?>
                        <?php
                        
             $featured3 = isset($featuredproducts['featured3']) ? $featuredproducts['featured3'] : 1;
             $sql = "SELECT product_id, product_name, product_desc, product_image FROM cgt_products WHERE product_id = $featured3";
             $result = $conn->query($sql);
             $row = $result->fetch_assoc();
            echo'
                        <div class="col-md-4 product-item">
                        <a href="product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                           <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="d-block w-100" style="object-fit: contain; height: 320px;">
                        </a>
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                        ';?>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row justify-content-center">
                        <!-- Another set of Product Items - Only 3 items -->
                        <?php
                        
                        $featured4 = isset($featuredproducts['featured4']) ? $featuredproducts['featured4'] : 1;
                        $sql = "SELECT product_id, product_name, product_desc, product_image FROM cgt_products WHERE product_id = $featured4";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo'
                        <div class="col-md-4 product-item">
                        <a href="product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                           <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="d-block w-100" style="object-fit: contain; height: 320px;">
                        </a>
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>
                        ';?>
                         <?php
                        
                        $featured5 = isset($featuredproducts['featured5']) ? $featuredproducts['featured5'] : 1;
                        $sql = "SELECT product_id, product_name, product_desc, product_image FROM cgt_products WHERE product_id = $featured5";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo'
                        <div class="col-md-4 product-item">
                             <a href="product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                           <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="d-block w-100" style="object-fit: contain; height: 320px;">
                        </a>
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>';
                        ?>
                         <?php
                        
                        $featured6 = isset($featuredproducts['featured6']) ? $featuredproducts['featured6'] : 1;
                        $sql = "SELECT product_id, product_name, product_desc, product_image FROM cgt_products WHERE product_id = $featured6";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo'
                        <div class="col-md-4 product-item">
                           <a href="product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">
                           <img src="' . htmlspecialchars($row["product_image"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '" class="d-block w-100" style="object-fit: contain; height: 320px;">
                        </a>
                            <div class="hot-overlay">HOT <i class="fa fa-fire" aria-hidden="true"></i></div>
                        </div>';
                        ?>
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
                <div class="row mt-4 items-container">
    <?php
        $sql = "SELECT product_id, product_name, product_desc, product_image, product_price FROM cgt_products ORDER BY RAND() LIMIT 8";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '
                    <div class="col-md-3 mb-4 item" data-id="'. htmlspecialchars($row["product_id"]) .'" data-name="'. htmlspecialchars($row["product_name"]) .'">
                        <div class="card h-100" style="border: none; box-shadow: none;">
                            <img src="'. htmlspecialchars($row["product_image"]) .'" alt="'. htmlspecialchars($row["product_name"]) .'" class="item-image" style="object-fit: contain; height: 320px;">
                            <div class="card-body text-center">
                                <h5 class="item-description">'. htmlspecialchars($row["product_name"]) .'</h5>
                                <div class="info-overlay"><a style ="color:white;text-decoration:none;" href="product copy.php?product_id='. htmlspecialchars($row["product_id"]) .'">More Info</a></div>
                            </div>
                        </div>
                    </div>';
            }
        } else {
            echo "<p>No products found.</p>";
        }
        $conn->close();
    ?>
</div>
                <div class="text-center mt-3">
                    <!-- Wrap button in a link pointing to the catalog page -->
                    <a href="product-catalog.php">
                        <button class="btn btn-primary" style="margin-top: 2%; margin-bottom: 5%;">Shop Now</button>
                    </a>
                </div>  
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
                <h5>Raven Gillian Tan</h5>
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
        <a href="https://www.facebook.com/profile.php?id=61569578181980" target="_blank" class="icon-circle" style="animation: float-left-2 6s ease-in-out infinite;"><i class="fa fa-facebook" aria-hidden="true"></i></a>
        <a href="https://x.com/gear_core33"target="_blank" class="icon-circle" style="animation: float-left-3 7s ease-in-out infinite;"><i class="fa fa-twitter" aria-hidden="true"></i></a>
        <a href="https://www.instagram.com/coregeartech/" target="_blank" class="icon-circle" style="animation: float-left-4 8s ease-in-out infinite;"><i class="fa fa-instagram" aria-hidden="true"></i></a>
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
<form class="contact-form mt-4" id="contactForm" action="save_contactmessage.php" method="POST" style="color: black; background-color: #8cbfc3; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);">
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Your name" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Your email" required>
    </div>
    <div class="mb-3">
        <label for="subject" class="form-label">Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject">
    </div>
    <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Your message" required></textarea>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-success glowing-btn" style="padding: 10px 20px; background-color: #1c475c; border: none; color: white;">Send Message</button>
    </div>
</form>

</div>
    </div>
<!-- CSS for styling icons -->
<style>
.items-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding-left: 0;
    padding-right: 0;
    margin: 0;
   
}

.item {
    width: 22%; /* Adjusted to ensure cards fit in a row */
    margin-bottom: 20px;
   
   
}

.item-image {
    width: 100%;
    height: 100%; /* Ensure the image takes up the full space */
}

.card-body {
    padding: 10px; /* Adjust padding if needed */
}


.card {
    border: none; /* Remove borders */
    box-shadow: none; /* Remove shadows */
}
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
            <a href="Website.php"><img src="logo.png" alt="Core Gear Tech Logo" width="20%"></a>
        </div>
        <p style ="color:gray;">© 2024 Core Gear Tech | All Rights Reserved</p>
    </div>
</div>



  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- Custom Script -->
     <script>
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

// Automatically scroll to a section if the page loads with a hash in the URL
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

// DOMContentLoaded event to ensure the DOM is fully loaded before executing scripts
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-input");
    const searchBtn = document.getElementById("search-btn");
    const suggestionsContainer = document.getElementById("suggestions");

    // Expand search input when search button is clicked
    searchBtn.addEventListener("click", function () {
        searchInput.classList.toggle("show"); // Toggle visibility of the search input
        searchInput.focus(); // Focus on the input when it appears
    });

    // Optional: Hide search input when clicking outside
    document.addEventListener("click", function (event) {
        if (!event.target.closest(".search-container")) {
            searchInput.classList.remove("show");
        }
    });

    // Fetch suggestions as the user types in the search input
    searchInput.addEventListener("input", function () {
        const query = searchInput.value.trim();
        if (!query) {
            suggestionsContainer.innerHTML = ''; // Clear suggestions if input is empty
            return;
        }

        // Fetch suggestions from the backend
        fetch(`search_suggestions.php?query=${encodeURIComponent(query)}`)
            .then((response) => response.json())
            .then((data) => {
                suggestionsContainer.innerHTML = ''; // Clear previous suggestions

                // Populate suggestions
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

    // Handle suggestion item click
    suggestionsContainer.addEventListener("click", function (event) {
        const target = event.target.closest(".suggestion-item");
        if (target) {
            const selectedProductId = target.getAttribute("data-product-id");
            if (selectedProductId) {
                window.location.href = `product copy.php?product_id=${selectedProductId}`;
            }
        }
    });
});

     </script>
</body>
</html>
