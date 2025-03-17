<?php
session_start();
include 'db.php';

// Fetch Visitors
$visitors = $pdo->query("SELECT * FROM visitors ORDER BY date_time DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Companies for dropdown
$companies = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visitor Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles/css/styles.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container mt-4">
        <h2>Manage Visitors</h2>

        <!-- Add Visitor Form -->
        <form id="addVisitorForm">
            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" required>
            <input type="text" name="last_name" id="last_name" class="form-control mt-2" placeholder="Last Name" required>
            <select name="company_id" id="company_id" class="form-control mt-2">
                <option value="">Select Company (Optional)</option>
                <?php foreach ($companies as $company) { ?>
                    <option value="<?= $company['id']; ?>"><?= htmlspecialchars($company['name']); ?></option>
                <?php } ?>
            </select>
            <textarea name="purpose" id="purpose" class="form-control mt-2" placeholder="Purpose of Visit" required></textarea>
            <button type="submit" class="btn btn-primary mt-2">Add Visitor</button>
        </form>

        <div id="alert-message" class="mt-3"></div>

        <!-- Visitors Table -->
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Company</th>
                    <th>Purpose</th>
                    <th>Date/Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="visitorsTable">
                <?php foreach ($visitors as $visitor) { ?>
                <tr id="visitor_<?= $visitor['id']; ?>">
                    <td><?= $visitor['id']; ?></td>
                    <td class="visitor-first"><?= htmlspecialchars($visitor['first_name']); ?></td>
                    <td class="visitor-last"><?= htmlspecialchars($visitor['last_name']); ?></td>
                    <td class="visitor-company"><?= htmlspecialchars($visitor['company'] ?: 'N/A'); ?></td>
                    <td class="visitor-purpose"><?= htmlspecialchars($visitor['purpose']); ?></td>
                    <td><?= $visitor['date_time']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-visitor" data-id="<?= $visitor['id']; ?>" data-first="<?= htmlspecialchars($visitor['first_name']); ?>" data-last="<?= htmlspecialchars($visitor['last_name']); ?>" data-company="<?= htmlspecialchars($visitor['company']); ?>" data-purpose="<?= htmlspecialchars($visitor['purpose']); ?>">Edit</button>
                        <button class="btn btn-danger btn-sm delete-visitor" data-id="<?= $visitor['id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Visitor Modal -->
<div class="modal fade" id="editVisitorModal" tabindex="-1" aria-labelledby="editVisitorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVisitorLabel">Edit Visitor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editVisitorForm">
                    <input type="hidden" id="edit_visitor_id">
                    <div class="mb-3">
                        <label for="edit_first_name" class="form-label">First Name:</label>
                        <input type="text" id="edit_first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_last_name" class="form-label">Last Name:</label>
                        <input type="text" id="edit_last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_purpose" class="form-label">Purpose:</label>
                        <textarea id="edit_purpose" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteVisitorModal" tabindex="-1" aria-labelledby="deleteVisitorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteVisitorLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this visitor?
                <input type="hidden" id="delete_visitor_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmDeleteVisitor" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Add Visitor
    $("#addVisitorForm").submit(function (e) {
        e.preventDefault();
        $.post("add_visitor.php", {
            first_name: $("#first_name").val(),
            last_name: $("#last_name").val(),
            company_id: $("#company_id").val(),
            purpose: $("#purpose").val()
        }, function (response) {
            let res = JSON.parse(response);
            if (res.success) {
                $("#visitorsTable").prepend(`
                    <tr id="visitor_${res.id}">
                        <td>${res.id}</td>
                        <td class="visitor-first">${$("#first_name").val()}</td>
                        <td class="visitor-last">${$("#last_name").val()}</td>
                        <td class="visitor-company">${$("#company_id option:selected").text()}</td>
                        <td class="visitor-purpose">${$("#purpose").val()}</td>
                        <td>${res.date_time}</td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-visitor" data-id="${res.id}" data-first="${$("#first_name").val()}" data-last="${$("#last_name").val()}" data-company="${$("#company_id option:selected").text()}" data-purpose="${$("#purpose").val()}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-visitor" data-id="${res.id}">Delete</button>
                        </td>
                    </tr>
                `);
                $("#first_name, #last_name, #purpose").val("");
                $("#alert-message").html('<div class="alert alert-success">Visitor added successfully!</div>');
                setTimeout(() => { $("#alert-message").html(""); }, 3000);
            }
        });
    });
});
</script>

</body>
</html>
