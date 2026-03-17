<?php

namespace App\Core;

use PDO;
use PDOException;

class DB
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $default = config('database.default');

        $config = config("database.connections.{$default}");

        if (!$config) {
            throw new \Exception("Database configuration not found.");
        }

        try {
            self::$pdo = new PDO(
                $config['dsn'],
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {

            if (env('APP_DEBUG', false)) {
                die("Database connection failed: " . $e->getMessage());
            }

            die("Database connection failed.");
        }

        return self::$pdo;
    }

    public static function connection(): PDO
    {
        return self::connect();
    }
}
