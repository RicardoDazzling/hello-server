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
        'login' => require_once 'login.php',
        'register' => require_once 'register.php',
        'user' => require_once 'user.php',
        'message' => require_once 'message.php',
        default => throw new NotFoundException(),
    };
}
catch (NotFoundException|MethodNotAllowedException|BadRequestException|UnAuthorizedException $e)
{
    Http::setHeadersByCode($e->getCode());
    echo json_encode(['error' => $e->getMessage()]);
}
