<?php

namespace App\Modules\Audit\Services;

use App\Modules\Audit\Repositories\AuditLogRepository;
use Throwable;

class AuditService
{
    public function __construct(
        private AuditLogRepository $repo
    ) {}

    public function log(
        int $tenantId,
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $metadata = null
    ): void {

        try {
            $this->repo->log(
                $tenantId,
                $userId,
                $action,
                $entityType,
                $entityId,
                $metadata,
                $_SERVER['REMOTE_ADDR'] ?? null
            );
        } catch (Throwable $e) {
            // NEVER break business logic if audit fails
            error_log("Audit log failed: " . $e->getMessage());
        }
    }
}