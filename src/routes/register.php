<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\Exceptions\NotFoundException;
use DazzRick\HelloServer\Exceptions\NotImplementedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Services\UserService;
use Respect\Validation\Validator as v;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

function getResponse(): string
{
    $postBody = json_decode(file_get_contents('php://input'));
    // TODO: Make a email register by mail validation.
    try {
        $user = (new UserService())->create($postBody);
        if (http_response_code() === StatusCode::OK) {
            Http::setHeadersByCode(StatusCode::CREATED);
        }
    } catch (ValidationException $e){
        Http::setHeadersByCode(StatusCode::BAD_REQUEST);

        return json_encode([
            'errors' => [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]
        ]);
    }
    return json_encode($user);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    throw new NotFoundException();
}

echo getResponse();

