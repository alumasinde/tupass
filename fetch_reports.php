<?php
include 'db.php';

$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';
$status = $_GET['status'] ?? '';
$department = $_GET['department'] ?? '';

// Build query
$query = "SELECT * FROM materials WHERE 1=1";

if ($dateFrom) {
    $query .= " AND date_out >= '$dateFrom'";
}
if ($dateTo) {
    $query .= " AND date_out <= '$dateTo'";
}
if ($status) {
    $query .= " AND status = '$status'";
}
if ($department) {
    $query .= " AND department = '$department'";
}

$query .= " ORDER BY date_out DESC";
$results = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

if (count($results) > 0) {
    foreach ($results as $row) {
        echo "<tr>
                <td>{$row['pass_no']}</td>
                <td>{$row['name']}</td>
                <td>{$row['department']}</td>
                <td>{$row['items']}</td>
                <td>{$row['status']}</td>
                <td>{$row['date_out']}</td>
                <td>{$row['date_in']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No records found.</td></tr>";
}
?>
