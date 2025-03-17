<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$dep_id = $_POST['dep_id'];

$stmt = $pdo->prepare("DELETE FROM departments WHERE id = :id");
$stmt->execute([':id' => $dep_id]);

echo json_encode(['success' => true, 'message' => 'Department deleted successfully']);
