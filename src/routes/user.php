<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\Exceptions\NotFoundException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Services\UserService;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;


// PHP 8.1 enums
enum UserAction: string
{
    case POST = 'POST';
    case GET = 'GET';
    case DELETE = 'DELETE';

    public function getResponse(): string
    {
        $postBody = file_get_contents('php://input');
        $postBody = json_decode($postBody);

        $uuid = $_REQUEST['uuid'] ?? null;

        $user = new UserService();

        try {
            $statusCode = StatusCode::OK;
            switch ($this){
                case self::POST:
                    if (is_null($uuid))
                    {
                        $statusCode = StatusCode::CREATED;
                        $response = $user->create($postBody);
                        break;
                    }
                    $response = $user->update($postBody, $uuid);
                    break;
                case self::GET:
                    if (is_null($uuid))
                    {
                        $response = $user->retrieve_all();
                        break;
                    }
                    $response = $user->retrieve($uuid);
                    break;
                case self::DELETE:
                    $statusCode = StatusCode::NO_CONTENT;
                    $user->remove($uuid);
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
    'DELETE' => UserAction::DELETE, // send 204 status code
    default => throw new NotFoundException(), // send 200
};


// response, as described in https://jsonapi.org/format/#profile-rules
echo $userAction->getResponse();