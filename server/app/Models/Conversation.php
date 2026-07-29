<?php

declare(strict_types=1);

namespace WpFatima\Models;

use WpFatima\Database;
use PDO;

class Conversation
{
    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            'SELECT id, wa_number, contact_name, last_message, last_message_at, created_at 
             FROM conversations ORDER BY last_message_at DESC'
        );
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, wa_number, contact_name, last_message, last_message_at, created_at 
             FROM conversations WHERE id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByWaNumber(string $waNumber): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, wa_number, contact_name, last_message, last_message_at, created_at 
             FROM conversations WHERE wa_number = ?'
        );
        $stmt->execute([$waNumber]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findOrCreate(string $waNumber, string $contactName = null): array
    {
        $existing = self::findByWaNumber($waNumber);
        if ($existing) {
            return $existing;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO conversations (wa_number, contact_name, last_message_at) VALUES (?, ?, NOW())'
        );
        $stmt->execute([$waNumber, $contactName]);
        $id = (int) $pdo->lastInsertId();

        return self::findById($id);
    }

    public static function updateLastMessage(int $id, string $body): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE conversations SET last_message = ?, last_message_at = NOW() WHERE id = ?'
        );
        $stmt->execute([$body, $id]);
    }
}
