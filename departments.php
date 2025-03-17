<?php
session_start();
include 'db.php';

// Check if user has Admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    die("Access Denied.");
}

// Fetch all departments
$departments = $pdo->query("SELECT * FROM departments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/css/users.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h2 class="text-center">Manage Departments</h2>

    <!-- Success/Error Messages -->
    <div id="success-message" class="alert alert-success text-center d-none"></div>
    <div id="error-message" class="alert alert-danger text-center d-none"></div>

    <!-- Add Department Form -->
    <div class="card p-3 mt-4">
        <h4>Add Department</h4>
        <form id="addDepartmentForm">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="dep_name" id="dep_name" class="form-control" placeholder="Department Name" required>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">Add Department</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Departments Table -->
    <div class="card p-3 mt-4">
        <h4>Department List</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Department Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="departmentTable">
                <?php foreach ($departments as $dep): ?>
                <tr id="depRow<?= $dep['id']; ?>">
                    <td><?= $dep['id']; ?></td>
                    <td><?= htmlspecialchars($dep['dep_name']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-edit" data-id="<?= $dep['id']; ?>" data-name="<?= htmlspecialchars($dep['dep_name']); ?>">Edit</button>
                        <button class="btn btn-danger btn-delete" data-id="<?= $dep['id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap Modals -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editDepartmentForm">
                    <input type="hidden" id="edit_dep_id">
                    <div class="mb-3">
                        <label>Department Name:</label>
                        <input type="text" id="edit_dep_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Add Department AJAX
    $("#addDepartmentForm").submit(function (e) {
        e.preventDefault();
        $.post("add_department.php", $(this).serialize(), function (response) {
            if (response.success) {
                $("#success-message").text(response.message).removeClass("d-none");
                location.reload();
            } else {
                $("#error-message").text(response.message).removeClass("d-none");
            }
        }, "json");
    });

    // Edit Department
    $(".btn-edit").click(function () {
        $("#edit_dep_id").val($(this).data("id"));
        $("#edit_dep_name").val($(this).data("name"));
        $("#editDepartmentModal").modal("show");
    });

    $("#editDepartmentForm").submit(function (e) {
        e.preventDefault();
        $.post("update_department.php", $(this).serialize(), function (response) {
            if (response.success) {
                $("#success-message").text(response.message).removeClass("d-none");
                $("#editDepartmentModal").modal("hide");
                location.reload();
            } else {
                $("#error-message").text(response.message).removeClass("d-none");
            }
        }, "json");
    });

    // Delete Department
    $(".btn-delete").click(function () {
        let depId = $(this).data("id");
        if (confirm("Are you sure you want to delete this department?")) {
            $.post("delete_department.php", { dep_id: depId }, function (response) {
                if (response.success) {
                    $("#success-message").text(response.message).removeClass("d-none");
                    location.reload();
                } else {
                    $("#error-message").text(response.message).removeClass("d-none");
                }
            }, "json");
        }
    });
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
