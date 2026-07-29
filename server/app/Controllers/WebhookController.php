<?php

declare(strict_types=1);

namespace WpFatima\Controllers;

use WpFatima\Models\Conversation;
use WpFatima\Models\Message;

class WebhookController
{
    public function handle(): void
    {
        $from = $_POST['From'] ?? '';
        $body = $_POST['Body'] ?? '';
        $messageSid = $_POST['MessageSid'] ?? '';

        if ($from === '' || $body === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Missing From or Body']);
            return;
        }

        $waNumber = preg_replace('/^whatsapp:/', '', $from);

        $conversation = Conversation::findOrCreate($waNumber);
        $conversationId = (int) $conversation['id'];

        Message::create($conversationId, 'inbound', $body, $messageSid, 'delivered');
        Conversation::updateLastMessage($conversationId, mb_substr($body, 0, 100));

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';
    }
}
