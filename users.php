<?php
session_start();
include 'db.php';

// Ensure user is logged in and has Admin or HOD access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
    die("Unauthorized access!");
}

// Fetch all users
$users = $pdo->query("
    SELECT users.id, users.employee_id, users.username, users.first_name, users.last_name, users.email, 
           users.role_id, users.department_id, roles.role_name, departments.dep_name
    FROM users
    LEFT JOIN roles ON users.role_id = roles.id
    LEFT JOIN departments ON users.department_id = departments.id
    ORDER BY users.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all roles
$roles = $pdo->query("SELECT id, role_name FROM roles")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all departments
$departments = $pdo->query("SELECT id, dep_name FROM departments")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/css/users.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content container mt-4">
    <h2 class="text-center">Manage Users</h2>

    <!-- Success message -->
    <div id="success-message" class="alert alert-success text-center d-none"></div>

    <!-- Add User Form -->
    <div class="card p-3 mt-4">
        <h4>Add User</h4>
        <form id="addUserForm">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Employee ID:</label>
                    <input type="text" name="employee_id" id="employee_id" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username:</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">First Name:</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name:</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role:</label>
                    <select name="role_id" id="role_id" class="form-control" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id']; ?>"><?= htmlspecialchars($role['role_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Department:</label>
                    <select name="department_id" id="department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= $department['id']; ?>"><?= htmlspecialchars($department['dep_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Add User</button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="card p-3 mt-4">
        <h4>User List</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTable">
                <?php foreach ($users as $user): ?>
                <tr id="userRow<?= $user['id']; ?>">
                    <td><?= $user['id']; ?></td>
                    <td><?= $user['employee_id']; ?></td>
                    <td><?= $user['username']; ?></td>
                    <td><?= $user['first_name'] . ' ' . $user['last_name']; ?></td>
                    <td><?= $user['email']; ?></td>
                    <td><?= $user['role_name']; ?></td>
                    <td><?= $user['dep_name']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-edit" data-id="<?= $user['id']; ?>">Edit</button>
                        <button class="btn btn-danger btn-delete" data-id="<?= $user['id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap Modals -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="edit_user_id">
                    <div class="mb-3">
                        <label>Username:</label>
                        <input type="text" id="edit_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email:</label>
                        <input type="email" id="edit_email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Add User AJAX
    $("#addUserForm").submit(function (e) {
        e.preventDefault();
        $.post("add_user.php", $(this).serialize(), function (response) {
            $("#success-message").text(response).removeClass("d-none");
            location.reload();
        });
    });

    // Edit User
    $(".btn-edit").click(function () {
        $("#edit_user_id").val($(this).data("id"));
        $("#editUserModal").modal("show");
    });

    $("#editUserForm").submit(function (e) {
        e.preventDefault();
        $.post("update_user.php", $(this).serialize(), function (response) {
            $("#success-message").text(response).removeClass("d-none");
            $("#editUserModal").modal("hide");
            location.reload();
        });
    });

    // Delete User
    $(".btn-delete").click(function () {
        $.post("delete_user.php", { user_id: $(this).data("id") }, function (response) {
            location.reload();
        });
    });
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
