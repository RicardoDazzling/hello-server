<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\DAL\TokenDAL;
use DazzRick\HelloServer\Entity\Token;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\Exceptions\NotImplementedException;
use DazzRick\HelloServer\Services\UserService;
use DazzRick\HelloServer\Services\VerificationService;
use Firebase\JWT\JWT;
use Respect\Validation\Validator as v;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

function getResponse(): string
{
    $email = $_REQUEST['email'] ?? null;
    if (is_null($email)) throw new BadRequestException('Email is required.');
    if(!(v::email()->validate($email))) throw new BadRequestException('Email is invalid.');
    $user = (new UserService())->retrieve(email: $email);
    (new VerificationService())->create($user);
    return json_encode(['Success' => true]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET')
{
    throw new MethodNotAllowedException();
}

echo getResponse();

