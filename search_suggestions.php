<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "cgt database";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search query
$query = $_GET['query'] ?? '';
$query = $conn->real_escape_string($query);

// Fetch matching products with product_id
$sql = "SELECT product_id, product_name, product_image 
        FROM cgt_products 
        WHERE product_name LIKE '%$query%' 
        LIMIT 10";
$result = $conn->query($sql);

$suggestions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = [
            'id' => $row['product_id'],        // Include product_id
            'name' => $row['product_name'],
            'image' => $row['product_image']  // Ensure this contains the correct image URL or path
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($suggestions);

$conn->close();
?>
