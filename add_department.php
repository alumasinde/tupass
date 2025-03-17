<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$dep_name = htmlspecialchars($_POST['dep_name']);
if (empty($dep_name)) {
    echo json_encode(['success' => false, 'message' => 'Department name cannot be empty']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO departments (dep_name) VALUES (:dep_name)");
$stmt->execute([':dep_name' => $dep_name]);

echo json_encode(['success' => true, 'message' => 'Department added successfully']);
