<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use RuntimeException;

class DatabaseConfig
{
    private static array $config = [];

    public static function load(): array
    {
        if (!empty(self::$config)) {
            return self::$config;
        }

        // Intentar cargar desde variables de entorno
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $database = $_ENV['DB_DATABASE'] ?? '';
        $username = $_ENV['DB_USERNAME'] ?? '';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        if (isset($_ENV['DOCKER_ENVIRONMENT']) && $_ENV['DOCKER_ENVIRONMENT'] === 'true') {
            $host = $_ENV['DB_DOCKER_HOST'] ?? 'mysql';
        }

        if (empty($database) || empty($username)) {
            throw new RuntimeException("Database configuration is incomplete. Please check DB_DATABASE and DB_USERNAME environment variables.");
        }

        self::$config = [
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ];

        return self::$config;
    }
}
