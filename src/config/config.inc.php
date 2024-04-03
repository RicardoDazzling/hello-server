<?php
namespace DazzRick\HelloServer\Config;

use Dotenv\Dotenv;

$path = dirname(__DIR__, 2);
$dotenv = Dotenv::createImmutable($path);
$dotenv->load();

// optional: check if the necessary values are in the .env file
$dotenv->required(['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS', 'SMTP_HOST', 'SMTP_USER', 'SMTP_PASS',
    'SMTP_PORT', 'JWT_KEY', 'JWT_ALG', 'APP_URL']);
if(!$dotenv->ifPresent('PRODUCTION'))
{
    $_ENV['PRODUCTION'] = 'FAlSE';
}