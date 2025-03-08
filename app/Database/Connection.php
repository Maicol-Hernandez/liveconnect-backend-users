<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use PDOStatement;

class Connection
{
    private static ?PDO $instance = null;
    private PDO $connection;

    private function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']};charset=utf8",
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD']
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \RuntimeException("An error has ocurred and cannot connect to the database:{$e->getMessage()}", 503, $e);
        }
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $self = new self();
            self::$instance = $self->connection;
        }
        return self::$instance;
    }

    public static function beginTransaction(): void
    {
        self::getInstance()->beginTransaction();
    }

    public static function commit(): void
    {
        self::getInstance()->commit();
    }

    public static function rollback(): void
    {
        self::getInstance()->rollBack();
    }

    public function prepare(string $sql): PDOStatement
    {
        return self::getInstance()->prepare($sql);
    }

    public function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}
