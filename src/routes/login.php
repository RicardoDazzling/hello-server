<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\dal\TokenDAL;
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

    if (!$user->isEmpty()) {
        $token = JWT::encode(
            [
                'iss' => $_ENV['APP_URL'],
                'iat' => time(),
                'data' => [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'name' => $user->name
                ]
            ],
            $_ENV['JWT_KEY'],
            $_ENV['JWT_ALG']
        );

        $entity = (new Token())->setToken($token);
        TokenDAL::create($entity);

        return json_encode([
            'message' => sprintf('%s successfully logged in', $user['email']),
            'token' => $token
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET')
{
    throw new MethodNotAllowedException();
}

echo getResponse();

