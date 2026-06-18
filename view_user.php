<?php
// Include necessary files
include '../Project in WST/server/server.php';

// Get the user ID from the query string (GET method)
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Fetch the user details
    $sql_user = "SELECT a.id AS user_id, 
                        CONCAT(a.fname, ' ', a.lname, ' ', a.mname) AS full_name, 
                        a.email AS email,
                        a.phonenumber AS phone,
                        a.address AS address,
                        a.status,
                        a.age
                 FROM cgt_accounts a
                 WHERE a.id = ?";
    $stmt = $conn->prepare($sql_user);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        // If no user is found
        echo "User not found.";
        exit;
    }
} else {
    echo "No user ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Details</title>
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
       
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        /*return button*/
        .btn-return { position: absolute; top: 20px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
            .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
        .container { max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background:#001f3f;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            color: #fff;}
        h1 { text-align: center; color: #333; }
        .user-info { margin-top: 20px; }
        .user-info p { font-size: 18px; }
        .btn-return {  color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .btn-return:hover { background-color: #555; }
    </style>
</head>
<body>

    <div class="container">
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <button class="btn-return" onclick="window.location.href='users.php';">
        <i class="fa fa-reply"></i>
    </button>
        <h1 style ="color:white;">User Details</h1>
        <div class="user-info">
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($user['user_id']); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> +63<?php echo htmlspecialchars($user['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($user['status'])); ?></p>
        </div>
        
    </div>

</body>
</html>
