<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Generate Next Gate Pass Number
$new_pass_no = 'GP001';
$stmt = $pdo->query("SELECT pass_no FROM gate_pass ORDER BY id DESC LIMIT 1");
$last_pass = $stmt->fetchColumn();

if ($last_pass) {
    preg_match('/GP(\d+)/', $last_pass, $matches);
    $next_number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
} else {
    $next_number = 1;
}

$new_pass_no = 'GP' . str_pad($next_number, 3, '0', STR_PAD_LEFT);

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    $pass_no = $new_pass_no;
    $date = $_POST['date'];
    $taken_by = htmlspecialchars($_POST['taken_by']);
    $company = htmlspecialchars($_POST['company']);
    $department_id = (int)$_POST['department'];
    $reason = htmlspecialchars($_POST['reason']);
    $expected_return_date = !empty($_POST['expected_return_date']) ? $_POST['expected_return_date'] : NULL;

    $stmt = $pdo->prepare("INSERT INTO gate_pass (pass_no, date, taken_by, company, department_id, reason, expected_return_date) 
                           VALUES (:pass_no, :date, :taken_by, :company, :department_id, :reason, :expected_return_date)");
    $stmt->execute([
        ':pass_no' => $pass_no,
        ':date' => $date,
        ':taken_by' => $taken_by,
        ':company' => $company,
        ':department_id' => $department_id,
        ':reason' => $reason,
        ':expected_return_date' => $expected_return_date
    ]);

    $gate_pass_id = $pdo->lastInsertId();

    if (!empty($_POST['item_name'])) {
        foreach ($_POST['item_name'] as $index => $item) {
            $stmt = $pdo->prepare("INSERT INTO materials (gate_pass_id, item_name, quantity, serial_no, returnable) 
                                   VALUES (:gate_pass_id, :item_name, :quantity, :serial_no, :returnable)");
            $stmt->execute([
                ':gate_pass_id' => $gate_pass_id,
                ':item_name' => htmlspecialchars($item),
                ':quantity' => (int)$_POST['quantity'][$index],
                ':serial_no' => !empty($_POST['serial_no'][$index]) ? htmlspecialchars($_POST['serial_no'][$index]) : NULL,
                ':returnable' => $_POST['returnable'][$index]
            ]);
        }
    }

    echo "<script>alert('Gate Pass Created Successfully!'); window.location.href='manage_gatepass.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/css/gate_pass.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h2 class="text-center">Gate Pass Form</h2>
    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Pass No:</label>
                <input type="text" class="form-control" value="<?= $new_pass_no; ?>" readonly required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Date:</label>
                <input type="date" name="date" class="form-control" required>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label">Taken By (Name):</label>
                <input type="text" id="taken_by" name="taken_by" class="form-control" required autocomplete="off">
                <div id="nameSuggestions" class="suggestions-list"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Company:</label>
                <input type="text" id="company" name="company" class="form-control" required autocomplete="off">
                <div id="companySuggestions" class="suggestions-list"></div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label">Department:</label>
                <select name="department" class="form-control" required>
                    <option value="">Select Department</option>
                    <?php
                    $stmt = $pdo->query("SELECT id, dep_name FROM departments");
                    while ($row = $stmt->fetch()) {
                        echo "<option value='{$row['id']}'>{$row['dep_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Reason for Removal/Disposal:</label>
                <textarea name="reason" class="form-control" required></textarea>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Material Details:</label>
            <table class="table table-bordered" id="materialsTable">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Serial No.</th>
                        <th>Returnable</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="item_name[]" class="form-control" required></td>
                        <td><input type="number" name="quantity[]" class="form-control" required></td>
                        <td><input type="text" name="serial_no[]" class="form-control"></td>
                        <td>
                            <select name="returnable[]" class="form-control">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </td>
                        <td><button type="button" class="btn btn-danger removeRow">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-secondary" id="addRow">Add More</button>
        </div>

        <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Expected Date of Return:</label>
                    <input type="date" name="expected_return_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Approved By (HOD's Name):</label>
                    <input type="text" name="approved_by" class="form-control" readonly>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Time Out:</label>
                    <input type="time" name="time_out" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date Out:</label>
                    <input type="date" name="date_out" class="form-control" required>
                </div>
            </div>

            <div class="mt-3 text-center">
                <button type="submit" class="btn btn-primary">Submit Gate Pass</button>
            </div>
        </form>
    </div>
<script type="text/javascript" src="script.js"></script>
<script>
$(document).on("click", ".removeRow", function () {
    $(this).closest("tr").remove();
});
</script>
</body>
</html>
