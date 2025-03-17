<?php
// Start session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

// Fetch user details
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch system settings (Company name and logo)
$stmt = $pdo->prepare("SELECT company_name, logo FROM system_settings LIMIT 1");
$stmt->execute();
$settings = $stmt->fetch();
?>
<link rel="stylesheet" href="styles/css/header.css">
<!-- Custom Navbar -->
<nav class="custom-navbar">
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">☰</button>

    <!-- Company Logo & Name -->
    <a class="navbar-brand" href="dashboard.php">
        <!-- Display the company logo or a default logo if not available -->
        <img src="upload/logos/<?php echo htmlspecialchars($settings['logo'] ?: 'default_logo.png'); ?>" alt="Company Logo" class="logo">
        <span class="company-name"><?php echo htmlspecialchars($settings['company_name']); ?></span>
    </a>

    <!-- User Profile Dropdown -->
    <div class="user-dropdown">
        <button id="userDropdownBtn" class="user-btn">
            <i class="icon-user"></i> <?php echo htmlspecialchars($user['username']); ?>
        </button>
        <div id="userDropdown" class="dropdown-menu">
            <a href="user_profile.php">Profile</a>
            <hr>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
</nav>

<!-- Sidebar Toggle Script -->
<script type="text/javascript" src="script.js"></script>
<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    // User dropdown toggle
    document.getElementById('userDropdownBtn').addEventListener('click', function() {
        document.getElementById('userDropdown').classList.toggle('show');
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        if (!document.getElementById('userDropdownBtn').contains(e.target) &&
            !document.getElementById('userDropdown').contains(e.target)) {
            document.getElementById('userDropdown').classList.remove('show');
        }
    });
</script>
