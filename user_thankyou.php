<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You for Your Purchase - Core Gear Tech</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: rgb(25, 24, 24,0.7); 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
        }
        h1 {
            color: #0056b3;
        }
        p {
            margin: 10px 0;
            color:white;
        }
        .button {
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 15px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        /*return button*/
    </style>
</head>
<body>
<div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
</div>
<div class="container">
    <h1>Thank You for Your Purchase!</h1>
    <p>Your order has been successfully placed with Core Gear Tech. We appreciate your business and hope you enjoy your products!</p>
    <p>If you have any questions, feel free to contact our support team.</p>
    <a href="userwebsite.php" class="button">Return to Core Gear Tech Website</a>
</div>

</body>
</html>
