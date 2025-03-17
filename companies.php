<?php
session_start();
include 'db.php';

// Handle Add Company
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_company'])) {
    $company_name = htmlspecialchars($_POST['company_name']);

    // Check if company exists
    $check = $pdo->prepare("SELECT id FROM companies WHERE name = :name");
    $check->execute([':name' => $company_name]);

    if ($check->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO companies (name) VALUES (:name)");
        $stmt->execute([':name' => $company_name]);
        echo json_encode(["success" => true, "message" => "Company added successfully!", "id" => $pdo->lastInsertId()]);
    } else {
        echo json_encode(["success" => false, "message" => "Company already exists!"]);
    }
    exit;
}

// Fetch all companies
$companies = $pdo->query("SELECT * FROM companies ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Companies</title>
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
        <h2>Manage Companies</h2>

        <!-- Add Company Form -->
        <form id="addCompanyForm">
            <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Company Name" required>
            <button type="submit" class="btn btn-primary mt-2">Add Company</button>
        </form>

        <div id="alert-message" class="mt-3"></div>

        <!-- Companies Table -->
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Company Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="companiesTable">
                <?php foreach ($companies as $company) { ?>
                <tr id="company_<?= $company['id']; ?>">
                    <td><?= $company['id']; ?></td>
                    <td class="company-name"><?= htmlspecialchars($company['name']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-company" data-id="<?= $company['id']; ?>" data-name="<?= htmlspecialchars($company['name']); ?>">Edit</button>
                        <button class="btn btn-danger btn-sm delete-company" data-id="<?= $company['id']; ?>">Delete</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Company Modal -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="editCompanyLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCompanyLabel">Edit Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCompanyForm">
                    <input type="hidden" id="edit_company_id">
                    <div class="mb-3">
                        <label for="edit_company_name" class="form-label">Company Name:</label>
                        <input type="text" id="edit_company_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCompanyModal" tabindex="-1" aria-labelledby="deleteCompanyLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCompanyLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this company?
                <input type="hidden" id="delete_company_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="confirmDeleteCompany" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Add Company
    $("#addCompanyForm").submit(function (e) {
        e.preventDefault();
        $.post("companies.php", { add_company: true, company_name: $("#company_name").val() }, function (response) {
            let res = JSON.parse(response);
            if (res.success) {
                $("#companiesTable").append(`
                    <tr id="company_${res.id}">
                        <td>${res.id}</td>
                        <td class="company-name">${$("#company_name").val()}</td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-company" data-id="${res.id}" data-name="${$("#company_name").val()}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-company" data-id="${res.id}">Delete</button>
                        </td>
                    </tr>
                `);
                $("#company_name").val("");
                $("#alert-message").html('<div class="alert alert-success">Company added successfully!</div>');
                setTimeout(() => { $("#alert-message").html(""); }, 3000);
            }
        });
    });

    // Open Edit Modal
    $(document).on("click", ".edit-company", function () {
        $("#edit_company_id").val($(this).data("id"));
        $("#edit_company_name").val($(this).data("name"));
        $("#editCompanyModal").modal("show");
    });

    // Save Edited Company
    $("#editCompanyForm").submit(function (e) {
        e.preventDefault();
        let companyId = $("#edit_company_id").val();
        let companyName = $("#edit_company_name").val();

        $.post("update_company.php", { id: companyId, company_name: companyName }, function (response) {
            let res = JSON.parse(response);
            if (res.success) {
                $(`#company_${companyId} .company-name`).text(companyName);
                $("#editCompanyModal").modal("hide");
            }
        });
    });

    // Open Delete Modal
    $(document).on("click", ".delete-company", function () {
        $("#delete_company_id").val($(this).data("id"));
        $("#deleteCompanyModal").modal("show");
    });

    // Confirm Delete Company
    $("#confirmDeleteCompany").click(function () {
        let companyId = $("#delete_company_id").val();

        $.post("delete_company.php", { id: companyId }, function (response) {
            let res = JSON.parse(response);
            if (res.success) {
                $(`#company_${companyId}`).remove();
                $("#deleteCompanyModal").modal("hide");
            }
        });
    });
});
</script>

</body>
</html>
