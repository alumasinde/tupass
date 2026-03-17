<?php

namespace App\Core;

use App\Core\DB;
use App\Modules\Audit\Repositories\AuditLogRepository;
use App\Modules\Audit\Services\AuditService;
use Throwable;

class Audit
{
    private static function service(): AuditService
    {
        $db = DB::connect();

        $repo = new AuditLogRepository($db);

        return new AuditService($repo);
    }

    public static function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $metadata = null
    ): void {

        try {
            if (!isset($_SESSION['user'])) {
                return;
            }

            $user = $_SESSION['user'];

            self::service()->log(
                (int) $user['tenant_id'],
                (int) $user['id'],
                $action,
                $entityType,
                $entityId,
                self::normalize($metadata)
            );

        } catch (Throwable $e) {
            error_log('Audit failed: ' . $e->getMessage());
        }
    }

    public static function system(
        int $tenantId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $metadata = null
    ): void {

        try {
            self::service()->log(
                $tenantId,
                null,
                $action,
                $entityType,
                $entityId,
                self::normalize($metadata)
            );

        } catch (Throwable $e) {
            error_log('Audit system failed: ' . $e->getMessage());
        }
    }

    private static function normalize(?array $metadata): ?array
    {
        $metadata = $metadata ?? [];

        $metadata['_context'] = [
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
            'method'    => $_SERVER['REQUEST_METHOD'] ?? null,
            'url'       => $_SERVER['REQUEST_URI'] ?? null,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        return $metadata;
    }
}