<?php
session_start();
include 'db.php';

// Handle Add Role
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_role'])) {
    $role_name = htmlspecialchars($_POST['role_name']);
    $stmt = $pdo->prepare("INSERT INTO roles (role_name) VALUES (:role_name)");
    $stmt->execute([':role_name' => $role_name]);
    echo json_encode(["success" => true, "message" => "Role added successfully!"]);
    exit;
}

// Fetch Roles
$roles = $pdo->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Roles</title>
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
        <h2>Manage Roles</h2>

        <!-- Add Role Form -->
        <form id="addRoleForm">
            <input type="text" name="role_name" id="role_name" class="form-control" placeholder="Role Name" required>
            <button type="submit" class="btn btn-primary mt-2">Add Role</button>
        </form>

        <div id="alert-message" class="mt-3"></div>

        <!-- Roles Table -->
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="rolesTable">
                <?php foreach ($roles as $role) { ?>
                <tr id="role_<?= $role['id']; ?>">
                    <td><?= $role['id']; ?></td>
                    <td class="role-name"><?= htmlspecialchars($role['role_name']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-role" data-id="<?= $role['id']; ?>" data-name="<?= htmlspecialchars($role['role_name']); ?>">Edit</button>
                        <button class="btn btn-danger btn-sm delete-role" data-id="<?= $role['id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleLabel">Edit Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editRoleForm">
                    <input type="hidden" id="edit_role_id">
                    <div class="mb-3">
                        <label for="edit_role_name" class="form-label">Role Name:</label>
                        <input type="text" id="edit_role_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRoleLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this role?
                <input type="hidden" id="delete_role_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmDeleteRole" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Add Role
    $("#addRoleForm").submit(function (e) {
        e.preventDefault();
        $.post("roles.php", { add_role: true, role_name: $("#role_name").val() }, function (response) {
            let res = JSON.parse(response);
            if (res.success) {
                $("#rolesTable").append(`
                    <tr id="role_${res.id}">
                        <td>${res.id}</td>
                        <td class="role-name">${$("#role_name").val()}</td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-role" data-id="${res.id}" data-name="${$("#role_name").val()}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-role" data-id="${res.id}">Delete</button>
                        </td>
                    </tr>
                `);
                $("#role_name").val("");
                $("#alert-message").html('<div class="alert alert-success">Role added successfully!</div>');
                setTimeout(() => { $("#alert-message").html(""); }, 3000);
            }
        });
    });

    // Open Edit Modal
    $(document).on("click", ".edit-role", function () {
        $("#edit_role_id").val($(this).data("id"));
        $("#edit_role_name").val($(this).data("name"));
        $("#editRoleModal").modal("show");
    });

    // Save Edited Role
    $("#editRoleForm").submit(function (e) {
        e.preventDefault();
        let roleId = $("#edit_role_id").val();
        let roleName = $("#edit_role_name").val();

        $.post("update_role.php", { id: roleId, role_name: roleName }, function (response) {
            let res = JSON.parse(response);
            if (res.success) {
                $(`#role_${roleId} .role-name`).text(roleName);
                $("#editRoleModal").modal("hide");
            }
        });
    });

    // Open Delete Modal
    $(document).on("click", ".delete-role", function () {
        $("#delete_role_id").val($(this).data("id"));
        $("#deleteRoleModal").modal("show");
    });

    // Confirm Delete Role
    $("#confirmDeleteRole").click(function () {
        let roleId = $("#delete_role_id").val();

        $.post("delete_role.php", { id: roleId }, function (response) {
            let res = JSON.parse(response);
            if (res.success) {
                $(`#role_${roleId}`).remove();
                $("#deleteRoleModal").modal("hide");
            }
        });
    });
});
</script>

</body>
</html>
