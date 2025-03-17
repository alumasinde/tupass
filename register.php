<?php
include 'db.php';

// Fetch departments from the database
$departments_stmt = $pdo->query("SELECT id, dep_name FROM departments");
$departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch roles from the database
$roles_stmt = $pdo->query("SELECT id, role_name FROM roles");
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $department_id = $_POST['department_id'];
    $role_id = $_POST['role_id'];

    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, username, password, department_id, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $username, $password, $department_id, $role_id]);

    echo "Registration successful! <a href='login.php'>Login here</a>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/css/register.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Register</h2>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label">First Name:</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Last Name:</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Fetch Departments from Database -->
            <div class="mb-3">
                <label class="form-label">Department:</label>
                <select name="department_id" class="form-control" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id']; ?>"><?= htmlspecialchars($dept['dep_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Fetch Roles from Database -->
            <div class="mb-3">
                <label class="form-label">Role:</label>
                <select name="role_id" class="form-control" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id']; ?>"><?= htmlspecialchars($role['role_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
            <div class="mt-3 text-center">
                <a href="login.php">Already Registered? Please Login</a>
            </div>
        </form>
    </div>
</body>
</html>
