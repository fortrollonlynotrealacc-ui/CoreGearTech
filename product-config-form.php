<?php
include '../Project in WST/server/server.php';

// Load the current product configuration
$productConfig = include('product-config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the new product IDs from the form inputs
    $monitor_id = isset($_POST['monitor']) ? $_POST['monitor'] : '';
    $keyboard_id = isset($_POST['keyboard']) ? $_POST['keyboard'] : '';
    $processor_id = isset($_POST['processor']) ? $_POST['processor'] : '';
    $headset_id = isset($_POST['headset']) ? $_POST['headset'] : '';

    // Array of input IDs
    $submittedIDs = [$monitor_id, $keyboard_id, $processor_id, $headset_id];

    // Validate product IDs
    $invalidIDs = [];
    foreach ($submittedIDs as $id) {
        $query = $conn->prepare("SELECT COUNT(*) as count FROM cgt_products WHERE product_id = ?");
        $query->bind_param('i', $id);
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        if ($result['count'] == 0) {
            $invalidIDs[] = $id;
        }
    }

    // If there are invalid IDs, show an alert and stop processing
    if (!empty($invalidIDs)) {
        echo "<script>alert('The following Product IDs are invalid: " . implode(', ', $invalidIDs) . ". Please check your input.');</script>";
    } else {
        // Prepare the new configuration array
        $newConfig = [
            'monitor' => $monitor_id,
            'keyboard' => $keyboard_id,
            'processor' => $processor_id,
            'headset' => $headset_id,
        ];

        // Save the updated configuration back to the product-config.php file
        $configFile = '<?php return ' . var_export($newConfig, true) . ';';
        file_put_contents('product-config.php', $configFile);

        // Redirect to admin_website.php after saving
        header('Location: adminwebsite.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <title>Update Product</title>
</head>
<body>
    <button class="btn-return" onclick="window.location.href='adminwebsite.php';">
        <i class="fa fa-reply"></i>
    </button>
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="form-container">
        <h2>Update Product Display</h2>
        <form method="POST" action="">
            <label for="monitor">First Product ID:</label>
            <input type="number" id="monitor" name="monitor" value=""required><br><br>

            <label for="keyboard">Second Product ID:</label>
            <input type="number" id="keyboard" name="keyboard" value=""required><br><br>

            <label for="processor">Third Product ID:</label>
            <input type="number" id="processor" name="processor" value=""required><br><br>

            <label for="headset">Fourth Product ID:</label>
            <input type="number" id="headset" name="headset" value=""required><br><br>
            
            <button type="submit">Update Product IDs</button>
        </form>
    </div>
</body>
</html>

<style>
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

    .form-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(25, 24, 24, 0.9); /* Gray background with low opacity */
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        z-index: 2;
    }

    .form-container h2 {
        color: white;
    }

    .form-container label, .form-container button {
        color: white;
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .form-container input {
        color: black;
        width: 50px;
    }

    .form-container input, .form-container button {
        background-color: white;
        border: 1px solid white;
        padding: 10px;
        width: 100%;
    }

    .form-container button {
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

    .form-container button:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
</style>
