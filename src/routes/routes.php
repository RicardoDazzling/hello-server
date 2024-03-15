<?php
namespace DazzRick\HelloServer;

$resource = $_REQUEST['resource'] ?? null;
return match ($resource) {
    'user' => require_once 'user.php',
    default => require_once 'main.php',
};