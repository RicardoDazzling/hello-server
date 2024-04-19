<?php

use DazzRick\HelloServer\DAL\TokenDAL;
use DazzRick\HelloServer\Entity\Token;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\Services\UserService;
use DazzRick\HelloServer\Services\VerificationService;
use Firebase\JWT\JWT;
use Respect\Validation\Validator as v;

function getResponse(): string
{
    $code = $_REQUEST['code'] ?? null;
    if (is_null($code)) throw new BadRequestException('Verification Code is required.');
    if (strlen($code) !== 6 || !is_numeric($code)) throw new BadRequestException('Invalid Verification Code.');

    $email = $_REQUEST['email'] ?? null;
    if (is_null($email)) throw new BadRequestException('Email is required.');
    if(!(v::email()->validate($email))) throw new BadRequestException('Email is invalid.');
    $user = (new UserService())->retrieve(email: $email);
    if ($user->isEmpty()) throw new BadRequestException('User not found.');
    (new VerificationService())->verify($code, $user);

    $token = JWT::encode(
        [
            'iss' => $_ENV['APP_URL'],
            'iat' => time(),
            'data' => [
                'uuid' => $user->getUuid(),
                'email' => $email,
                'name' => $user->getName()
            ]
        ],
        $_ENV['JWT_KEY'],
        $_ENV['JWT_ALG']
    );

    $entity = (new Token())->setToken($token);
    TokenDAL::create($entity);

    return json_encode([
        'message' => sprintf('%s successfully logged in', $email),
        'token' => $token
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET')
{
    throw new MethodNotAllowedException();
}

echo getResponse();

