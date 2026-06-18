<?php
include '../Project in WST/server/server.php';

// Fetch distinct user IDs and their names who have sent messages
$query = "SELECT DISTINCT cm.user_id, CONCAT(ca.fname, ' ', ca.lname, ' ', ca.mname) AS full_name 
          FROM chat_messages cm 
          JOIN cgt_accounts ca ON cm.user_id = ca.id 
          ORDER BY cm.created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error: " . $conn->error); // Debug error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Messages</title>
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            font-size: 32px;
            color: white;     
            margin-bottom: 20px;      
        }
        h2 {
            text-align: center;
            font-size: 22px;
            color:lightblue;     
            margin-bottom: 20px;      
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
            background-color: #343a40;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        li {
            margin: 0;
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        li:last-child {
            border-bottom: none;
        }

        a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        a:hover {
            color: lightblue;
        }

        /* Return button */
        .btn-return {
            position: absolute;
            top: 10px;
            left: 20px;
            background-color: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .btn-return:hover {
            color: #f0f0f0;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5);
        }

        /* Video Background */
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
    <button class="btn-return" onclick="window.location.href='adminwebsite.php';">
        <i class="fa fa-reply"></i>
    </button>
    <h1>User Messages</h1>
    <h2>These are the users that message the admin.</h2>
    <ul>
        <?php
        // Loop through the result and display user names
        while ($row = $result->fetch_assoc()) {
            echo "<li><a href='view_messages.php?user_id=" . $row['user_id'] . "'>" . htmlspecialchars($row['full_name']) . "</a></li>";
        }
        ?>
    </ul>
</body>
</html>
