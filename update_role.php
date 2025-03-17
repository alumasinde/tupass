<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $role_name = htmlspecialchars($_POST['role_name']);

    $stmt = $pdo->prepare("UPDATE roles SET role_name = ? WHERE id = ?");
    $stmt->execute([$role_name, $id]);

    echo json_encode(["success" => true]);
}
?>
