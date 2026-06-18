<?php
session_start();
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); // Clear the error message after displaying
$register_message = isset($_SESSION['register_message']) ? $_SESSION['register_message'] : '';
unset($_SESSION['register_message']); // Clear the error message after displaying
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="bootstrap.min.css"> <!-- Bootstrap for responsiveness -->
    <style>
        
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


        /* Styling for the form */
        body, html {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

/* Flexbox layout for the main container */
.scrollable.container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* Allow .content to grow and push footer down */
.content {
    flex: 1; /* Fills remaining space */
    display: flex;
    justify-content: center;
    align-items: center;
}
.registration-container {
    width: 80%;
    max-width: 700px;
    background-color: rgba(25, 24, 24, 0.9);
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.logo-title-container {
    display: flex;
    align-items: center; /* Vertically aligns logo and title */
    gap: 10px; /* Decrease spacing between logo and title */
}

.logo img {
    width: 110px; /* Increased width for a larger logo */
}

.logo-title-container {
    display: flex;
    align-items: center;
    justify-content: space-between; /* Positions logo on left and centers title */
    width: 100%; /* Full width to allow centering */
}

.logo {
    flex-shrink: 0; /* Keeps the logo on the left */
}

.form-title {
    flex-grow: 1; /* Allows title to expand and center */
    margin-left:10%;
    font-size: 30px;
    font-weight: bold;
    color: #007bff;
    line-height: 1.2;
}


        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: white;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
        }

        /* Layout adjustments for better width usage */
        .row .col {
            padding: 0 10px;
        }

        .captcha-container {
            display: flex;
            align-items: center;
        }
        .captcha-text {
         display: flex;
         gap: 10px; /* Increase spacing between letters */
         background-color: #f2f2f2;
         padding: 10px 15px;
         border-radius: 4px;
         justify-content: center;
        }

        .captcha-letter {
        font-size: 24px;
        color: #333;
        }

        .bold {
        font-weight: bold;
        }

        .thin {
        font-weight: 300;
        }

        .blurred {
        filter: blur(1px);
        }

        .rotated {
        transform: rotate(10deg);
        }



        .submit-btn {
            width: 50%;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-left:25%;
        }

        .submit-btn:hover {
            background-color: #0056b3;
            
        }
        .loginpath a{
            color: white;
            margin-left: 35%;
            text-decoration:none;
        }
        .loginpath a:hover{
            color: rgb(20, 80, 112);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            color: #fff;
            background-color: rgba(25, 24, 24, 0.9);
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
        }

        .modal-button {
            background-color: blue;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            text-align: center;
            display: block;
            margin: 0 auto;
        }

        .modal-button:hover {
            background-color: darkblue;
        }

/* Footer styles */
.footer {
    background-color: black;
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
/* Unified message container */
.message-container {
    text-align: center;
    font-weight: bold;
    margin-top: 3px;
    margin-bottom: 3px;
    height: 20px; /* Reserve space for the message */
    visibility: hidden; /* Hide initially but keep space */
}

/* Show message */
.message-container.show {
    visibility: visible;
}

/* Error message style */
.message-container .error {
    color: red;
}

/* Success message style */
.message-container .success {
    color: forestgreen;
}

    </style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    var messageContainer = document.getElementById("messageContainer");
    if (messageContainer && messageContainer.textContent.trim() !== "") {
        messageContainer.classList.add("show");
        setTimeout(function () {
            messageContainer.classList.remove("show");
        }, 3000);
    }
});

    function generateCaptcha() {
            const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            let captchaContainer = document.getElementById("captchaText");
            captchaContainer.innerHTML = ''; // Clear previous CAPTCHA
            const effects = ["bold", "thin", "blurred", "rotated"];

            // Create a 6-character CAPTCHA
            for (let i = 0; i < 6; i++) {
                let char = chars.charAt(Math.floor(Math.random() * chars.length));
                let span = document.createElement("span");
                span.classList.add("captcha-letter");
                let randomEffect = effects[i % effects.length]; // Cycle through effects for variety
                span.classList.add(randomEffect);
                span.textContent = char;
                captchaContainer.appendChild(span);
            }
        }

        function validateCaptcha() {
            const captchaContainer = document.getElementById("captchaText").textContent;
            const userCaptchaInput = document.getElementById("captchaInput").value;

            if (userCaptchaInput !== captchaContainer) {
                alert("Incorrect CAPTCHA. Please try again.");
                generateCaptcha(); // Refresh CAPTCHA
                return false; // Prevent form submission
            }
            return true;
        }

        window.onload = generateCaptcha;

   window.onload = generateCaptcha;

</script>
</head>
<body>
<div class = "scrollable container">
<div class="video-container">
        <div class="video-background"></div>
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
</div>
<main class = "content">
    <div class="registration-container">
    <div class="logo-title-container">
        <div class="logo">
            <a href="Website.php"><img src="logo.png" alt="Core Gear Tech Logo"></a>
        </div>
        <h2 class="form-title">Register an Account</h2>
    </div>
    <div class="message-container" id="messageContainer">
    <?php 
    if ($error_message) {
        echo "<span class='error'>$error_message</span>";
    } elseif ($register_message) {
        echo "<span class='success'>$register_message</span>";
    }
    ?>
    </div>


        <form onsubmit="return validateCaptcha();" action="model/toReg.php" method="POST">
            <!-- Name Fields Row -->
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="fname">First Name</label>
                    <input type="text" name="fname" id="fname" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="mname">Middle Name</label>
                    <input type="text" name="mname" id="mname">
                </div>
                <div class="col-md-4 form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" name="lname" id="lname" required>
                </div>
            </div>

            <!-- Email and Age Fields Row -->
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" required min="12" max="120">
                </div>
            </div>

            <!-- Phone Number and Address Row -->
            <div class="row">
                <div class="col-md-6 form-group">
                <label for="phonenumber">Phone Number</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" style = "margin-right:10px; margin-top:5px;font-size:18px;">+63</span>
            </div>
            <input type="text" name="phonenumber" pattern="\d{10}" title="Phone number must be 10 digits." id="phonenumber" class="form-control" required>
        </div>
                </div>  
                <div class="col-md-6 form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" required>
                </div>
            </div>

            <!-- Password Fields Row -->
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
            </div>

            <!-- CAPTCHA Row -->
            <div class="row">
                <div class="col-md-6 captcha-container">
                    <div id="captchaText" class="captcha-text"></div>
                    <button type="button" onclick="generateCaptcha();" class="btn btn-secondary" style = "margin-left:5%">Refresh</button>
                </div>
                <div class="col-md-6 form-group">
                    <label for="captchaInput">Enter CAPTCHA</label>
                    <input type="text" id="captchaInput" required>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn">Register</button><br><br>
            <div class = loginpath>
            <a href="login.php">Already have an account?</a></div>
        </form>
        <!-- Registration Success Modal -->
    <?php if ($register_message) { ?>
        <div class="modal" id="successModal">
            <div class="modal-content">
                <h2><?php echo $register_message; ?></h2>
                <button class="modal-button" id="okButton">OK</button>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Show the modal when registration is successful
                document.getElementById('successModal').style.display = 'block';

                // Redirect to login.php when OK button is clicked
                document.getElementById('okButton').onclick = function() {
                    window.location.href = 'login.php';
                };
            });
        </script>
    <?php } ?>
    </div>
    </main>
    </div>
    <!-- Footer as before -->
<?php include('footer.php'); ?>
</body>
</html>
