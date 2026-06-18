<?php
session_start();
include '../Project in WST/server/server.php';

if (!isset($_SESSION['email'])) {
    die('Email session not set. Please start from the beginning.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    $otp = trim($_POST['otp']); // Trim to avoid extra spaces
    $current_time = date('Y-m-d H:i:s'); // Current server time

    // Check if OTP is correct and not expired
    $query = $conn->prepare("SELECT * FROM cgt_accounts WHERE email = ? AND otp = ? AND otp_expiry > ?");
    $query->bind_param('sss', $email, $otp, $current_time);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['verified_email'] = $email;
        header('Location: reset_password.php'); // Redirect to reset password page
        exit;
    } else {
        // Check if OTP exists but expired
        $query = $conn->prepare("SELECT * FROM cgt_accounts WHERE email = ? AND otp = ?");
        $query->bind_param('ss', $email, $otp);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = 'OTP has expired. Please request a new one.';
        } else {
            $_SESSION['error_message'] = 'Invalid OTP. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /*Button Return*/ 
    .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
    .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
    
        /* Add styling similar to the login page */
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f7f7f7;
        }

        .forgot-container {
            background-color: rgba(25, 24, 24, 0.9);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
            margin-bottom: 16%;
            margin-top: 16%;
        }

        h2 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            color: #fff;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-btn {
            width: 100%;
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

        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }
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
        .needHelp{
            color:lightblue;
        }
        .needHelp:hover {
        color: rgba(64, 135, 148);
        }
    </style>
</head>
<body>
<div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
</div>
<div class="navbar bg-dark fixed-top">
    <div class="container d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <div class="myLogo">
            <a href="userwebsite.php"><img src="logo.png" alt="myLogo" width="80" height="80"></a>
        </div>
        <h2 style="margin-right:700px; color: white;">Core Gear Tech | Verify OTP</h2>
        <a href="needHelp.php" class="needHelp" style="text-decoration:none;" target="_blank">Need help?</a>
    </div>
</div>

<button class="btn-return" style="margin-top: 5%;" onclick="window.location.href='login.php';">
        <i class="fa fa-reply"></i> <!-- Return icon -->
</button>
<div class="forgot-container">
    <h2>Verify OTP</h2>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <form action="" method="POST">
        <div class="form-group">
            <label for="otp">Enter OTP</label>
            <input type="text" id="otp" name="otp" required>
        </div>
        <button type="submit" class="submit-btn">Verify OTP</button>
    </form>
</div>
<?php include('footer.php'); ?>
</body>
</html>
