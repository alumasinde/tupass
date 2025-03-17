<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$dep_id = $_POST['edit_dep_id'];
$dep_name = htmlspecialchars($_POST['edit_dep_name']);

$stmt = $pdo->prepare("UPDATE departments SET dep_name = :dep_name WHERE id = :id");
$stmt->execute([':dep_name' => $dep_name, ':id' => $dep_id]);

echo json_encode(['success' => true, 'message' => 'Department updated successfully']);
