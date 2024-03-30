<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Services\UserService;
use DazzRick\HelloServer\Services\VerificationService;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

function getResponse(): string
{
    $postBody = json_decode(file_get_contents('php://input'));
    try {
        $user = (new UserService())->create($postBody);
        if (http_response_code() !== StatusCode::OK || $user->isEmpty()) {
            return json_encode(['Success' => false]);
        }
        Http::setHeadersByCode(StatusCode::CREATED);
        (new VerificationService())->create($user);
    } catch (ValidationException $e){
        Http::setHeadersByCode(StatusCode::BAD_REQUEST);

        return json_encode([
            'errors' => [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]
        ]);
    }
    return json_encode(['Success' => true]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    throw new MethodNotAllowedException();
}

echo getResponse();

