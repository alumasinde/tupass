<?php

namespace App\Modules\Tenant\Repositories;

use App\Core\DB;
use PDO;

class TenantRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connect();
    }

    public function findActiveByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, code
            FROM tenants
            WHERE code = :code
              AND is_active = 1
            LIMIT 1
        ");

        $stmt->execute([
            ':code' => $code
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}