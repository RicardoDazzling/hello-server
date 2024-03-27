<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\dal\TokenDAL;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Services\UserService;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;


// PHP 8.1 enums
enum UserAction: string
{
    case POST = 'POST';
    case GET = 'GET';

    public function getResponse(): string
    {
        $postBody = file_get_contents('php://input');
        $postBody = json_decode($postBody, true);

        $token = $_REQUEST['token'] ?? null;
        $tokenEntity = TokenDAL::validate($token);
        $uuid = $tokenEntity->getUuid();

        $user = new UserService();

        $response = [];

        try {
            $statusCode = StatusCode::OK;
            switch ($this){
                case self::POST:
                    $response = $user->update($postBody, $uuid)->getData();
                    break;
                case self::GET:
                    $response = $user->retrieve($uuid)->getData();
                    break;
            }
            if (http_response_code() === StatusCode::OK)
            {
                Http::setHeadersByCode($statusCode);
            }
        } catch (ValidationException $e) {
            // Send 400 http status code
            Http::setHeadersByCode(StatusCode::BAD_REQUEST);

            $response = [
                'errors' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ]
            ];
        }
        return json_encode($response);
    }
}



// PHP 8.0 match - https://stitcher.io/blog/php-8-match-or-switch
// Various HTTP codes explained here: https://www.apiscience.com/blog/7-ways-to-validate-that-your-apis-are-working-correctly/
$userAction = match ($_SERVER['REQUEST_METHOD']) {
    'POST' => UserAction::POST, // send 201
    'GET' => UserAction::GET, // send 200
    //'DELETE' => UserAction::DELETE, // send 204 status code
    default => throw new MethodNotAllowedException(), // send 405
};


// response, as described in https://jsonapi.org/format/#profile-rules
echo $userAction->getResponse();