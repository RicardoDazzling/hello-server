<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\exceptions\NotFoundException;

$resource = $_REQUEST['resource'] ?? null;
try {
    return match ($resource) {
        'login' => require_once 'login.php',
        'register' => require_once 'register.php',
        'user' => require_once 'user.php',
        default => require_once 'notfound.php',
    };
} catch (NotFoundException $exception){
    return require_once 'notfound.php';
}
