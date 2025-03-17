<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $newPassword = substr(md5(time()), 0, 8); // Generate a random password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashedPassword, $username]);

        echo "<p class='text-success text-center'>New Password: <strong>$newPassword</strong></p>";
    } else {
        $error = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Reset Password</h2>
        <?php if (isset($error)) { echo "<p class='text-danger text-center'>$error</p>"; } ?>
        <form method="POST" class="mx-auto w-50">
            <div class="mb-3">
                <label class="form-label">Enter Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100">Reset Password</button>
            <p class="mt-3 text-center"><a href="login.php">Back to Login</a></p>
        </form>
    </div>
</body>
</html>
