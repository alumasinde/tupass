<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2, 3, 4, 5])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access!']);
    exit;
}

$pass_no = htmlspecialchars($_POST['pass_no']);
$action = $_POST['action']; // Either 'check_in' or 'check_out'
$user_id = $_SESSION['user_id'];

// Check if the gate pass exists
$stmt = $pdo->prepare("SELECT id FROM materials WHERE pass_no = ?");
$stmt->execute([$pass_no]);
$material = $stmt->fetch();

if (!$material) {
    echo json_encode(['success' => false, 'message' => 'Invalid Pass Number!']);
    exit;
}

$material_id = $material['id'];

if ($action == 'check_out') {
    // Check if material is already checked out
    $stmt = $pdo->prepare("SELECT id FROM check_in_out WHERE material_id = ? AND check_out_time IS NULL");
    $stmt->execute([$material_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'This material is already checked out!']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO check_in_out (pass_no, material_id, check_out_time, checked_by) VALUES (?, ?, NOW(), ?)");
        $stmt->execute([$pass_no, $material_id, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Material Checked Out Successfully!']);
    }
} elseif ($action == 'check_in') {
    // Check if material is checked out before checking in
    $stmt = $pdo->prepare("SELECT id FROM check_in_out WHERE material_id = ? AND check_in_time IS NULL");
    $stmt->execute([$material_id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        echo json_encode(['success' => false, 'message' => 'This material is not checked out!']);
    } else {
        $stmt = $pdo->prepare("UPDATE check_in_out SET check_in_time = NOW(), checked_by = ? WHERE material_id = ? AND check_in_time IS NULL");
        $stmt->execute([$user_id, $material_id]);
        echo json_encode(['success' => true, 'message' => 'Material Checked In Successfully!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action!']);
}
