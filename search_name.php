<?php
include 'db.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];

    $stmt = $pdo->prepare("
        SELECT name FROM users WHERE name LIKE ? 
        UNION 
        SELECT name FROM visitors WHERE name LIKE ?
    ");
    
    $stmt->execute(["%$query%", "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($results);
}
?>
