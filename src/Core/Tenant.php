<?php

namespace App\Core;

class Tenant
{
    public static function id(): ?int
    {
        return $_SESSION['user']['tenant_id'] ?? null;
    }

    public static function set(int $tenantId): void
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['user'] = [];
        }

        $_SESSION['user']['tenant_id'] = $tenantId;
    }
}