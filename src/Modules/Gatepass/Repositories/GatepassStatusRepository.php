<?php

namespace App\Modules\Gatepass\Repositories;

use App\Core\DB;
use PDO;

class GatepassStatusRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connect();
    }

    /**
     * Get status ID by CODE (preferred)
     */
    public function getIdByCode(int $tenantId, string $code): ?int
    {
        $stmt = $this->db->prepare("
            SELECT id
            FROM gatepass_statuses
            WHERE tenant_id = ?
              AND code = ?
            LIMIT 1
        ");

        $stmt->execute([$tenantId, strtolower($code)]);

        $id = $stmt->fetchColumn();

        return $id !== false ? (int)$id : null;
    }

    /**
     * Strict version (throws if not found)
     */
    public function requireIdByCode(int $tenantId, string $code): int
    {
        $id = $this->getIdByCode($tenantId, $code);

        if ($id === null) {
            throw new \RuntimeException(
                "Gatepass status code '{$code}' not found for tenant {$tenantId}"
            );
        }

        return $id;
    }

    /**
     * Check if given status ID is APPROVED
     */
    public function isApproved(int $tenantId, int $statusId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM gatepass_statuses
            WHERE id = ?
              AND tenant_id = ?
              AND code = 'approved'
            LIMIT 1
        ");

        $stmt->execute([$statusId, $tenantId]);

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Check if REJECTED
     */
    public function isRejected(int $tenantId, int $statusId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM gatepass_statuses
            WHERE id = ?
              AND tenant_id = ?
              AND code = 'rejected'
            LIMIT 1
        ");

        $stmt->execute([$statusId, $tenantId]);

        return (int)$stmt->fetchColumn() > 0;
    }
}