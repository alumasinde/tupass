<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

include 'db.php';

try {
    // Fetch total gate passes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM gate_pass");
    $totalGatePasses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Fetch approved passes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM gate_pass WHERE status = 'approved'");
    $totalApprovedPasses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Fetch pending approvals
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM gate_pass WHERE status = 'pending'");
    $totalPendingPasses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Fetch rejected passes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM gate_pass WHERE status = 'rejected'");
    $totalRejectedPasses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Fetch pending check-outs (approved gate passes that haven't been checked out)
    $stmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM gate_pass gp 
        LEFT JOIN check_in_out cio ON gp.id = cio.id
        WHERE gp.status = 'approved' AND cio.check_out_time IS NULL
    ");
    $totalPendingCheckOuts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Fetch pending check-ins (items that have been checked out but not checked in)
    $stmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM check_in_out 
        WHERE check_out_time IS NOT NULL AND check_in_time IS NULL
    ");
    $totalPendingCheckIns = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Return JSON response
    echo json_encode([
        "totalGatePasses" => $totalGatePasses,
        "totalApprovedPasses" => $totalApprovedPasses,
        "totalPendingPasses" => $totalPendingPasses,
        "totalRejectedPasses" => $totalRejectedPasses,
        "totalPendingCheckOuts" => $totalPendingCheckOuts,
        "totalPendingCheckIns" => $totalPendingCheckIns
    ]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
exit;
