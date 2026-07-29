<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

use WpFatima\Controllers\MessageController;
use WpFatima\Controllers\WebhookController;

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/');
    $uri = preg_replace('#^.*/public#', '', $uri);

    $controller = new MessageController();
    $webhook = new WebhookController();

    if ($uri === '/webhook/whatsapp' && $method === 'POST') {
        $webhook->handle();
    } elseif ($uri === '/api/messages' && $method === 'GET') {
        $controller->list();
    } elseif (preg_match('#^/api/messages/(\d+)$#', $uri, $m) && $method === 'GET') {
        $controller->messages((int) $m[1]);
    } elseif ($uri === '/api/send' && $method === 'POST') {
        $controller->send();
    } else {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not found', 'uri' => $uri]);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine(),
    ]);
}
