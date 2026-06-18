

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Gear Tech | Need Help</title>
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
           /*return button*/
    .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
    .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
            
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000;
            color: #fff;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background:#001f3f;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            color: #fff;
        }
        h1 {
            text-align: center;
            color: #444;
        }
        ol {
            margin-left: 20px;
        }
        .important {
            color: #d9534f;
            font-weight: bold;
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
<button class="btn-return" style="margin-top: 5%;" onclick="window.location.href='website.php';">
    <i class="fa fa-reply"></i> 
</button>
    <div class="container">
        <h1 style = "color:white;">Need Help? Reset Your Password</h1>
        <p>If you've forgotten your password, don't worry! Follow these steps to reset it and log back into your account:</p>

        <h2>Step 1: Request a Password Reset</h2>
        <ol>
            <li>Go to the login page and click on the <strong>"Forgot Password"</strong> link.</li>
            <li>Enter your registered email address in the provided field.</li>
            <li>Click on the <strong>"Send OTP"</strong> button.
                <ul>
                    <li>An OTP (One-Time Password) will be sent to your email or phone.</li>
                    <li>The OTP is valid for <span class="important">15 minutes</span>.</li>
                </ul>
            </li>
        </ol>

        <h2>Step 2: Verify the OTP</h2>
        <ol>
            <li>Check your email OTP message.</li>
            <li>Go to the <strong>"Verify OTP"</strong> page.</li>
            <li>Enter the OTP you received in the provided field.</li>
            <li>Click <strong>"Verify OTP"</strong>.
                <ul>
                    <li>If the OTP is valid, you will be redirected to the <strong>Reset Password</strong> page.</li>
                    <li>If the OTP is incorrect or expired:
                        <ul>
                            <li>You’ll see an error message.</li>
                            <li>You can request a new OTP by repeating <strong>Step 1</strong>.</li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ol>

        <h2>Step 3: Reset Your Password</h2>
        <ol>
            <li>On the <strong>"Reset Password"</strong> page:
                <ul>
                    <li>Enter your new password in the provided field.</li>
                    <li>Confirm the new password by entering it again.</li>
                    <li>Click <strong>"Submit"</strong>.</li>
                </ul>
            </li>
            <li>You will see a confirmation message if the password reset is successful.</li>
        </ol>

        <h2>Step 4: Log In with Your New Password</h2>
        <ol>
            <li>Return to the login page.</li>
            <li>Enter your email and the new password you just created.</li>
            <li>Click on the <strong>"Login"</strong> button to access your account.</li>
        </ol>

        <h2>Important Notes</h2>
        <ul>
            <li>Ensure that you use a strong password:
                <ul>
                    <li>At least 8 characters.</li>
                    <li>Includes uppercase and lowercase letters, numbers, and special characters.</li>
                </ul>
            </li>
            <li>If you don’t receive the OTP:
                <ul>
                    <li>Check your spam/junk folder in your email.</li>
                    <li>Ensure your phone has a stable network connection.</li>
                </ul>
            </li>
            <li>The OTP is valid for <span class="important">15 minutes</span> only. If it expires, request a new one.</li>
        </ul>

        <p>By following these steps, you can securely reset your password and regain access to your account.</p>
    </div>
</body>
</html>
