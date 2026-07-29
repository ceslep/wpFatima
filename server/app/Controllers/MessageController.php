<?php

declare(strict_types=1);

namespace WpFatima\Controllers;

use WpFatima\Config;
use WpFatima\Models\Conversation;
use WpFatima\Models\Message;
use Twilio\Rest\Client;

class MessageController
{
    public function list(): void
    {
        $conversations = Conversation::findAll();
        $this->json($conversations);
    }

    public function messages(int $conversationId): void
    {
        $conversation = Conversation::findById($conversationId);
        if (!$conversation) {
            http_response_code(404);
            $this->json(['error' => 'Conversation not found']);
            return;
        }

        $messages = Message::findByConversation($conversationId);
        $this->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function send(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $conversationId = (int) ($input['conversation_id'] ?? 0);
        $body = trim($input['body'] ?? '');

        if ($conversationId === 0 || $body === '') {
            http_response_code(400);
            $this->json(['error' => 'conversation_id and body required']);
            return;
        }

        $conversation = Conversation::findById($conversationId);
        if (!$conversation) {
            http_response_code(404);
            $this->json(['error' => 'Conversation not found']);
            return;
        }

        $config = Config::getInstance();
        $sid = $config->get('TWILIO_ACCOUNT_SID');
        $token = $config->get('TWILIO_AUTH_TOKEN');
        $from = $config->get('TWILIO_WHATSAPP_NUMBER');

        $twilioSid = null;
        $status = 'queued';
        $twilioError = null;

        $to = $conversation['wa_number'];
        if (!str_starts_with($to, 'whatsapp:')) {
            $to = 'whatsapp:' . $to;
        }

        try {
            $client = new Client($sid, $token);
            $message = $client->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => $body,
                ]
            );
            $twilioSid = $message->sid;
            $status = 'sent';
        } catch (\Exception $e) {
            $status = 'failed';
            $twilioError = $e->getMessage();
        }

        $msg = Message::create($conversationId, 'outbound', $body, $twilioSid, $status);
        Conversation::updateLastMessage($conversationId, mb_substr($body, 0, 100));

        $response = $msg;
        if ($twilioError) {
            $response['twilio_error'] = $twilioError;
        }
        $this->json($response);
    }

    private function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
