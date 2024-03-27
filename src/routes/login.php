<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\dal\TokenDAL;
use DazzRick\HelloServer\Entity\Token;
use DazzRick\HelloServer\Exceptions\NotFoundException;
use DazzRick\HelloServer\Exceptions\NotImplementedException;
use DazzRick\HelloServer\Services\UserService;
use Firebase\JWT\JWT;
use Respect\Validation\Validator as v;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

function getResponse(): string
{
    $return = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $postBody = json_decode(file_get_contents('php://input'));
        // TODO: Make a email login by mail validation.
        throw new NotImplementedException();
    }

    $uuid = $_REQUEST['uuid'] ?? null;
    if (is_null($uuid))
    {
        Http::setHeadersByCode(StatusCode::BAD_REQUEST);

        $return = ['errors' => ['message' => 'Uuid is required.']];
    }
    if(!(v::uuid()->validate($uuid)))
    {
        Http::setHeadersByCode(StatusCode::BAD_REQUEST);

        $return = ['errors' => ['message' => 'Uuid is invalid.']];
    }
    if(is_null($return))
    {
        $user = (new UserService())->retrieve($uuid);
        $return = [];
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

            $return = [
                'message' => sprintf('%s successfully logged in', $user['email']),
                'token' => $token
            ];
        }
    }
    return json_encode($return);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET')
{
    throw new NotFoundException();
}

echo getResponse();

