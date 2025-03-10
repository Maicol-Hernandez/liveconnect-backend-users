<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use Exception;
use App\Models\Pet;
use App\Database\Connection;

class PetUser
{
    private const TABLE = "pet_user";

    public static function createBulk(int $userId, array $petIds): bool
    {
        if (empty($petIds)) {
            return false;
        }

        try {
            $placeholders = [];
            $values = [];

            foreach ($petIds as $petId) {
                Pet::findById($petId);

                $placeholders[] = '(?, ?)';
                $values[] = $userId;
                $values[] = $petId;
            }

            $sql = sprintf(
                'INSERT INTO %s (user_id, pet_id) VALUES %s',
                self::TABLE,
                implode(', ', $placeholders)
            );

            $stmt = Connection::getInstance()->prepare($sql);
            $stmt->execute($values);

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function deleteBulk(int $userId, ?array &$petIds = null): bool
    {
        try {
            $sql = sprintf(
                'DELETE FROM %s WHERE user_id = ?',
                self::TABLE
            );

            $stmt = Connection::getInstance()->prepare($sql);
            $stmt->execute([$userId]);

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function updateBulk(int $userId, array $petIds): bool
    {
        return self::deleteBulk($userId) && self::createBulk($userId, $petIds);
    }

    public static function getPetsForUser(int $userId): array
    {
        $query = sprintf(
            'SELECT pets.* FROM pets JOIN %s ON pets.id = %s.pet_id WHERE %s.user_id = :user_id',
            self::TABLE,
            self::TABLE,
            self::TABLE
        );

        $statement = Connection::getInstance()->prepare($query);
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
