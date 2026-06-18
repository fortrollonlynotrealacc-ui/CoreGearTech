<?php
session_start();
include '../Project in WST/server/server.php';

if (!isset($_SESSION['verified_email'])) {
    header('Location: forgot_password.php'); // Redirect if not verified
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['verified_email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = 'Passwords do not match. Please try again.';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the new password in the database
        $update = $conn->prepare("UPDATE cgt_accounts SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
        $update->bind_param('ss', $hashed_password, $email);
        $update->execute();

        if ($update->affected_rows > 0) {
            $_SESSION['success_message'] = 'Password reset successfully.';
            unset($_SESSION['verified_email']);
            header('Location: login.php'); // Redirect to login
            exit;
        } else {
            $_SESSION['error_message'] = 'Failed to reset your password. Please try again.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /*Button Return*/ 
    .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
    .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
    
        /* Add styling similar to the login page */
        .needHelp{
            color:lightblue;
        }
        .needHelp:hover {
        color: rgba(64, 135, 148);
        }
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
        <h2 style="margin-right:700px; color: white;">Core Gear Tech | Reset Password</h2>
        <a href="needHelp.php" class="needHelp" style="text-decoration:none;" target="_blank">Need help?</a>
    </div>
</div>

<button class="btn-return" style="margin-top: 5%;" onclick="window.location.href='login.php';">
        <i class="fa fa-reply"></i> <!-- Return icon -->
</button>
<div class="forgot-container">
    <h2 style ="padding-bottom:5px;">Reset Password</h2>
    <?php if (isset($_SESSION['error_message'])): ?>
            <script>
                // Display the error message using JavaScript
                var errorMessage = '<?= $_SESSION['error_message']; ?>';
                var errorDiv = document.createElement('div');
                errorDiv.innerText = errorMessage;
                errorDiv.style.color = 'red';
                errorDiv.style.fontWeight = 'bold';
                errorDiv.style.marginTop = '10px';
                errorDiv.style.position = 'absolute';
                errorDiv.style.top = '41%';
                errorDiv.style.left = '50%';
                errorDiv.style.transform = 'translate(-50%, -50%)';
                document.body.appendChild(errorDiv);

                // Hide the error message after 2 seconds
                setTimeout(function() {
                    errorDiv.style.display = 'none';
                }, 3000); // 2 seconds
            </script>
            <?php unset($_SESSION['error_message']); ?> <!-- Clear error message -->
        <?php endif; ?>
    <form action="" method="POST">
    <div class="form-group">
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" required>
    </div>
    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <button type="submit" class="submit-btn" style ="margin-bottom:5px;">Reset Password</button>
</form>
</div>
<?php include('footer.php'); ?>
</body>
</html>
