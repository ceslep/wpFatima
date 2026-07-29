<?php

declare(strict_types=1);

namespace WpFatima;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $config = Config::getInstance();
            $host = $config->get('DB_HOST', '127.0.0.1');
            $port = $config->get('DB_PORT', '3306');
            $name = $config->get('DB_NAME', 'wpfatima');
            $user = $config->get('DB_USER', 'root');
            $pass = $config->get('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
