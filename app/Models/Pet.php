<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Database\Connection;

class Pet
{
    private const TABLE = 'pets';

    public static function getAll(): array
    {
        $conn = new Connection();
        $table = self::TABLE;

        $stmt = $conn->prepare(
            "SELECT id, name, created_at FROM {$table} ORDER BY created_at DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
