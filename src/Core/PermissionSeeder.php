<?php

namespace App\Core;

use PDO;

class PermissionSeeder
{
    public static function seed(PDO $db): void
    {
        $map = require base_path('config/permissions.php');

        foreach ($map as $module => $actions) {

            // Insert module
            $stmt = $db->prepare("
                INSERT IGNORE INTO modules (name)
                VALUES (?)
            ");
            $stmt->execute([$module]);

            $moduleId = $db->lastInsertId()
                ?: $db->query("SELECT id FROM modules WHERE name = '$module'")
                      ->fetchColumn();

            foreach ($actions as $action) {

                // Insert action
                $stmt = $db->prepare("
                    INSERT IGNORE INTO actions (name)
                    VALUES (?)
                ");
                $stmt->execute([$action]);

                $actionId = $db->lastInsertId()
                    ?: $db->query("SELECT id FROM actions WHERE name = '$action'")
                          ->fetchColumn();

                // Insert permission
                $stmt = $db->prepare("
                    INSERT IGNORE INTO permissions (module_id, action_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$moduleId, $actionId]);
            }
        }
    }
}
