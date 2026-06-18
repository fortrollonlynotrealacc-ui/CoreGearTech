<?php
session_start();
include '../Project in WST/server/server.php';
require_once('TCPDF-main/tcpdf.php'); 

// Determine which view to show (guest or user purchases)
$view = isset($_GET['view']) ? $_GET['view'] : 'guest';

if ($view === 'guest') {
    $sql = "SELECT g.guest_id, g.paypal_accountnumber, p.product_id, p.quantity, p.purchase_date, p.price, 'N/A' AS order_status 
            FROM cgt_guest_purchases p
            JOIN cgt_guests g ON p.guest_id = g.guest_id
            ORDER BY p.purchase_date DESC"; // Sort by purchase_date descending
} else {
    $sql = "SELECT purchase_id, user_id, product_id, quantity, purchase_date, 
            (quantity * price) AS total_price, payment_method, account_number, order_status 
            FROM cgt_user_purchase"; // Base query

    // Optional: If a user_id is provided, add it as an additional filter
    if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
        $sql .= " WHERE user_id = $user_id"; // Add WHERE clause here
    }

    // Add ordering by purchase_date
    $sql .= " ORDER BY purchase_date DESC";
}



$result = $conn->query($sql);

// Initialize success message as empty
$pdfGenerated = false;

if (isset($_POST['generate_pdf'])) {
    $pdf = new TCPDF(); 
    $pdf->AddPage(); 
    $pdf->SetFont('helvetica', '', 8); // Reduce font size for better fit
    
    // Add title to PDF
    if ($view === 'user') {
        $pdf->Cell(0, 10, 'Purchase History of USERS', 0, 1, 'C');
        $pdf->Ln(10); // Line break
    }
    if ($view === 'guest') {
        $pdf->Cell(0, 10, 'Purchase History of GUEST', 0, 1, 'C');
        $pdf->Ln(10); // Line break
    }
    
    // Adjust column widths to fit content
    $pdf->Cell(13, 10, $view === 'guest' ? 'Guest ID' : 'User ID', 1);
    $pdf->Cell(15, 10, 'Product ID', 1);
    $pdf->Cell(13, 10, 'Quantity', 1);
    $pdf->Cell(30, 10, 'Purchase Date', 1);
    $pdf->Cell(25, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Payment Method', 1);
    $pdf->Cell(40, 10, 'Account', 1);
    $pdf->Cell(24, 10, 'Order Status', 1); // Add Order Status column
    $pdf->Ln(); // Line break
    
    // Add table data to the PDF
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(13, 10, htmlspecialchars($view === 'guest' ? $row['guest_id'] : $row['user_id']), 1);
        $pdf->Cell(15, 10, htmlspecialchars($row['product_id']), 1);
        $pdf->Cell(13, 10, htmlspecialchars($row['quantity']), 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['purchase_date']), 1);
        $pdf->Cell(25, 10, '$' . number_format($view === 'guest' ? $row['price'] : $row['total_price'], 2), 1);
        $pdf->Cell(30, 10, $view === 'guest' ? 'PayPal' : htmlspecialchars($row['payment_method']), 1);
        
        $accountNumber = ($view === 'user' && $row['payment_method'] === 'cash_on_delivery') ? 'N/A' : 
                         htmlspecialchars($view === 'guest' ? $row['paypal_accountnumber'] : $row['account_number']);
        $pdf->Cell(40, 10, $accountNumber, 1);
        
        // Add Order Status value
        $pdf->Cell(24, 10, htmlspecialchars($row['order_status']), 1);
        $pdf->Ln();
        
        if ($pdf->GetY() > 260) {
            $pdf->AddPage();
        }
    }
    
    // Output the PDF to the browser
    $pdf->Output('purchase_history.pdf', 'I');
    
    // Set success message
    $pdfGenerated = true;

    // Exit to prevent further script execution
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase History</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>
    <style>
        /* Basic styling for the table */
        table { width: 90%; margin: 20px auto; background-color: #343a40; color: #ffffff; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #444; text-align: center; }
        th { background-color: rgba(64, 135, 148, 0.7); }
        .page-title { text-align: center; font-size: 24px; margin: 20px; color: #343a40; }
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        /*return button*/
        .btn-return { position: absolute; top: 20px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
            .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
        /* Switch Buttons Styling */
        .switch-buttons { text-align: center; margin: 20px; }
        .switch-button { padding: 10px 20px; cursor: pointer; background-color: #408794; color: white; border: none; }
        .switch-button.active { background-color: #346999}
        .user-select { margin: 20px auto; text-align: center; }
    </style>

    <div class="video-background">
        <video autoplay muted loop id="bg-video">
            <source src="videobg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <button class="btn-return" onclick="window.location.href='adminwebsite.php';">
        <i class="fa fa-reply"></i>
    </button>

    <h1 class="page-title" style ="color:white;">Purchase History</h1>

    <!-- Switch Buttons -->
    <div class="switch-buttons">
        <button onclick="switchView('guest')" class="switch-button <?php echo ($view === 'guest') ? 'active' : ''; ?>">Guest Purchases</button>
        <button onclick="switchView('user')" class="switch-button <?php echo ($view === 'user') ? 'active' : ''; ?>">User Purchases</button>
    </div>

    <!-- Optional: User Selection Dropdown -->
    <?php if ($view === 'user'): ?>
        <div class="user-select">
            <form method="GET">
                <input type="hidden" name="view" value="user">
                <label for="user_id" style="color:white;">View Purchases for User ID:</label>
                <input type="number" name="user_id" id="user_id" min="1" value="<?php echo isset($user_id) ? $user_id : ''; ?>">
                <button type="submit" class="switch-button">Filter</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- PDF Generation Button -->
    <form method="POST" target="_blank">
        <button type="submit" name="generate_pdf" class="switch-button" style="background-color: #343a40;">Generate PDF</button>
    </form>

    <?php if ($pdfGenerated): ?>
        <p style="color:white; text-align:center; font-size: 18px;">PDF has been generated successfully!</p>
    <?php endif; ?>
    <?php if ($result->num_rows > 0) : ?>
        <table>
    <tr>
        <?php if ($view === 'guest') : ?>
            <th>Guest ID</th>
        <?php else : ?>
            <th>User ID</th>
        <?php endif; ?>
        <th>Product ID</th>
        <th>Quantity</th>
        <th>Purchase Date</th>
        <th><?php echo $view === 'guest' ? 'Price' : 'Total Price'; ?></th>
        <th>Payment Method</th>
        <th>Account</th>
        <th>Order Status</th> <!-- New column -->
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
    <tr>
        <td><?php echo htmlspecialchars($view === 'guest' ? $row['guest_id'] : $row['user_id']); ?></td>
        <td><?php echo htmlspecialchars($row['product_id']); ?></td>
        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
        <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
        <td>
            <?php 
            echo '$' . number_format($view === 'guest' ? $row['price'] : $row['total_price'], 2); 
            ?>
        </td>
        <td><?php echo $view === 'guest' ? 'PayPal' : htmlspecialchars($row['payment_method']); ?></td>
        <td>
            <?php 
            echo ($view === 'user' && $row['payment_method'] === 'cash_on_delivery') ? 'N/A' : 
                 htmlspecialchars($view === 'guest' ? $row['paypal_accountnumber'] : $row['account_number']);
            ?>
        </td>
        <td><?php echo htmlspecialchars($row['order_status']); ?></td> <!-- New column -->
    </tr>
    <?php endwhile; ?>
</table>

<?php else : ?>
    <p style="color:white; text-align:center;">No records found.</p>
<?php endif; ?>

</body>
</html>

<script>
function switchView(view) {
    window.location.href = '?view=' + view;
}
</script>
