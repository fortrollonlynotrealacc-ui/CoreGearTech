<?php
include '../Project in WST/server/server.php';

// Handle form submission for adding and updating
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];
    $product_image = $_POST['product_image'];
    $product_category = $_POST['product_category'];
    $product_price = $_POST['product_price'];
    $quantity = $_POST['quantity'];

    if ($product_id) {
        // Update existing product
        $stmt = $conn->prepare("UPDATE cgt_products SET product_name=?, product_desc=?, product_image=?, product_category=?, product_price=?, quantity=? WHERE product_id=?");
        $stmt->bind_param("ssssdii", $product_name, $product_desc, $product_image, $product_category, $product_price, $quantity, $product_id);
    }else {
        // Insert new product
        $stmt = $conn->prepare("INSERT INTO cgt_products (product_name, product_desc, product_image, product_category, product_price, quantity) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdi", $product_name, $product_desc, $product_image, $product_category, $product_price, $quantity);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: adminproducts.php");
    exit;
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM cgt_products WHERE product_id=$delete_id");
    header("Location: adminproducts.php");
    exit;
}

// Fetch product for editing
$product_to_edit = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $result = $conn->query("SELECT * FROM cgt_products WHERE product_id=$edit_id");
    $product_to_edit = $result->fetch_assoc();
}

// Fetch all products for display
$products = $conn->query("SELECT * FROM cgt_products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <title>Admin Products</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            text-align: center;
            max-width: 100%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            color:white;
            background-color: rgba(50, 50, 50, 0.2);
        }

        form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left;
        }

        form input[type="text"],
        form input[type="number"],
        form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form textarea {
            height: 80px; /* Adjust as needed */
        }

        form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #218838;
        }
        form .cancel-button {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        form .cancel-button:hover {
            background-color: #c82333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            color:white;
        }
        a{
            text-decoration:none;
            color:white;
        }
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        .checkout-container { width: 50%; margin: 0 auto; background-color: rgb(25, 24, 24,0.7); padding: 20px; border-radius: 8px;color: white;}
        /*return button*/
        .btn-return { position: absolute; top: 5px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
            .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
    </style>
</head>
<body>
    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <button class="btn-return" style="margin-top: 2%;" onclick="window.location.href='adminwebsite.php'">
        <i class="fa fa-reply"></i> <!-- Return icon -->
    </button>
    <div class="container">

        <h2><?php echo $product_to_edit ? "Edit Product" : "Add Product"; ?></h2>

        <!-- Form for adding/editing products -->
        <form action="adminproducts.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo $product_to_edit ? $product_to_edit['product_id'] : ''; ?>">
            
            <label>Product Name:</label>
            <input type="text" name="product_name" value="<?php echo $product_to_edit ? $product_to_edit['product_name'] : ''; ?>" required>
            
            <label>Description:</label>
            <textarea name="product_desc" required><?php echo $product_to_edit ? $product_to_edit['product_desc'] : ''; ?></textarea>
            
            <label>Image:</label>
            <input type="text" name="product_image" value="<?php echo $product_to_edit ? $product_to_edit['product_image'] : ''; ?>" required>
            
            <label>Category:</label>
            <input type="text" name="product_category" value="<?php echo $product_to_edit ? $product_to_edit['product_category'] : ''; ?>" required>
            
            <label>Price:</label>
            <input type="number" name="product_price" step="0.01" value="<?php echo $product_to_edit ? $product_to_edit['product_price'] : ''; ?>" required>
            
            <label>Quantity:</label>
            <input type="number" name="quantity" value="<?php echo $product_to_edit ? $product_to_edit['quantity'] : ''; ?>" required>
            
            <button type="submit" style ="background-color: #173a4a;"><?php echo $product_to_edit ? "Save Product" : "Add Product"; ?></button>
            <?php if ($product_to_edit): ?>
                <a href="adminproducts.php">
                    <button type="button" class="cancel-button">Cancel</button>
                </a>
            <?php endif; ?>
        </form>

        <h3 style = "margin-top:5%;">Product List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Image</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['product_id']; ?></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['product_desc']; ?></td>
                <td><?php echo $row['product_image']; ?></td>
                <td><?php echo $row['product_category']; ?></td>
                <td><?php echo $row['product_price']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>
                    <a href="adminproducts.php?edit_id=<?php echo $row['product_id']; ?>">Edit</a> |
                    <a href="adminproducts.php?delete_id=<?php echo $row['product_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
