<?php
session_start();
include 'db.php';

// Ensure user is logged in and has the correct role (Gate Man, Security, Admin, GM)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2, 3, 4, 5])) {
    die("Unauthorized access!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check In/Out</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/css/check_in_out.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="main-content container mt-4">
        <h2 class="text-center">Check-In / Check-Out</h2>

        <!-- Alert Messages -->
        <div id="message-box" class="alert text-center d-none"></div>

        <!-- Check-In/Out Form -->
        <div class="card p-3 mt-4">
            <form id="checkInOutForm">
                <div class="mb-3">
                    <label for="pass_no" class="form-label">Gate Pass Number:</label>
                    <input type="text" name="pass_no" id="pass_no" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Action:</label>
                    <select name="action" id="action" class="form-control" required>
                        <option value="check_out">Check Out</option>
                        <option value="check_in">Check In</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        $("#checkInOutForm").submit(function (e) {
            e.preventDefault();

            $.post("process_check_in_out.php", $(this).serialize(), function (response) {
                $("#message-box")
                    .removeClass("d-none alert-success alert-danger")
                    .addClass(response.success ? "alert-success" : "alert-danger")
                    .text(response.message);
            }, "json");
        });
    });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
