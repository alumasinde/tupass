<?php

namespace App\Core;

final class QueryBuilder
{
    /**
     * Apply global search across multiple columns
     */
    public static function applySearch(
        string $sql,
        array $searchableColumns,
        array &$bindings,
        ?string $searchQuery
    ): string {
        $search = trim((string) $searchQuery);

        if ($search === '' || empty($searchableColumns)) {
            return $sql;
        }

        $conditions = [];
        foreach ($searchableColumns as $index => $column) {
            $param = ":search_{$index}";
            $conditions[] = "{$column} LIKE {$param}";
            $bindings[$param] = "%{$search}%";
        }

        $searchCondition = "(" . implode(' OR ', $conditions) . ")";

        return self::addWhereOrAnd($sql, $searchCondition);
    }

    /**
     * Apply multiple filters with support for operators
     */
    public static function applyFilters(
        string $sql,
        array $filters,
        array &$bindings
    ): string {
        foreach ($filters as $column => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $param = ':filter_' . str_replace(['.', '-'], '_', $column);

            if (is_array($value)) {
                // Handle IN clause
                $placeholders = [];
                foreach ($value as $i => $v) {
                    $p = $param . '_' . $i;
                    $placeholders[] = $p;
                    $bindings[$p] = $v;
                }
                $condition = "{$column} IN (" . implode(', ', $placeholders) . ")";
            } else {
                // Default to equality (you can extend this with operator support)
                $condition = "{$column} = {$param}";
                $bindings[$param] = $value;
            }

            $sql = self::addWhereOrAnd($sql, $condition);
        }

        return $sql;
    }

    /**
     * Smartly adds WHERE or AND depending on existing query
     */
    private static function addWhereOrAnd(string $sql, string $condition): string
    {
        $sql = trim($sql);

        // Check if there's already a WHERE clause
        if (preg_match('/\s+WHERE\s+/i', $sql)) {
            return $sql . " AND {$condition}";
        }

        return $sql . " WHERE {$condition}";
    }

    /**
     * Bonus: Advanced filter with operator support
     */
    public static function applyAdvancedFilters(
        string $sql,
        array $filters,
        array &$bindings
    ): string {
        foreach ($filters as $column => $data) {
            if (empty($data['value']) && $data['value'] !== 0 && $data['value'] !== '0') {
                continue;
            }

            $operator = $data['operator'] ?? '=';
            $param = ':adv_' . str_replace('.', '_', $column);

            $allowedOperators = ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE'];

            if (!in_array($operator, $allowedOperators)) {
                $operator = '=';
            }

            if ($operator === 'LIKE' || $operator === 'NOT LIKE') {
                $bindings[$param] = "%{$data['value']}%";
            } else {
                $bindings[$param] = $data['value'];
            }

            $condition = "{$column} {$operator} {$param}";
            $sql = self::addWhereOrAnd($sql, $condition);
        }

        return $sql;
    }
}