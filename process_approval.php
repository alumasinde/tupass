<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
    exit;
}

$id = $_POST['id'];
$user_role = $_SESSION['role'];

// Get current gate pass status
$stmt = $pdo->prepare("SELECT status FROM gate_pass WHERE id = ?");
$stmt->execute([$id]);
$gate_pass = $stmt->fetch();

if (!$gate_pass) {
    echo json_encode(['success' => false, 'message' => 'Gate Pass not found!']);
    exit;
}

$status = $gate_pass['status'];

// Check role-based approval logic
if ($status === 'Pending' && $user_role === 'HOD') {
    $new_status = 'Security Approval';
} elseif ($status === 'Security Approval' && $user_role === 'Security') {
    $new_status = 'GM Approval';
} elseif ($status === 'GM Approval' && $user_role === 'General Manager') {
    $new_status = 'Approved';
} else {
    echo json_encode(['success' => false, 'message' => 'You are not authorized to approve this Gate Pass!']);
    exit;
}

// Update the approval status
$stmt = $pdo->prepare("UPDATE gate_pass SET status = ? WHERE id = ?");
$stmt->execute([$new_status, $id]);

echo json_encode(['success' => true, 'message' => 'Gate Pass Approved!']);
