<?php
include '../Project in WST/server/server.php';
// Load the current product configuration
$featuredproducts = include('featured-products-config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the new product IDs from the form inputs
    $featured1 = isset($_POST['featured1']) ? $_POST['featured1'] : '';
    $featured2 = isset($_POST['featured2']) ? $_POST['featured2'] : '';
    $featured3 = isset($_POST['featured3']) ? $_POST['featured3'] : '';
    $featured4 = isset($_POST['featured4']) ? $_POST['featured4'] : '';
    $featured5 = isset($_POST['featured5']) ? $_POST['featured5'] : '';
    $featured6 = isset($_POST['featured6']) ? $_POST['featured6'] : '';

    // Array of input IDs
    $submittedIDs = [$featured1, $featured2, $featured3, $featured4, $featured5, $featured6];

    // Validate product IDs
    $invalidIDs = [];
    foreach ($submittedIDs as $id) {
        if ($id) {
            // Prepare the query to check if the product ID exists
            $query = $conn->prepare("SELECT COUNT(*) as count FROM cgt_products WHERE product_id = ?");
            $query->bind_param('i', $id);
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            
            // If the product ID does not exist in the database, add to invalid IDs
            if ($result['count'] == 0) {
                $invalidIDs[] = $id;
            }
        }
    }

    // If there are invalid IDs, show an alert and stop processing
    if (!empty($invalidIDs)) {
        // Output the JavaScript alert with the invalid product IDs
        echo "<script>alert('The following Product IDs are invalid: " . implode(', ', $invalidIDs) . ". Please check your input.');</script>";
    } else {
        // Prepare the new configuration array with valid product IDs
        $newConfig = [
            'featured1' => $featured1,
            'featured2' => $featured2,
            'featured3' => $featured3,
            'featured4' => $featured4,
            'featured5' => $featured5,
            'featured6' => $featured6
        ];

        // Save the updated configuration back to the featured-products-config.php file
        $configFile = '<?php return ' . var_export($newConfig, true) . ';';
        file_put_contents('featured-products-config.php', $configFile);

        // Redirect to admin_website.php after saving the configuration
        header('Location: adminwebsite.php#features');
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
    <title>Update Featured Product </title>
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
        
    </button>
    </div>
    <div class="form-container">
    <h2>Update Featured Products Display</h2>
    <form method="POST" action="">
        <label for="featured1">First Featured Products ID:</label>
        <input type="number" id="featured1" name="featured1" value="" required><br>

        <label for="featured2">Second Featured Products ID:</label>
        <input type="number" id="featured2" name="featured2" value=""required><br>

        <label for="featured3">Third Featured Products ID:</label>
        <input type="number" id="featured3" name="featured3" value=""required><br>

        <label for="featured4">Fourth Featured Products ID:</label>
        <input type="number" id="featured4" name="featured4" value=""required><br>

        <label for="featured5">Fifth Featured Products ID:</label>
        <input type="number" id="featured5" name="featured5" value=""required><br>

        <label for="featured6">Sixth Featured Products ID:</label>
        <input type="number" id="featured6" name="featured6" value=""required><br><br>




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
            height: 40px;
        }

        .form-container input, .form-container button {
            background-color:white;
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
