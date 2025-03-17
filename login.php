<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password, role_id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store user details in session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['employee_id'] = $user['employee_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];

        // Redirect user based on their role (you can customize based on your needs)
        if ($_SESSION['role_id'] == 1) { // Assuming 1 is for Admin
            $_SESSION['role'] = 'Admin';
            header("Location: dashboard_stats.php");
        } elseif ($_SESSION['role_id'] == 2) { // Assuming 2 is for HOD
            $_SESSION['role'] = 'HOD';
            header("Location: dashboard_stats.php");
        } else {
            $_SESSION['role'] = 'Employee'; // or another role based on your system
            header("Location: dashboard_stats.php");
        }
        exit;
    } else {
        // Show error message if login fails
        echo "<script>alert('Invalid credentials!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/css//login.css">
</head>
<body>
    <div class="main-content">
        <h2 class="text-center">Login</h2>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <div class="mt-3 text-center">
                <a href="register.php">Register</a> | <a href="reset_password.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>
</html>
