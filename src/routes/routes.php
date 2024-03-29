<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\exceptions\NotFoundException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use PH7\PhpHttpResponseHeader\Http;

$resource = $_REQUEST['resource'] ?? null;
try
{
    return match ($resource) {
        'clean' => require_once 'clean.php',
        'login' => require_once 'login.php',
        'register' => require_once 'register.php',
        'user' => require_once 'user.php',
        'message' || 'file' => require_once 'base.php',
        default => throw new NotFoundException(),
    };
}
catch (NotFoundException|MethodNotAllowedException|BadRequestException|UnAuthorizedException $e)
{
    Http::setHeadersByCode($e->getCode());
    echo json_encode(['error' => $e->getMessage()]);
}
