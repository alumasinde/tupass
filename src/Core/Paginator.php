<?php
namespace App\Core;

use PDO;

class Paginator
{
    private PDO $db;
    private string $baseSql;
    private array $bindings;
    private int $page;
    private int $perPage;

    public function __construct(
        PDO $db,
        string $baseSql,
        array $bindings,
        int $page = 1,
        int $perPage = 20
    ) {
        $this->db = $db;
        $this->baseSql = $baseSql;
        $this->bindings = $bindings;
        $this->page = max(1, $page);
        $this->perPage = max(1, min($perPage, 100)); // hard limit
    }

    public function paginate(string $dataSql): array
    {
        $total = $this->getTotal();

        $offset = ($this->page - 1) * $this->perPage;

        $dataSql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($dataSql);

        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $this->perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'meta' => [
                'total' => $total,
                'per_page' => $this->perPage,
                'current_page' => $this->page,
                'last_page' => (int)ceil($total / $this->perPage),
            ]
        ];
    }

    private function getTotal(): int
    {
        $countSql = "SELECT COUNT(*) {$this->baseSql}";

        $stmt = $this->db->prepare($countSql);

        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }
}