<?php
// Start session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Define user role
$role = isset($_SESSION['role_name']) ? $_SESSION['role_name'] : 'Employee'; // Default role if not set
?>
<link rel="stylesheet" href="styles/css/sidebar.css">
<!-- FontAwesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Custom Sidebar -->
<div id="sidebar" class="sidebar">

    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>

        <!-- Gate Pass Section -->
        <li><a href="manage_gatepass.php"><i class="fas fa-file-signature"></i> Create Gate Pass</a></li>

        <li><a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a></li>

        <li><a href="check_in_out.php"><i class="fas fa-exchange-alt"></i> Check-In/Out</a></li>

        <li><a href="departments.php"><i class="fas fa-id-card"></i>Manage Department</a></li>

        <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>

        <li><a href="visitors.php"><i class="fas fa-user-friends"></i>Manage Visitors</a></li>

        <li><a href="companies.php"><i class="fas fa-industry"></i> Manage Companies</a></li>

        <li><a href="roles.php"><i class="fas fa-user-lock"></i>Manage Roles</a></li>

        <li><a href="settings.php"><i class="fas fa-cogs"></i> System Settings</a></li>

        <!-- Logout -->
        <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Sidebar Toggle Script -->
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>
