<?php

namespace App\Modules\Settings\Controllers;

use App\Core\DB;
use App\Core\Request;
use App\Core\View;
use PDO;

class UserManagementController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connect();
    }

    private function user(): array
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        return $_SESSION['user'];
    }

    /**
     * List users
     */
    public function index()
    {
        $user = $this->user();
        $tenantId = $user['tenant_id'];

        $stmt = $this->db->prepare("
            SELECT u.*,
                   GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.tenant_id = :tenant_id
            GROUP BY u.id
            ORDER BY u.id DESC
        ");

        $stmt->execute([':tenant_id' => $tenantId]);

        return View::render('Settings::users.index', [
            'title' => 'User Management',
            'users' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ], 'app');
    }

    /**
     * Show create form
     */
    public function create()
    {
        $user = $this->user();
        $tenantId = $user['tenant_id'];

        $stmt = $this->db->prepare("
            SELECT id, name
            FROM roles
            WHERE tenant_id = :tenant_id
        ");

        $stmt->execute([':tenant_id' => $tenantId]);

        return View::render('Settings::users.create', [
            'title' => 'Create User',
            'roles' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ], 'app');
    }

    /**
     * Store user
     */
    public function store(Request $request)
    {
        $user = $this->user();
        $tenantId = $user['tenant_id'];

        $this->db->beginTransaction();

        $stmt = $this->db->prepare("
            INSERT INTO users
            (tenant_id, email, password_hash, first_name, last_name, username)
            VALUES
            (:tenant_id, :email, :password, :first_name, :last_name, :username)
        ");

        $stmt->execute([
            ':tenant_id' => $tenantId,
            ':email' => $request->input('email'),
            ':password' => password_hash($request->input('password'), PASSWORD_DEFAULT),
            ':first_name' => $request->input('first_name'),
            ':last_name' => $request->input('last_name'),
            ':username' => $request->input('username'),
        ]);

        $userId = (int) $this->db->lastInsertId();

        $roles = $request->input('roles') ?? [];

        if (!empty($roles)) {
            $roleStmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id)
                VALUES (:user_id, :role_id)
            ");

            foreach ($roles as $roleId) {
                $roleStmt->execute([
                    ':user_id' => $userId,
                    ':role_id' => $roleId
                ]);
            }
        }

        $this->db->commit();

        header("Location: /settings/users");
        exit;
    }

    public function edit(Request $request, int $id)
    {
        $user = $this->user(); // current logged-in user
        $tenantId = $user['tenant_id'];

        // Fetch user to edit
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE id = :id
              AND tenant_id = :tenant_id
            LIMIT 1
        ");
        $stmt->execute([
            ':id' => $id,
            ':tenant_id' => $tenantId
        ]);
        $editUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editUser) {
            header("Location: /settings/users");
            exit;
        }

        // Fetch tenant roles
        $rolesStmt = $this->db->prepare("
            SELECT id, name
            FROM roles
            WHERE tenant_id = :tenant_id
        ");
        $rolesStmt->execute([':tenant_id' => $tenantId]);
        $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch current roles for user
        $userRolesStmt = $this->db->prepare("
            SELECT role_id
            FROM user_roles
            WHERE user_id = :user_id
              AND tenant_id = :tenant_id
        ");
        $userRolesStmt->execute([
            ':user_id' => $id,
            ':tenant_id' => $tenantId
        ]);
        $userRoles = array_column($userRolesStmt->fetchAll(PDO::FETCH_ASSOC), 'role_id');

        return View::render('Settings::users.edit', [
            'title' => 'Edit User',
            'userData' => $editUser,
            'roles' => $roles,
            'userRoles' => $userRoles
        ], 'app');
    }

    // Handle form submission
    public function update(Request $request, int $id)
    {
        $user = $this->user(); // current logged-in user
        $tenantId = $user['tenant_id'];

        // Get submitted data
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $roleIds = $request->input('roles', []); // array of role IDs

        // Update user
        $stmt = $this->db->prepare("
            UPDATE users
            SET first_name = :first_name,
                last_name  = :last_name,
                email      = :email
            WHERE id = :id
              AND tenant_id = :tenant_id
        ");
        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':id' => $id,
            ':tenant_id' => $tenantId
        ]);

        // Update roles
        // Remove old roles
        $stmt = $this->db->prepare("
            DELETE FROM user_roles
            WHERE user_id = :user_id
              AND tenant_id = :tenant_id
        ");
        $stmt->execute([
            ':user_id' => $id,
            ':tenant_id' => $tenantId
        ]);

        // Assign new roles
        if (!empty($roleIds)) {
            $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id, tenant_id)
                VALUES (:user_id, :role_id, :tenant_id)
            ");
            foreach ($roleIds as $roleId) {
                $stmt->execute([
                    ':user_id' => $id,
                    ':role_id' => $roleId,
                    ':tenant_id' => $tenantId
                ]);
            }
        }

        // Redirect back
        header("Location: /settings/users/{$id}/edit");
        exit;
    }


    //Show User Profile page with option to change password
    public function profile()
    {
        $user = $this->user();

        return View::render('Settings::users.profile', [
            'title' => 'My Profile',
            'user' => $user
        ], 'app');
    }
    public function updateProfile(Request $request)
    {
        $user = $this->user();
        $tenantId = $user['tenant_id'];
        $userId = $user['id'];

        $firstName = trim((string) $request->input('first_name'));
        $lastName = trim((string) $request->input('last_name'));
        $email = trim((string) $request->input('email'));

        $currentPassword = (string) $request->input('current_password');
        $newPassword = (string) $request->input('new_password');
        $confirmPassword = (string) $request->input('confirm_password');

        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return View::render('Settings::users.profile', [
                'title' => 'My Profile',
                'user' => $user,
                'error' => 'Invalid email address.'
            ], 'app');
        }

        try {
            $this->db->beginTransaction();

            // Update profile fields
            $stmt = $this->db->prepare("
            UPDATE users
            SET first_name = :first_name,
                last_name  = :last_name,
                email      = :email
            WHERE id = :id
              AND tenant_id = :tenant_id
        ");

            $stmt->execute([
                ':first_name' => $firstName,
                ':last_name' => $lastName,
                ':email' => $email,
                ':id' => $userId,
                ':tenant_id' => $tenantId
            ]);

            // Handle password change if provided
            if (!empty($newPassword)) {

                if ($newPassword !== $confirmPassword) {
                    throw new \RuntimeException('New passwords do not match.');
                }

                // Fetch current password hash
                $stmt = $this->db->prepare("
                SELECT password_hash
                FROM users
                WHERE id = :id
                  AND tenant_id = :tenant_id
                LIMIT 1
            ");

                $stmt->execute([
                    ':id' => $userId,
                    ':tenant_id' => $tenantId
                ]);

                $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$dbUser || !password_verify($currentPassword, $dbUser['password_hash'])) {
                    throw new \RuntimeException('Current password is incorrect.');
                }

                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

                $stmt = $this->db->prepare("
                UPDATE users
                SET password_hash = :password
                WHERE id = :id
                  AND tenant_id = :tenant_id
            ");

                $stmt->execute([
                    ':password' => $newHash,
                    ':id' => $userId,
                    ':tenant_id' => $tenantId
                ]);
            }

            $this->db->commit();

            // Update session values
            $_SESSION['user']['first_name'] = $firstName;
            $_SESSION['user']['last_name'] = $lastName;
            $_SESSION['user']['email'] = $email;

            header("Location: /settings/users/profile");
            exit;

        } catch (\Throwable $e) {

            $this->db->rollBack();

            return View::render('Settings::users.profile', [
                'title' => 'My Profile',
                'user' => $user,
                'error' => $e->getMessage()
            ], 'app');
        }
    }
}