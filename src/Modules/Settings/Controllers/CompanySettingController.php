<?php

namespace App\Modules\Settings\Controllers;

use App\Core\DB;
use App\Core\Request;
use App\Core\View;
use PDO;

class CompanySettingController
{
    private PDO $db;
    private int $tenantId;

    public function __construct()
    {
        if (!isset($_SESSION['user']['tenant_id'])) {
            header('Location: /login');
            exit;
        }

        $this->tenantId = (int) $_SESSION['user']['tenant_id'];
        $this->db = DB::connect();
    }

    /**
     * Show company settings page
     */
    public function index()
    {
        $stmt = $this->db->prepare("
            SELECT name, email, code,phone, country
            FROM tenants
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([':id' => $this->tenantId]);

        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        return View::render('Settings::company', [
            'title'   => 'Company Settings',
            'company' => $company ?: []
        ], 'app');
    }

    /**
     * Update company settings
     */
    public function update(Request $request)
    {
        $stmt = $this->db->prepare("
            UPDATE tenants
            SET name    = :name,
                email   = :email,
                code    = :code,
                phone   = :phone,
                country = :country,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $stmt->execute([
            ':name'    => trim($request->input('company_name') ?? ''),
            ':email'   => trim($request->input('email') ?? ''),
            ':code'   => trim($request->input('code') ?? ''),
            ':phone'   => trim($request->input('phone') ?? ''),
            ':country' => trim($request->input('country') ?? ''),
            ':id'      => $this->tenantId,
        ]);

        header('Location: /settings/company');
        exit;
    }
}