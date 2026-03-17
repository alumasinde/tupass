<?php

namespace App\Modules\Audit\Repositories;

use PDO;

class AuditLogRepository
{
    public function __construct(private PDO $db) {}

    public function log(
        int $tenantId,
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $metadata = null,
        ?string $ipAddress = null
    ): void {

        $stmt = $this->db->prepare("
            INSERT INTO audit_logs
            (tenant_id, user_id, action, entity_type, entity_id, metadata, ip_address)
            VALUES
            (:tenant_id, :user_id, :action, :entity_type, :entity_id, :metadata, :ip_address)
        ");

        $stmt->execute([
            ':tenant_id'   => $tenantId,
            ':user_id'     => $userId,
            ':action'      => $action,
            ':entity_type' => $entityType,
            ':entity_id'   => $entityId,
            ':metadata'    => $metadata ? json_encode($metadata) : null,
            ':ip_address'  => $ipAddress,
        ]);
    }
}