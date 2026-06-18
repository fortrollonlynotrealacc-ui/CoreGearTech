<?php
session_start();
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']); // Clear the error message after displaying
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <style>
        /* Video Background Styling */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            z-index: -1;
        }

        #bg-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));
            z-index: 1;
        }

        /* Main Container Styling */
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .scrollable.container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 80%;
            max-width: 400px;
            background-color: rgba(25, 24, 24, 0.9);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .logo-title-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .logo img {
            width: 110px;
        }

        .form-title {
            margin-right:38%;
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

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
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
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }
        .registerpath a{
            color: white;
            text-align:center;
            text-decoration:none;
        }
        .registerpath a:hover{
            color: rgb(20, 80, 112);
        }
        /* Footer styling */
        .footer {
            background-color: black;
            color: white;
            padding: 20px 0;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .footer-logo {
            margin-bottom: 10px;
        }

        .footer-contents a {
            color: white;
            text-decoration: none;
        }
        /* Error message styling */
        .error-message {
            text-align: center;
            color: red;
            font-weight: bold;
            margin-top: 3px;
            margin-bottom: 3px;
            height: 20px; /* Reserve space for the error message */
            visibility: hidden; /* Hide initially but keep space */
        }

        /* Show error message */
        .show {
            visibility: visible;
        }
    </style>
</head>
</head>
<body>

<div class="scrollable container">
    <!-- Video Background -->
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <!-- Login Form -->
    <main class="content">
        <div class="login-container">
            <div class="logo-title-container">
                <div class="logo">
                    <a href="Website.php"><img src="logo.png" alt="Core Gear Tech Logo"></a>
                </div>
                <h2 class="form-title">Login</h2>
            </div>

            <div class="error-message" id="errorMessage">
             <?php if ($error_message) echo $error_message; ?>
            </div>

            <form action="model/toLog.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="password" id="password" required>
                </div>
                <button type="submit" class="submit-btn">LOGIN</button>
                <div class="registerpath">
                    <br><a href="register.php">Don't have an account?</a>
                </div>
                    <br><a href="forgot_password.php" style ="color:light-blue; text-decoration:none;">Forgot Password</a>
            </form>
        </div>
    </main>
</div>

<!-- Footer as before -->
<?php include('footer.php'); ?>
<script>
    // Show the error message for 3 seconds, then hide it
    document.addEventListener("DOMContentLoaded", function () {
        var errorMessage = document.getElementById("errorMessage");
        if (errorMessage && errorMessage.innerHTML.trim() !== "") {
            errorMessage.classList.add("show"); 
            setTimeout(function () {
                errorMessage.classList.remove("show"); 
            }, 3000);
        }
    });
</script>
</body>
</html>