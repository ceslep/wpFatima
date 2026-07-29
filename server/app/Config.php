<?php

declare(strict_types=1);

namespace WpFatima;

use Dotenv\Dotenv;

class Config
{
    private static ?self $instance = null;

    private function __construct()
    {
        $root = dirname(__DIR__);
        $dotenv = Dotenv::createUnsafeImmutable($root);
        $dotenv->load();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get(string $key, string $default = ''): string
    {
        return $_ENV[$key] ?? $default;
    }
}
