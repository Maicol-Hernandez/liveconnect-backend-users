<?php

namespace App\Models;

use PDO;
use App\Database\Connection;
use App\Exceptions\HttpException;

class User
{
    private const TABLE = "users";

    public static function getAll(): array
    {
        $table = self::TABLE;

        $stmt = Connection::getInstance()->prepare(
            "SELECT id, name, email, password, created_at, updated_at FROM {$table} ORDER BY created_at DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function checkExistingUser(string $email): void
    {
        $table = self::TABLE;

        $stmt = Connection::getInstance()->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE email = :email"
        );

        $stmt->execute([
            ':email' => $email,
        ]);

        if ($stmt->fetchColumn() > 0) {
            throw new HttpException("Email already exists", 409);
        }
    }

    public static function create(array $userData): array
    {
        $table = self::TABLE;

        self::checkExistingUser($userData['email']);

        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

        $stmt = Connection::getInstance()->prepare(
            "INSERT INTO {$table} (name, email, password) VALUES (:name, :email, :password)"
        );

        $stmt->execute([
            ':name' => $userData['name'],
            ':email' => $userData['email'],
            ':password' => $hashedPassword,
        ]);

        return self::findById(Connection::getInstance()->lastInsertId());
    }

    public static function update(int $userId, array $userDetails): array
    {
        $tableName = self::TABLE;

        $user = self::findById($userId);
        if ($user['email'] !== $userDetails['email']) {
            self::checkExistingUser($userDetails['email']);
        }

        $hashedPassword = password_hash($userDetails['password'], PASSWORD_DEFAULT);

        $updateQuery = Connection::getInstance()->prepare(
            "UPDATE {$tableName} SET name = :name, email = :email, password = :password WHERE id = :id"
        );

        $updateQuery->execute([
            ':name' => $userDetails['name'],
            ':email' => $userDetails['email'],
            ':password' => $hashedPassword,
            ':id' => $userId,
        ]);

        return self::findById($userId);
    }

    public static function findById(int $id): array
    {
        $table = self::TABLE;

        $stmt = Connection::getInstance()->prepare(
            "SELECT id, name, email, created_at, updated_at FROM {$table} WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new HttpException("User not found", 404);
        }

        return $user;
    }

    public static function delete(int $id): void
    {
        $table = self::TABLE;

        $stmt = Connection::getInstance()->prepare(
            "DELETE FROM {$table} WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            throw new HttpException("User not found", 404);
        }
    }
}
