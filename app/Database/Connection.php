<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;
use Throwable;

class Connection
{
    private static ?PDO $instance = null;
    private PDO $connection;
    private array $config;

    private function __construct()
    {
        try {
            $this->config = DatabaseConfig::load();

            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (PDOException $e) {
            saveLog($e);
            throw new RuntimeException("Database connection error: {$e->getMessage()}", 503, $e);
        } catch (Throwable $th) {
            saveLog($th);
            throw new RuntimeException("Unexpected error in database connection: {$th->getMessage()}", 503, $th);
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

    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    public static function rollback(): bool
    {
        return self::getInstance()->rollBack();
    }

    public static function prepare(string $sql): PDOStatement
    {
        return self::getInstance()->prepare($sql);
    }

    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }

    public static function close(): void
    {
        self::$instance = null;
    }
}
