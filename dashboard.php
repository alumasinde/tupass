<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Fetch user details
$stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- FontAwesome for icons -->
  <link rel="stylesheet" href="styles/css/dashboard.css">

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">

<main class="dashboard-container">
    <section class="welcome-section">
        <h2>Welcome, <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name']; ?>!</h2>
    </section>

    <section class="dashboard-grid">
        <div class="card">
            <h3>Dashboard</h3>
            <p>View system insights and recent activity.</p>
            <a href="dashboard_stats.php" class="btn">Go</a>
        </div>

        <div class="card">
            <h3>Manage Gate Pass</h3>
            <p>Create and track gate passes.</p>
            <a href="manage_gatepass.php" class="btn">Go</a>
        </div>

        <div class="card">
            <h3>Approvals</h3>
            <p>Approve or reject material requests.</p>
            <a href="approvals.php" class="btn">Go</a>
        </div>

        <div class="card">
            <h3>Reports</h3>
            <p>View and export system reports.</p>
            <a href="reports.php" class="btn">Go</a>
        </div>

        <div class="card">
            <h3>Settings</h3>
            <p>Manage system configurations.</p>
            <a href="settings.php" class="btn">Go</a>
        </div>

        <div class="card">
            <h3>Logout</h3>
            <p>Sign out from the system.</p>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </section>
</main>
</div>
</body>
</html>
