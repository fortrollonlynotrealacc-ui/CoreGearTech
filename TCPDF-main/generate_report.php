<?php
require_once('../Project in WST/TCPDF-main/tcpdf.php');
include '../Project in WST/server/server.php';

// Fetch users, their purchase count, and status
$sql = "SELECT a.id AS user_id, 
               CONCAT(a.fname, ' ', a.lname, ' ', a.mname) AS full_name, 
               (SELECT COUNT(*) FROM cgt_user_purchases p WHERE p.user_id = a.id) AS purchase_count,
               a.status
        FROM cgt_accounts a";
$result = $conn->query($sql);

// Initialize TCPDF
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Users Report');
$pdf->SetHeaderData('', 0, 'Users Report', "Generated on: " . date('Y-m-d H:i:s'));

// Set margins and auto page break
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add table header
$tbl = '<table border="1" cellpadding="4">
    <thead>
        <tr style="background-color: #343a40; color: white;">
            <th>User ID</th>
            <th>Full Name</th>
            <th>Number of Purchases</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>';

// Populate table rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tbl .= '<tr>
            <td>' . htmlspecialchars($row['user_id']) . '</td>
            <td>' . htmlspecialchars($row['full_name']) . '</td>
            <td>' . htmlspecialchars($row['purchase_count']) . '</td>
            <td>' . htmlspecialchars(ucfirst($row['status'])) . '</td>
        </tr>';
    }
} else {
    $tbl .= '<tr><td colspan="4">No users found</td></tr>';
}

$tbl .= '</tbody></table>';

// Output the table
$pdf->writeHTML($tbl, true, false, false, false, '');

// Close and output PDF document
$pdf->Output('users_report.pdf', 'I'); // Open PDF in browser
?>
