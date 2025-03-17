<?php
include 'db.php';

$format = $_GET['format'] ?? 'pdf';

$query = "SELECT pass_no, name, department, items, status, date_out, date_in FROM materials ORDER BY date_out DESC";
$results = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

if ($format == 'pdf') {
    require 'tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('Helvetica', '', 12);
    $html = '<h2>Gate Pass Reports</h2><table border="1" cellpadding="5">';
    foreach ($results as $row) {
        $html .= "<tr><td>{$row['pass_no']}</td><td>{$row['name']}</td><td>{$row['department']}</td><td>{$row['items']}</td><td>{$row['status']}</td><td>{$row['date_out']}</td><td>{$row['date_in']}</td></tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('GatePassReports.pdf', 'D');
    exit;
}

if ($format == 'excel') {
    header("Content-Disposition: attachment; filename=GatePassReports.xlsx");
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

    echo "Pass No\tName\tDepartment\tItems\tStatus\tDate Out\tCheck-In Date\n";
    foreach ($results as $row) {
        echo "{$row['pass_no']}\t{$row['name']}\t{$row['department']}\t{$row['items']}\t{$row['status']}\t{$row['date_out']}\t{$row['date_in']}\n";
    }
    exit;
}
?>
