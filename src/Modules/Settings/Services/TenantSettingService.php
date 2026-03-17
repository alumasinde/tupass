<?php

namespace App\Modules\Settings\Services;

use App\Core\DB;
use PDO;

class TenantSettingService
{
    private PDO $db;
    private int $tenantId;

    public function __construct(?int $tenantId = null)
    {
        $this->db = DB::connect();

        if ($tenantId !== null) {
            $this->tenantId = $tenantId;
            return;
        }

        if (!isset($_SESSION['user']['tenant_id'])) {
            throw new \RuntimeException('Tenant not found in session.');
        }

        $this->tenantId = (int) $_SESSION['user']['tenant_id'];
    }

    /**
     * Get setting (auto JSON decode)
     */
    public function get(string $key, array $default = []): array
    {
        $stmt = $this->db->prepare("
            SELECT setting_value
            FROM tenant_settings
            WHERE tenant_id = :tenant_id
              AND setting_key = :key
            LIMIT 1
        ");

        $stmt->execute([
            ':tenant_id' => $this->tenantId,
            ':key'       => $key
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            if (!empty($default)) {
                $this->set($key, $default);
            }
            return $default;
        }

        return json_decode($row['setting_value'], true) ?? $default;
    }

    /**
     * Save setting (auto JSON encode)
     */
    public function set(string $key, array $value): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO tenant_settings (tenant_id, setting_key, setting_value)
            VALUES (:tenant_id, :key, :value)
            ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                updated_at = CURRENT_TIMESTAMP
        ");

        $stmt->execute([
            ':tenant_id' => $this->tenantId,
            ':key'       => $key,
            ':value'     => json_encode($value)
        ]);
    }

    /**
     * Delete setting
     */
    public function delete(string $key): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM tenant_settings
            WHERE tenant_id = :tenant_id
              AND setting_key = :key
        ");

        $stmt->execute([
            ':tenant_id' => $this->tenantId,
            ':key'       => $key
        ]);
    }

    /**
     * Check if setting exists
     */
    public function exists(string $key): bool
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM tenant_settings
            WHERE tenant_id = :tenant_id
              AND setting_key = :key
            LIMIT 1
        ");

        $stmt->execute([
            ':tenant_id' => $this->tenantId,
            ':key'       => $key
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Increment numeric value inside setting JSON
     */
    public function increment(string $key, string $field, int $step = 1): int
    {
        $data = $this->get($key);

        $current = (int) ($data[$field] ?? 0);
        $current += $step;

        $data[$field] = $current;

        $this->set($key, $data);

        return $current;
    }
}