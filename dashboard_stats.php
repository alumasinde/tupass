<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Fetch user details
$stmt = $pdo->prepare("SELECT first_name, last_name, role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ensure first_name and last_name exist before using them
$first_name = isset($user['first_name']) ? htmlspecialchars($user['first_name']) : "User";
$last_name = isset($user['last_name']) ? htmlspecialchars($user['last_name']) : "";
?>

<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- FontAwesome for icons -->
<link rel="stylesheet" href="styles/css/dashboard_stats.css">

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <main class="dashboard-container">
        <section class="stats-section">
            <div class="stats-grid">
                <div class="card">
                    <i class="fas fa-clipboard-list fa-3x"></i>
                    <h3>Total Gate Passes</h3>
                    <p id="totalGatePasses">0</p>
                </div>

                <div class="card">
                    <i class="fas fa-check-circle fa-3x"></i>
                    <h3>Approved Passes</h3>
                    <p id="approvedGatePasses">0</p>
                </div>

                <div class="card">
                    <i class="fas fa-clock fa-3x"></i>
                    <h3>Pending Approvals</h3>
                    <p id="pendingPasses">0</p>
                </div>

                <div class="card">
                    <i class="fas fa-times-circle fa-3x"></i>
                    <h3>Rejected Passes</h3>
                    <p id="rejectedPasses">0</p>
                </div>

                <div class="card">
                    <i class="fas fa-sign-out-alt fa-3x"></i>
                    <h3>Pending Check-Outs</h3>
                    <p id="pendingCheckOuts">0</p>
                </div>

                <div class="card">
                    <i class="fas fa-sign-in-alt fa-3x"></i>
                    <h3>Pending Check-Ins</h3>
                    <p id="pendingCheckIns">0</p>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
function fetchStats() {
    $.ajax({
        url: 'fetch_stats.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#totalGatePasses').text(data.totalGatePasses);
            $('#approvedGatePasses').text(data.totalApprovedPasses);
            $('#pendingPasses').text(data.totalPendingPasses);
            $('#rejectedPasses').text(data.totalRejectedPasses);
            $('#pendingCheckOuts').text(data.totalPendingCheckOuts);
            $('#pendingCheckIns').text(data.totalPendingCheckIns);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching stats:', error);
        }
    });
}

// Fetch stats every 30 seconds
setInterval(fetchStats, 30000);
fetchStats();
</script>

</body>
</html>
