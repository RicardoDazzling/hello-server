<?php
namespace DazzRick\HelloServer;

class AllowCors
{
    private const ALLOW_CORS_ORIGIN_KEY = 'Access-Control-Allow-Origin';
    private const ALLOW_CORS_ORIGIN_VALUE = '*';

    private const ALLOW_CORS_METHODS_KEY = 'Access-Control-Allow-Methods';
    private const ALLOW_CORS_METHODS_VALUE = 'GET, POST, PUT, DELETE, OPTIONS';

    public function init(): void
    {
        $this->set(self::ALLOW_CORS_ORIGIN_KEY, self::ALLOW_CORS_ORIGIN_VALUE);
        $this->set(self::ALLOW_CORS_METHODS_KEY, self::ALLOW_CORS_METHODS_VALUE);
    }

    private function set(string $key, string $value): void
    {
        header($key . ': ' . $value);
    }
}