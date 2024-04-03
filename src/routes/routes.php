<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\Exceptions\NotFoundException;
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
        'message', 'file', 'writing', 'lost', 'call' => require_once 'base.php',
        'verify' => require_once 'verify.php',
        default => throw new NotFoundException(),
    };
}
catch (NotFoundException|MethodNotAllowedException|BadRequestException|UnAuthorizedException|InternalServerException $e)
{
    Http::setHeadersByCode($e->getCode());
    echo json_encode(['error' => $e->getMessage()]);
}
