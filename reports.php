<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Fetch departments for filtering
$departments = $pdo->query("SELECT DISTINCT dep_name FROM departments ORDER BY dep_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="reports.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <main class="dashboard-container">
        <h2>Reports</h2>

        <!-- Filters Section -->
        <div class="filters">
            <label for="dateFrom">From:</label>
            <input type="date" id="dateFrom">
            
            <label for="dateTo">To:</label>
            <input type="date" id="dateTo">

            <label for="status">Status:</label>
            <select id="status">
                <option value="">All</option>
                <option value="Approved">Approved</option>
                <option value="Pending">Pending</option>
                <option value="Rejected">Rejected</option>
                <option value="Checked Out">Checked Out</option>
                <option value="Checked In">Checked In</option>
            </select>

            <label for="department">Department:</label>
            <select id="dep_name">
                <option value="">All</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo htmlspecialchars($dept['dep_name']); ?>">
                        <?php echo htmlspecialchars($dept['dep_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button id="filterBtn"><i class="fas fa-filter"></i> Apply Filters</button>
        </div>

        <!-- Reports Table -->
        <div class="reports-table">
            <table>
                <thead>
                    <tr>
                        <th>Pass No</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Item(s)</th>
                        <th>Status</th>
                        <th>Date Out</th>
                        <th>Check-In Date</th>
                    </tr>
                </thead>
                <tbody id="reportsData">
                    <tr><td colspan="7">No data found.</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Export Buttons -->
        <div class="export-buttons">
            <button id="exportPDF"><i class="fas fa-file-pdf"></i> Export to PDF</button>
            <button id="exportExcel"><i class="fas fa-file-excel"></i> Export to Excel</button>
        </div>
    </main>
</div>

<script>
$(document).ready(function () {
    function fetchReports() {
        var dateFrom = $("#dateFrom").val();
        var dateTo = $("#dateTo").val();
        var status = $("#status").val();
        var department = $("#department").val();

        $.ajax({
            url: 'fetch_reports.php',
            method: 'GET',
            data: { dateFrom, dateTo, status, department },
            success: function (response) {
                $("#reportsData").html(response);
            }
        });
    }

    $("#filterBtn").click(fetchReports);
    fetchReports();

    $("#exportPDF").click(function () {
        window.location.href = "export_reports.php?format=pdf";
    });

    $("#exportExcel").click(function () {
        window.location.href = "export_reports.php?format=excel";
    });
});
</script>

</body>
</html>
