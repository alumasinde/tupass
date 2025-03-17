<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2, 3, 4, 5])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access!']));
}

// CSRF Token Setup
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch settings from the database
$stmt = $pdo->prepare("SELECT * FROM system_settings LIMIT 1");
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Default values for missing settings
$defaults = [
    'company_name' => '',
    'company_email' => '',
    'company_address' => '',
    'company_phone' => '',
    'theme_color' => '#000000',
    'email_settings' => '',
    'sms_settings' => '',
    'logo' => ''
];

$settings = array_merge($defaults, $settings);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token!']);
        exit;
    }

    // Validate and Sanitize Inputs
    $company_name = htmlspecialchars(trim($_POST['company_name'] ?? ''));
    $company_email = filter_var($_POST['company_email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';
    $company_address = htmlspecialchars(trim($_POST['company_address'] ?? ''));
    $company_phone = preg_replace('/[^0-9+]/', '', $_POST['company_phone'] ?? '');
    $theme_color = htmlspecialchars($_POST['theme_color'] ?? '#000000');
    $email_settings = htmlspecialchars(trim($_POST['email_settings'] ?? ''));
    $sms_settings = htmlspecialchars(trim($_POST['sms_settings'] ?? ''));

    // Handle Logo Upload
    $logo_filename = $settings['logo']; // Keep existing logo
    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['logo']['tmp_name']);

        if (in_array($file_type, $allowed_types) && $_FILES['logo']['size'] <= 2 * 1024 * 1024) {
            $logo_filename = 'logo_' . time() . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $upload_dir = '../upload/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_filename);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid logo file type or file too large!']);
            exit;
        }
    }

    // Update settings in the database
    $stmt = $pdo->prepare("
        UPDATE system_settings SET
            company_name = ?, 
            company_email = ?, 
            company_address = ?, 
            company_phone = ?, 
            theme_color = ?, 
            email_settings = ?, 
            sms_settings = ?, 
            logo = ?
        WHERE id = 1
    ");
    $stmt->execute([
        $company_name,
        $company_email,
        $company_address,
        $company_phone,
        $theme_color,
        $email_settings,
        $sms_settings,
        $logo_filename
    ]);

    echo json_encode(['success' => true, 'message' => 'Settings Updated Successfully!', 'logo' => $logo_filename]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="styles/css/settings.css">

</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2 class="text-center mt-5">System Settings</h2>

        <div id="message-box" class="alert text-center d-none"></div>

        <form id="settings-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <label class="form-label">Company Name:</label>
            <input type="text" name="company_name" class="form-control" 
                   value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Company Email:</label>
            <input type="email" name="company_email" class="form-control" 
                   value="<?php echo isset($_POST['company_email']) ? htmlspecialchars($_POST['company_email']) : ''; ?>" required>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label">Company Address:</label>
            <input type="text" name="company_address" class="form-control" 
                   value="<?php echo isset($_POST['company_address']) ? htmlspecialchars($_POST['company_address']) : ''; ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Company Phone:</label>
            <input type="text" name="company_phone" class="form-control" 
                   value="<?php echo isset($_POST['company_phone']) ? htmlspecialchars($_POST['company_phone']) : ''; ?>" required>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label">Theme Color:</label>
            <input type="color" name="theme_color" class="form-control" 
                   value="<?php echo isset($_POST['theme_color']) ? htmlspecialchars($_POST['theme_color']) : ''; ?>" required>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label">Logo:</label>
            <input type="file" name="logo" class="form-control">
            <div class="mt-2" id="logo-preview">
                <?php if ($settings['logo']): ?>
                    <img src="../upload/logos/<?= htmlspecialchars($settings['logo']); ?>" alt="Logo" width="100">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center">
        <button type="submit" class="btn btn-primary">Update Settings</button>
    </div>
</div>

    <script>
    $(document).ready(function () {
        $("#settings-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: "settings.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        $("#message-box").removeClass("d-none alert-danger").addClass("alert-success").text(response.message);
                        if (response.logo) {
                            $("#logo-preview").html('<img src="../upload/logos/' + response.logo + '" width="100">');
                        }
                    } else {
                        $("#message-box").removeClass("d-none alert-success").addClass("alert-danger").text(response.message);
                    }
                }
            });
        });
    });
    </script>
</body>
</html>