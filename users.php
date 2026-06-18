<?php
// Include necessary files
include '../Project in WST/server/server.php';

// Fetch users data
$sql_users = "SELECT a.id AS user_id, 
                     CONCAT(a.fname, ' ', a.lname, ' ', a.mname) AS full_name, 
                     a.email AS email,
                     (SELECT COUNT(*) FROM cgt_user_purchase p WHERE p.user_id = a.id) AS purchase_count,
                     a.status
              FROM cgt_accounts a";
$result_users = $conn->query($sql_users);

// Fetch guests data
$sql_guests = "SELECT 
                   g.guest_id,
                   g.guest_name, 
                   g.guest_email, 
                   g.guest_number, 
                   (SELECT COUNT(*) FROM cgt_guest_purchases p WHERE p.guest_id = g.guest_id) AS purchase_count 
               FROM cgt_guests g";
$result_guests = $conn->query($sql_guests);

// Generate PDF if requested
if (isset($_GET['generate_report'])) {
    require_once('../Project in WST/TCPDF-main/tcpdf.php');

    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Users and Guests Report');
    $pdf->SetHeaderData('', 0, 'Users and Guests Report', "Generated on: " . date('Y-m-d H:i:s'));
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 10, PDF_MARGIN_RIGHT);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    // Users Section
    $pdf->writeHTML("<h3>Users Report</h3>", true, false, true, false, '');
    $tbl_users = '<table border="1" cellpadding="4">
        <thead>
            <tr style="background-color: #343a40; color: white;">
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Number of Purchases</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
    if ($result_users->num_rows > 0) {
        while ($row = $result_users->fetch_assoc()) {
            $tbl_users .= '<tr>
                <td>' . htmlspecialchars($row['user_id']) . '</td>
                <td>' . htmlspecialchars($row['full_name']) . '</td>
                <td>' . htmlspecialchars($row['email']) . '</td>
                <td>' . htmlspecialchars($row['purchase_count']) . '</td>
                <td>' . htmlspecialchars(ucfirst($row['status'])) . '</td>
            </tr>';
        }
    } else {
        $tbl_users .= '<tr><td colspan="4">No users found</td></tr>';
    }
    $tbl_users .= '</tbody></table>';
    $pdf->writeHTML($tbl_users, true, false, false, false, '');

    // Guests Section
    $pdf->writeHTML("<h3>Guests Report</h3>", true, false, true, false, '');
    $tbl_guests = '<table border="1" cellpadding="4">
        <thead>
            <tr style="background-color: #343a40; color: white;">
                <th>ID</th>
                <th>Guest Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Number of Purchases</th>
            </tr>
        </thead>
        <tbody>';
    if ($result_guests->num_rows > 0) {
        while ($guest = $result_guests->fetch_assoc()) {
            $tbl_guests .= '<tr>
                <td>' . htmlspecialchars($guest['guest_id']) . '</td>
                <td>' . htmlspecialchars($guest['guest_name']) . '</td>
                <td>' . htmlspecialchars($guest['guest_email']) . '</td>
                <td>' . htmlspecialchars($guest['guest_number']) . '</td>
                <td>' . htmlspecialchars($guest['purchase_count']) . '</td>
            </tr>';
        }
    } else {
        $tbl_guests .= '<tr><td colspan="4">No guests found</td></tr>';
    }
    $tbl_guests .= '</tbody></table>';
    $pdf->writeHTML($tbl_guests, true, false, false, false, '');

    // Output PDF
    $pdf->Output('users_and_guests_report.pdf', 'I');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users and Guests</title>
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap.bundle.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        table { width: 90%; margin: 20px auto; background-color: #343a40; color: #ffffff; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #444; text-align: center; }
        th { background-color: rgba(64, 135, 148, 0.7); }
        h1, h2 { text-align: center; color: white; }
        .btn-download { display: block; margin: 20px auto; background-color: #333; color: #fff; font-size: 16px; padding: 10px 20px; text-align: center; text-decoration: none; width:20%;}
        .btn-download:hover { background-color: #555; }
        /* Video Background Styling */
        .video-background { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1;}
        #bg-video {width: 100%;height: 100%;object-fit: cover;}
        .video-background::before {content: ''; position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1));z-index: 1;}
        /*return button*/
        .btn-return { position: absolute; top: 20px;left: 20px; background-color: transparent; border: none; color: white;
            font-size: 2rem; cursor: pointer; transition: color 0.3s ease;}
            .btn-return:hover { color: #f0f0f0;  text-shadow: 0 0 10px rgba(255, 255, 255, 0.7), 0 0 20px rgba(255, 255, 255, 0.5); }
        .action-button:hover{background-color:darkgray;}
        .view a:hover { color: lightblue; }
    
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

    <h1>Users and Guests</h1>
    <a href="?generate_report=true" class="btn-download" target="_blank">Download PDF Report</a>


    <h2>Users</h2>
<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Number of Purchases</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_users->num_rows > 0) : ?>
            <?php while ($row = $result_users->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['purchase_count']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                    <td>
    <form action="admin_actions.php" method="post" style="display:inline;" 
          onsubmit="return confirmAction(this);">
        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
        <?php if ($row['status'] === 'active') : ?>
            <button type="submit" name="block" class="action-button btn-block">Block</button>
        <?php elseif ($row['status'] === 'blocked') : ?>
            <button type="submit" name="unblock" class="action-button btn-unblock">Unblock</button>
        <?php endif; ?>
        <?php if ($row['status'] !== 'deleted') : ?>
            <button type="submit" name="delete" class="action-button btn-delete" 
                    onclick="return confirm('Are you sure you want to delete this user?');">
                Delete
            </button>
        <?php endif; ?>
    </form>
    <!-- View button -->
    <a href="view_user.php?user_id=<?php echo $row['user_id']; ?>" class="view btn-view" style ="text-decoration:none; color:lightblue;">  View</a>
</td>

                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr><td colspan="6">No users found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


<h2>Guests</h2>
<table>
    <thead>
        <tr>
            <th>Guest ID</th>
            <th>Guest Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Number of Purchases</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_guests->num_rows > 0) : ?>
            <?php while ($guest = $result_guests->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($guest['guest_id']); ?></td>
                    <td><?php echo htmlspecialchars($guest['guest_name']); ?></td>
                    <td><?php echo htmlspecialchars($guest['guest_email']); ?></td>
                    <td><?php echo htmlspecialchars($guest['guest_number']); ?></td>
                    <td><?php echo htmlspecialchars($guest['purchase_count']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr><td colspan="5">No guests found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<script>
    function confirmAction(form) {
        const blockButton = form.querySelector('button[name="block"]');
        const unblockButton = form.querySelector('button[name="unblock"]');

        if (blockButton && blockButton === document.activeElement) {
            return confirm('Are you sure you want to block this user?');
        }
        if (unblockButton && unblockButton === document.activeElement) {
            return confirm('Are you sure you want to unblock this user?');
        }
        return true; // Allow submission for other actions like Delete
    }
</script>

</body>
</html>
