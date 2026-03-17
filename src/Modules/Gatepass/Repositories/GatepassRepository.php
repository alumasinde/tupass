<?php

namespace App\Modules\Gatepass\Repositories;

use App\Core\BaseRepository;
use App\Modules\Gatepass\Repositories\GatepassStatusRepository;
use InvalidArgumentException;
use PDO;

class GatepassRepository extends BaseRepository
{
    protected string $table = 'gatepasses';
    protected string $alias = 'g';

    protected array $searchable = [
        'g.gatepass_number'
    ];

    protected array $filterable = [
        'status_id',
        'gatepass_type_id',
        'department_id'
    ];

    protected array $sortable = [
        'created_at',
        'gatepass_number',
        'status_id'
    ];

    private GatepassStatusRepository $statusRepo;

    public function __construct()
    {
        parent::__construct();
        $this->statusRepo = new GatepassStatusRepository();
    }

    /* =========================================================
     * CREATE
     * ========================================================= */

    public function create(array $data): int
    {
        $required = [
            'tenant_id',
            'gatepass_number',
            'status_id',
            'created_by',
            'purpose',
            'is_returnable',
            'needs_approval',
            'department_id'

        ];

        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if ((int)$data['tenant_id'] <= 0) {
            throw new InvalidArgumentException('Invalid tenant_id.');
        }

        if ((int)$data['created_by'] <= 0) {
            throw new InvalidArgumentException('Invalid created_by.');
        }

        if (trim($data['gatepass_number']) === '') {
            throw new InvalidArgumentException('Gatepass number cannot be empty.');
        }

        if (trim($data['purpose']) === '') {
            throw new InvalidArgumentException('Purpose cannot be empty.');
        }

        if ((int)$data['department_id'] <= 0) {
    throw new InvalidArgumentException('Invalid department_id.');
}

        $sql = "
            INSERT INTO {$this->table} (
                tenant_id,
                visit_id,
                gatepass_number,
                gatepass_type_id,
                status_id,
                created_by,
                checked_in_by,
                checked_out_by,
                purpose,
                is_returnable,
                expected_return_date,
                needs_approval,
                department_id
            )
            VALUES (
                :tenant_id,
                :visit_id,
                :gatepass_number,
                :gatepass_type_id,
                :status_id,
                :created_by,
                :checked_in_by,
                :checked_out_by,
                :purpose,
                :is_returnable,
                :expected_return_date,
                :needs_approval,
                :department_id
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':tenant_id'            => (int)$data['tenant_id'],
            ':visit_id'             => !empty($data['visit_id']) ? (int)$data['visit_id'] : null,
            ':gatepass_number'      => trim($data['gatepass_number']),
            ':gatepass_type_id'     => !empty($data['gatepass_type_id']) ? (int)$data['gatepass_type_id'] : null,
            ':status_id'            => (int)$data['status_id'],
            ':created_by'           => (int)$data['created_by'],
            ':checked_in_by'        => !empty($data['checked_in_by']) ? (int)$data['checked_in_by'] : null,
            ':checked_out_by'       => !empty($data['checked_out_by']) ? (int)$data['checked_out_by'] : null,
            ':purpose'              => trim($data['purpose']),
            ':is_returnable'        => (int)$data['is_returnable'],
            ':expected_return_date' => $data['expected_return_date'] ?? null,
            ':needs_approval'       => (int)$data['needs_approval'],
            ':department_id'        => (int)$data['department_id'],]);

        return (int)$this->db->lastInsertId();
    }

    /* =========================================================
     * UPDATE
     * ========================================================= */

    public function update(int $tenantId, int $id, array $data): bool
{
    $allowed = [
        'visit_id',
        'gatepass_type_id',
        'purpose',
        'is_returnable',
        'expected_return_date',
        'needs_approval',
    ];

    $setClauses = [];
    $bindings   = [
        ':tenant_id' => $tenantId,
        ':id'        => $id,
    ];

    foreach ($allowed as $field) {

        if (!array_key_exists($field, $data)) {
            continue;
        }

        $setClauses[] = "{$field} = :{$field}";

        switch ($field) {

            case 'department_id':
                $value = (int)$data[$field];
                if ($value <= 0) {
                    throw new InvalidArgumentException('Invalid department_id.');
                }
                $bindings[":{$field}"] = $value;
                break;

            case 'visit_id':
            case 'gatepass_type_id':
                $bindings[":{$field}"] = !empty($data[$field])
                    ? (int)$data[$field]
                    : null;
                break;

            case 'is_returnable':
            case 'needs_approval':
                $bindings[":{$field}"] = (int)(bool)$data[$field];
                break;

            case 'purpose':
                $value = trim($data[$field]);
                if ($value === '') {
                    throw new InvalidArgumentException('Purpose cannot be empty.');
                }
                $bindings[":{$field}"] = $value;
                break;

            case 'expected_return_date':
                $bindings[":{$field}"] = $data[$field] ?: null;
                break;
        }
    }

    if (empty($setClauses)) {
        throw new InvalidArgumentException('No updatable fields provided.');
    }

    $sql = "
        UPDATE {$this->table}
        SET " . implode(', ', $setClauses) . "
        WHERE tenant_id = :tenant_id
          AND id        = :id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($bindings);

    return $stmt->rowCount() > 0;
}
    /* =========================================================
     * UPDATE STATUS
     * ========================================================= */

    public function updateStatus(int $tenantId, int $id, int $statusId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET status_id = :status_id
            WHERE tenant_id = :tenant_id
              AND id        = :id
        ");

        $stmt->execute([
            ':status_id' => $statusId,
            ':tenant_id' => $tenantId,
            ':id'        => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

    /* =========================================================
     * LIST (uses BaseRepository pagination/search/filter/sort)
     * ========================================================= */

  public function findAllByTenant(int $tenantId): array
{
    $stmt = $this->db->prepare("
        SELECT 
            g.*,
            s.name  AS status_name,
            s.code  AS status_code,
            gt.name AS gatepass_type_name,
            gt.type_code
        FROM {$this->table} g
        LEFT JOIN gatepass_statuses s
            ON s.id = g.status_id
           AND s.tenant_id = g.tenant_id
        LEFT JOIN gatepass_types gt
            ON gt.id = g.gatepass_type_id
           AND gt.tenant_id = g.tenant_id
        WHERE g.tenant_id = :tenant_id
        ORDER BY g.created_at DESC
    ");

    $stmt->execute([
        ':tenant_id' => $tenantId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function listWithRelations(int $tenantId, array $params = []): array
    {
        $baseSql = "
            FROM gatepasses g
            LEFT JOIN gatepass_statuses s
                ON g.status_id = s.id
               AND s.tenant_id = g.tenant_id
            LEFT JOIN gatepass_types gt
                ON g.gatepass_type_id = gt.id
               AND gt.tenant_id = g.tenant_id
            WHERE g.tenant_id = :tenant_id
        ";

        return $this->customPaginatedQuery(
            $tenantId,
            $baseSql,
            "
            SELECT 
                g.*,
                s.name  AS status_name,
                gt.name AS gatepass_type_name
            ",
            $params
        );
    }

    protected function customPaginatedQuery(
        int $tenantId,
        string $baseSql,
        string $select,
        array $params
    ): array {

        $bindings = [':tenant_id' => $tenantId];

        $search  = $params['search'] ?? null;
        $filters = $params['filters'] ?? [];
        $page    = (int)($params['page'] ?? 1);
        $perPage = (int)($params['per_page'] ?? 20);

        $baseSql = \App\Core\QueryBuilder::applySearch(
            $baseSql,
            $this->searchable,
            $bindings,
            $search
        );

        $allowed = [];
        foreach ($this->filterable as $col) {
            if (isset($filters[$col])) {
                $allowed["g.$col"] = $filters[$col];
            }
        }

        $baseSql = \App\Core\QueryBuilder::applyFilters(
            $baseSql,
            $allowed,
            $bindings
        );

        $dataSql = $select . $baseSql . " ORDER BY g.created_at DESC";

        $paginator = new \App\Core\Paginator(
            $this->db,
            $baseSql,
            $bindings,
            $page,
            $perPage
        );

        return $paginator->paginate($dataSql);
    }

    /* =========================================================
     * FINDERS
     * ========================================================= */

  public function findById(int $tenantId, int $id): ?array
{
    $sql = "
        SELECT 
            g.*,

            -- Status
            s.name  AS status_name,
            s.code  AS status_code,

            -- Type
            gt.name      AS gatepass_type_name,
            gt.type_code AS type_code,

            -- Creator
            u.first_name AS created_by_first_name,
            u.last_name  AS created_by_last_name

        FROM gatepasses g

        INNER JOIN gatepass_statuses s
            ON s.id = g.status_id
           AND s.tenant_id = g.tenant_id

        INNER JOIN gatepass_types gt
            ON gt.id = g.gatepass_type_id
           AND gt.tenant_id = g.tenant_id

        LEFT JOIN users u
            ON u.id = g.created_by
           AND u.tenant_id = g.tenant_id

        WHERE g.tenant_id = :tenant_id
          AND g.id        = :id

        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':tenant_id' => $tenantId,
        ':id'        => $id,
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        return null;
    }

    // Normalize critical workflow fields defensively
    $result['status_code'] = strtoupper($result['status_code']);
    $result['type_code']   = strtoupper($result['type_code']);

    return $result;
}

    public function findByNumber(int $tenantId, string $number): ?array
    {
        $stmt = $this->db->prepare("
            SELECT g.*, s.name AS status_name,
                   u.first_name, u.last_name,
                   gt.name AS gatepass_type_name,
                   gt.type_code
            FROM gatepasses g
            LEFT JOIN gatepass_statuses s
                ON s.id = g.status_id
               AND s.tenant_id = g.tenant_id
            LEFT JOIN users u
                ON u.id = g.created_by
               AND u.tenant_id = g.tenant_id
            LEFT JOIN gatepass_types gt
                ON gt.id = g.gatepass_type_id
               AND gt.tenant_id = g.tenant_id
            WHERE g.tenant_id      = :tenant_id
              AND g.gatepass_number = :number
            LIMIT 1
        ");

        $stmt->execute([
            ':tenant_id' => $tenantId,
            ':number'    => $number,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findAllByDepartment(int $tenantId, int $departmentId): array
{
    $stmt = $this->db->prepare("
      SELECT
    g.id,
    g.gatepass_number,
    g.actual_in,
    g.actual_out,
    g.is_returnable,
    g.needs_approval,

    CONCAT(v.first_name,' ',v.last_name) AS visitor_name,
    vc.name AS company,
    g.purpose,
    g.created_at,

    gs.name AS status_name,
    gs.code AS status_code,

    gt.type_code AS type_code,
    gt.name AS gatepass_type_name,

    d.name AS department_name,

    CONCAT(u.first_name,' ',u.last_name) AS requested_by

FROM gatepasses g

LEFT JOIN visits vi
       ON vi.id = g.visit_id
      AND vi.tenant_id = g.tenant_id

LEFT JOIN visitors v
       ON v.id = vi.visitor_id
      AND v.tenant_id = g.tenant_id

LEFT JOIN visitor_companies vc
       ON vc.id = v.company_id
      AND vc.tenant_id = g.tenant_id

LEFT JOIN gatepass_statuses gs
       ON gs.id = g.status_id
      AND gs.tenant_id = g.tenant_id

LEFT JOIN gatepass_types gt
       ON gt.id = g.gatepass_type_id
      AND gt.tenant_id = g.tenant_id

LEFT JOIN departments d
       ON d.id = g.department_id
      AND d.tenant_id = g.tenant_id

LEFT JOIN users u
       ON u.id = g.created_by
      AND u.tenant_id = g.tenant_id

WHERE g.tenant_id = :tenant_id
  AND g.department_id = :department_id

ORDER BY g.created_at DESC;
    ");

    $stmt->execute([
        ':tenant_id'     => $tenantId,
        ':department_id' => $departmentId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /* =========================================================
     * WORKFLOW
     * ========================================================= */

   public function getWorkflowIdFromType(
    int $tenantId,
    int $gatepassTypeId
): ?int {

    $stmt = $this->db->prepare("
        SELECT workflow_id
        FROM workflow_gatepass_type
        WHERE tenant_id = :tenant_id
          AND gatepass_type_id = :gatepass_type_id
        LIMIT 1
    ");

    $stmt->execute([
        ':tenant_id'        => $tenantId,
        ':gatepass_type_id' => $gatepassTypeId,
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? (int) $row['workflow_id'] : null;
}
    /* =========================================================
     * STATUS OPERATIONS
     * ========================================================= */

   public function checkIn(
    int $tenantId,
    int $gatepassId,
    int $userId,
    string $timestamp
): bool {

    $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $timestamp);
    if (!$dt) {
        throw new InvalidArgumentException("Invalid datetime format.");
    }

    $checkedInStatusId = $this->statusRepo
        ->requireIdByCode($tenantId, 'CHECKED_IN');

    $checkedOutStatusId = $this->statusRepo
        ->requireIdByCode($tenantId, 'CHECKED_OUT');

    $approvedStatusId = $this->statusRepo
        ->requireIdByCode($tenantId, 'APPROVED');

    $stmt = $this->db->prepare("
        UPDATE gatepasses
        SET actual_in     = :timestamp,
            checked_in_by = :user_id,
            status_id     = :checked_in_status
        WHERE tenant_id   = :tenant_id
          AND id          = :id
          AND actual_in IS NULL
          AND status_id IN (:checked_out_status, :approved_status)
    ");

    $stmt->execute([
        ':timestamp'          => $timestamp,
        ':user_id'            => $userId,
        ':checked_in_status'  => $checkedInStatusId,
        ':tenant_id'          => $tenantId,
        ':id'                 => $gatepassId,
        ':checked_out_status' => $checkedOutStatusId,
        ':approved_status'    => $approvedStatusId,
    ]);

    return $stmt->rowCount() > 0;
}

    public function checkOut(
    int $tenantId,
    int $gatepassId,
    int $userId,
    string $timestamp
): bool {

    $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $timestamp);
    if (!$dt) {
        throw new InvalidArgumentException("Invalid datetime format.");
    }

    $statusId = $this->statusRepo
        ->requireIdByCode($tenantId, 'CHECKED_OUT'); // use uppercase

    $stmt = $this->db->prepare("
        UPDATE gatepasses
        SET actual_out      = :timestamp,
            checked_out_by  = :user_id,
            status_id       = :status_id
        WHERE tenant_id     = :tenant_id
          AND id            = :id
          AND actual_out IS NULL
    ");

    $stmt->execute([
        ':timestamp' => $timestamp,
        ':user_id'   => $userId,
        ':status_id' => $statusId,
        ':tenant_id' => $tenantId,
        ':id'        => $gatepassId,
    ]);

    return $stmt->rowCount() > 0;
}

    public function delete(int $tenantId, int $id): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM gatepasses
        WHERE tenant_id = :tenant_id
          AND id        = :id
    ");

        $stmt->execute([
            ':tenant_id' => $tenantId,
            ':id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

}