<?php

namespace App\Modules\Gatepass\Repositories;

use App\Core\DB;
use PDO;

class GatepassItemRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::connect();
    }

    public function insertMany(int $tenantId, int $gatepassId, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO gatepass_items
            (tenant_id, gatepass_id, item_name, description, quantity, serial_number, is_returnable)
            VALUES
            (:tenant_id, :gatepass_id, :item_name, :description, :quantity, :serial_number, :is_returnable)
        ");

        foreach ($items as $item) {

            if (empty($item['item_name'])) {
                continue;
            }

            $stmt->execute([
                ':tenant_id'     => $tenantId,
                ':gatepass_id'   => $gatepassId,
                ':item_name'     => trim($item['item_name']),
                ':description'   => $item['description'] ?? null,
                ':quantity'      => (int)($item['quantity'] ?? 1),
                ':serial_number' => $item['serial_number'] ?? null,
                ':is_returnable' => (int)($item['is_returnable'] ?? 0),
            ]);
        }
    }

    public function findByGatepass(int $tenantId, int $gatepassId): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM gatepass_items
            WHERE tenant_id = :tenant_id
              AND gatepass_id = :gatepass_id
        ");

        $stmt->execute([
            ':tenant_id' => $tenantId,
            ':gatepass_id' => $gatepassId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByGatepass(int $tenantId, int $gatepassId): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM gatepass_items
            WHERE tenant_id = :tenant_id
              AND gatepass_id = :gatepass_id
        ");

        $stmt->execute([
            ':tenant_id' => $tenantId,
            ':gatepass_id' => $gatepassId
        ]);
    }

    public function updateReturnedQuantity(
        int $tenantId,
        int $itemId,
        int $returnedQty
    ): bool {

        $stmt = $this->db->prepare("
            UPDATE gatepass_items
            SET returned_quantity = :returned_quantity
            WHERE tenant_id = :tenant_id
              AND id = :id
        ");

        $stmt->execute([
            ':returned_quantity' => $returnedQty,
            ':tenant_id' => $tenantId,
            ':id' => $itemId
        ]);

        return $stmt->rowCount() > 0;
    }
}
