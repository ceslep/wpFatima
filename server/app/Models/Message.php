<?php

declare(strict_types=1);

namespace WpFatima\Models;

use WpFatima\Database;

class Message
{
    public static function findByConversation(int $conversationId, int $limit = 50, int $offset = 0): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, conversation_id, direction, body, twilio_message_sid, status, created_at 
             FROM messages WHERE conversation_id = ? 
             ORDER BY created_at ASC LIMIT ? OFFSET ?'
        );
        $stmt->execute([$conversationId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public static function create(
        int $conversationId,
        string $direction,
        string $body,
        string $twilioSid = null,
        string $status = 'queued'
    ): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO messages (conversation_id, direction, body, twilio_message_sid, status) 
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$conversationId, $direction, $body, $twilioSid, $status]);

        $id = (int) $pdo->lastInsertId();
        return self::findById($id);
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, conversation_id, direction, body, twilio_message_sid, status, created_at 
             FROM messages WHERE id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updateStatus(string $twilioSid, string $status): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE messages SET status = ? WHERE twilio_message_sid = ?');
        $stmt->execute([$status, $twilioSid]);
    }
}
