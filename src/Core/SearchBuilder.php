<?php
namespace App\Core;

final class SearchBuilder
{
    public static function apply(
        string $sql,
        array $searchableColumns,
        array &$bindings
    ): string {

        $q = trim($_GET['q'] ?? '');

        if ($q === '') {
            return $sql;
        }

        $conditions = [];

        foreach ($searchableColumns as $index => $column) {
            $param = ":search_$index";
            $conditions[] = "$column LIKE $param";
            $bindings[$param] = '%' . $q . '%';
        }

        $sql .= " AND (" . implode(' OR ', $conditions) . ")";

        return $sql;
    }
}