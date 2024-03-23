<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\Exceptions\NotFoundException;
use DazzRick\HelloServer\Exceptions\NotImplementedException;
use DazzRick\HelloServer\Services\UserService;
use Respect\Validation\Validator as v;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

function getResponse(): string
{
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

        return json_encode(['errors' => ['message' => 'Uuid is required.']]);
    }
    if(!(v::uuid()->validate($uuid)))
    {
        Http::setHeadersByCode(StatusCode::BAD_REQUEST);

        return json_encode(['errors' => ['message' => 'Uuid is invalid.']]);
    }
    $user = (new UserService())->retrieve($uuid);
    return json_encode($user);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET')
{
    throw new NotFoundException();
}

echo getResponse();

