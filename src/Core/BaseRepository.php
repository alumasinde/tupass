<?php

namespace App\Core;

use PDO;

abstract class BaseRepository
{
    protected PDO $db;
    protected string $table;
    protected string $alias = 't';

    protected array $searchable = [];
    protected array $filterable = [];
    protected array $sortable = [];

    public function __construct()
    {
        $this->db = DB::connect();
    }

    public function count(int $tenantId): int
{
    $stmt = $this->db->prepare("
        SELECT COUNT(*) 
        FROM {$this->table}
        WHERE tenant_id = :tenant_id
    ");

    $stmt->execute([':tenant_id' => $tenantId]);

    return (int) $stmt->fetchColumn();
}

    protected function baseFrom(): string
    {
        return " FROM {$this->table} {$this->alias} 
                 WHERE {$this->alias}.tenant_id = :tenant_id ";
    }

    protected function selectColumns(): string
{
    return "SELECT {$this->alias}.*";
}

protected function baseQuery(): string
{
    return "FROM {$this->table} {$this->alias}";
}
    public function list(
    int $tenantId,
    array $params = []
): array {

    $search   = $params['search'] ?? null;
    $filters  = $params['filters'] ?? [];
    $sortBy   = $params['sort_by'] ?? 'created_at';
    $sortDir  = strtolower($params['sort_dir'] ?? 'desc');
    $page     = (int)($params['page'] ?? 1);
    $perPage  = (int)($params['per_page'] ?? 10);

    if (!in_array($sortBy, $this->sortable, true)) {
        $sortBy = 'created_at';
    }

    if (!in_array($sortDir, ['asc', 'desc'], true)) {
        $sortDir = 'desc';
    }

    // Base SQL (JOINs allowed here)
    $baseSql = $this->baseQuery() . " 
        WHERE {$this->alias}.tenant_id = :tenant_id
    ";

    $bindings = [':tenant_id' => $tenantId];

    // Search
    if (!empty($this->searchable)) {
        $baseSql = QueryBuilder::applySearch(
            $baseSql,
            $this->searchable,
            $bindings,
            $search
        );
    }

    // Filters (whitelisted only)
    $allowedFilters = [];
    foreach ($this->filterable as $column) {
        if (isset($filters[$column])) {
            $allowedFilters[$column] = $filters[$column];
        }
    }

    $baseSql = QueryBuilder::applyFilters(
        $baseSql,
        $allowedFilters,
        $bindings
    );

    $dataSql = "
        {$this->selectColumns()}
        {$baseSql}
        ORDER BY {$sortBy} {$sortDir}
    ";

    $paginator = new Paginator(
        $this->db,
        $baseSql,
        $bindings,
        $page,
        $perPage
    );

    return $paginator->paginate($dataSql);
}
    /* ===============================
       Transactions
    =============================== */

    public function begin(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollback(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }
}