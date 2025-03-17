<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access!");
}

// Fetch all gate passes pending approval
$stmt = $pdo->query("SELECT * FROM gate_pass WHERE status IN ('Pending', 'Security Approval', 'GM Approval')");
$gate_passes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass Approvals</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/css/approvals.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="main-content container mt-4">
        <h2 class="text-center">Gate Pass Approvals</h2>

        <!-- Success Message -->
        <div id="message-box" class="alert text-center d-none"></div>

        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Pass No</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($gate_passes) > 0): ?>
                    <?php foreach ($gate_passes as $pass): ?>
                        <tr id="row-<?= $pass['id']; ?>">
                            <td><?= htmlspecialchars($pass['pass_no']); ?></td>
                            <td><?= htmlspecialchars($pass['date']); ?></td>
                            <td>
                                <?php 
                                if ($pass['status'] === 'Pending') {
                                    echo "Pending Approval";
                                } elseif ($pass['status'] === 'Security Approval') {
                                    echo "Waiting for Security Approval";
                                } elseif ($pass['status'] === 'GM Approval') {
                                    echo "Waiting for GM Approval";
                                } elseif ($pass['status'] === 'Approved') {
                                    echo "Fully Approved";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($pass['status'] === 'Pending' && $_SESSION['role'] === 'HOD'): ?>
                                    <button class="btn btn-success btn-sm approve-btn" data-id="<?= $pass['id']; ?>">Approve</button>
                                <?php elseif ($pass['status'] === 'Security Approval' && $_SESSION['role'] === 'Security'): ?>
                                    <button class="btn btn-success btn-sm approve-btn" data-id="<?= $pass['id']; ?>">Approve</button>
                                <?php elseif ($pass['status'] === 'GM Approval' && $_SESSION['role'] === 'General Manager'): ?>
                                    <button class="btn btn-success btn-sm approve-btn" data-id="<?= $pass['id']; ?>">Approve</button>
                                <?php elseif ($_SESSION['role'] === 'Admin'): ?>
                                    <a href="view_gate_pass.php?id=<?= $pass['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No pending approvals.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Approval Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to approve this Gate Pass?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApprove">Yes, Approve</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        let approveId = null;

        $(".approve-btn").click(function () {
            approveId = $(this).data("id");
            $("#confirmModal").modal("show");
        });

        $("#confirmApprove").click(function () {
            if (approveId) {
                $.post("process_approval.php", { id: approveId }, function (response) {
                    if (response.success) {
                        $("#message-box")
                            .removeClass("d-none alert-danger")
                            .addClass("alert-success")
                            .text(response.message);
                        $("#row-" + approveId).remove();
                    } else {
                        $("#message-box")
                            .removeClass("d-none alert-success")
                            .addClass("alert-danger")
                            .text(response.message);
                    }
                    $("#confirmModal").modal("hide");
                }, "json");
            }
        });
    });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
