<?php
session_start();
include '../Project in WST/server/server.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../Project in WST/PHPMailer-master/src/Exception.php';
require '../Project in WST/PHPMailer-master/src/PHPMailer.php';
require '../Project in WST/PHPMailer-master/src/SMTP.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Invalid email address.';
        header('Location: forgot_password.php');
        exit;
    }

    // Check if the email exists in the database
    $query = $conn->prepare("SELECT * FROM cgt_accounts WHERE email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Generate OTP
        $otp = rand(100000, 999999); // 6-digit OTP
        $expiry_time = date('Y-m-d H:i:s', strtotime('+15 minutes')); // OTP expires in 15 mins

        // Store OTP in the database
        $update = $conn->prepare("UPDATE cgt_accounts SET otp = ?, otp_expiry = ? WHERE email = ?");
        $update->bind_param('sss', $otp, $expiry_time, $email);
        $update->execute();

        if ($update->affected_rows > 0) {
            // Send the OTP via email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'fernandezjust6@gmail.com'; // Your email
                $mail->Password = 'wppp rbeq mdvs klhz'; // Your app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('fernandezjust6@gmail.com', 'Core Gear Tech');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'OTP for Password Reset';
                $mail->Body = "Hello,<br><br>Your One-Time Password (OTP) is: <b>$otp</b><br><br>It is valid for 15 minutes.";
                $mail->AltBody = "Your OTP is: $otp. It is valid for 15 minutes.";

                $mail->send();
                $_SESSION['success_message'] = 'An OTP has been sent to your email.';
                $_SESSION['email'] = $email;
                header('Location: verify_otp.php');
                exit;
            } catch (Exception $e) {
                $_SESSION['error_message'] = 'Failed to send the OTP. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error_message'] = 'Failed to generate OTP. Please try again.';
        }
    } else {
        $_SESSION['error_message'] = 'No account found with this email address.';
    }

    header('Location: forgot_password.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /*return button*/
    .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
    .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
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
        <h2 style="margin-right:700px; color: white;">Core Gear Tech | Forgot Password</h2>
        <a href="needHelp.php" class="needHelp" style="text-decoration:none;" target="_blank">Need help?</a>
    </div>
</div>

<button class="btn-return" style="margin-top: 5%;" onclick="window.location.href='login.php';">
        <i class="fa fa-reply"></i> <!-- Return icon -->
</button>
<div class="forgot-container">
    <h2>Forgot Password</h2>

    <!-- Display error or success messages -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>
        <button type="submit" class="submit-btn">Send Temporary OTP</button>
    </form>
</div>
<?php include('footer.php'); ?>
</body>
</html>
