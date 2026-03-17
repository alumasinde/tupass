<?php
namespace App\Core;

final class QueryBuilder
{
    public static function applySearch(
        string $sql,
        array $searchableColumns,
        array &$bindings,
        ?string $query
    ): string {

        $q = trim((string)$query);

        if ($q === '') {
            return $sql;
        }

        $conditions = [];

        foreach ($searchableColumns as $index => $column) {
            $param = ":search_$index";
            $conditions[] = "$column LIKE $param";
            $bindings[$param] = '%' . $q . '%';
        }

        return $sql . " AND (" . implode(' OR ', $conditions) . ")";
    }

    public static function applyFilters(
        string $sql,
        array $filters,
        array &$bindings
    ): string {

        foreach ($filters as $column => $value) {

            if ($value === null || $value === '') {
                continue;
            }

            $param = ':filter_' . str_replace('.', '_', $column);

            $sql .= " AND {$column} = {$param}";
            $bindings[$param] = $value;
        }

        return $sql;
    }
}