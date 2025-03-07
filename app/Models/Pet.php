<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Database\Connection;
use App\Exceptions\HttpException;

class Pet
{
    private const TABLE = 'pets';

    public static function getAll(): array
    {
        $table = self::TABLE;

        $stmt = Connection::getInstance()->prepare(
            "SELECT id, name, created_at FROM {$table} ORDER BY created_at DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): array
    {
        $table = self::TABLE;

        $stmt = Connection::getInstance()->prepare(
            "SELECT id, name, created_at FROM {$table} WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);

        $pet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pet) {
            throw new HttpException("Pet not found with id {$id}", 404);
        }

        return $pet;
    }
}
