<?php

declare(strict_types=1);

namespace App\Models;

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
}
