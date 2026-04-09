<?php

namespace App\Modules\Auth\Controllers;

use App\Modules\Auth\Services\AuthService;
use App\Modules\Tenant\Repositories\TenantRepository;
use App\Core\Controller;
use App\Core\View;
use App\Core\Request;
use RuntimeException;

class LoginController extends Controller
{
    private AuthService $auth;

    public function __construct(?AuthService $auth = null)
    {
        $this->auth = $auth ?? new AuthService();
    }

 public function index()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!empty($_SESSION['user'])) {
        header('Location: /dashboard');
        exit;
    }

    try {
        $tenantId = $this->resolveTenantId();
        $tenant = $this->auth->getTenantContext($tenantId);

    } catch (\Throwable $e) {
        error_log($e->getMessage());
        http_response_code(400);
        exit('Invalid tenant');
    }

    return View::render(
        'Auth::login',
        [
            'title'        => 'Login',
            'csrf_token'   => $this->generateCsrfToken(),
            'company_logo' => $tenant['tenant_logo'],
            'tenant_name'  => $tenant['tenant_name'],
        ],
        'guest'
    );
}
    public function store(Request $request)
    {
        $this->assertValidCsrf($request->input('csrf_token'));

        // Tenant MUST come from trusted context (e.g., subdomain resolver)
        $tenantId = $this->resolveTenantId();

        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');

        if (!$this->isValidEmail($email) || empty($password)) {
            return $this->errorResponse('Invalid credentials.');
        }

        if (!$this->auth->attempt($tenantId, $email, $password)) {
            // TODO: hook rate limiter here
            return $this->errorResponse('Invalid credentials.');
        }

        header('Location: /dashboard');
        exit;
    }

 private function errorResponse(string $message)
{
    try {
        $tenantId = $this->resolveTenantId();
        $tenant = $this->auth->getTenantContext($tenantId);

    } catch (\Throwable $e) {
        $tenant = [
            'tenant_name'  => 'Company',
            'tenant_logo'  => null,
        ];
    }

    return View::render(
        'Auth::login',
        [
            'title'        => 'Login',
            'error'        => $message,
            'csrf_token'   => $this->generateCsrfToken(),
            'company_logo' => $tenant['tenant_logo'],
            'tenant_name'  => $tenant['tenant_name'],
        ],
        'guest'
    );
}

    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /*
    |--------------------------------------------------------------------------
    | CSRF
    |--------------------------------------------------------------------------
    */

    private function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    private function assertValidCsrf(?string $token): void
    {
        if (
            empty($token) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            throw new RuntimeException('Invalid CSRF token.');
        }
    }



    /* |--------------------------------------------------------------------------
     | Tenant Resolution
     private function resolveTenantId(): int
 {
     // Local development override
     return 1;
 }
 */
    private function resolveTenantId(): int
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $parts = explode('.', $host);

        // Handle localhost & dev environments
        if ($host === 'localhost' || count($parts) < 2) {
            // Option 1: fallback tenant (dev only)
            return $this->getDefaultTenantId();
        }

        // Handle tenant.localhost OR tenant.domain.com
        $subdomain = $parts[0];

        // Prevent "www" being treated as tenant
        if ($subdomain === 'www') {
            throw new RuntimeException('Tenant subdomain missing.');
        }

        $tenantRepo = new TenantRepository();
        $tenant = $tenantRepo->findActiveByCode($subdomain);

        if (!$tenant) {
            throw new RuntimeException('Invalid tenant.');
        }

        return (int) $tenant['id'];
    }

    private function getDefaultTenantId(): int
    {
        $tenantRepo = new TenantRepository();
        $tenant = $tenantRepo->findActiveByCode('default'); // or 'tenant1'

        if (!$tenant) {
            throw new RuntimeException('Default tenant not configured.');
        }

        return (int) $tenant['id'];
    }
}