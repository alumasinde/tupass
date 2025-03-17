<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Redirect to dashboard if logged in
header("Location: dashboard_stats.php");
exit;
?>
