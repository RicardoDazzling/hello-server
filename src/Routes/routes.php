<?php

use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\Exceptions\NotFoundException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use DazzRick\HelloServer\Services\MailerService;
use PH7\PhpHttpResponseHeader\Http;

$resource = $_REQUEST['resource'] ?? null;
try
{
    match ($resource) {
        'online' => require_once 'online.php',
        'clean' => require_once 'clean.php',
        'login' => require_once 'login.php',
        'register' => require_once 'register.php',
        'user' => require_once 'user.php',
        'group' => require_once 'group.php',
        'message', 'file', 'writing', 'lost', 'call' => require_once 'base.php',
        'verify' => require_once 'verify.php',
        'callback' => MailerService::callback(),
        default => throw new NotFoundException(),
    };
}
catch (NotFoundException|MethodNotAllowedException|BadRequestException|UnAuthorizedException|InternalServerException $e)
{
    Http::setHeadersByCode($e->getCode());
    echo json_encode(['error' => $e->getMessage()]);
}
