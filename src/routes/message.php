<?php
namespace DazzRick\HelloServer\Routes;

use DazzRick\HelloServer\dal\TokenDAL;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Services\MessageService;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;


// PHP 8.1 enums
enum MessageAction: string
{
    case POST = 'POST';
    case GET = 'GET';
    case DELETE = 'DELETE';

    public function getResponse(): string
    {
        $postBody = file_get_contents('php://input');
        $postBody = json_decode($postBody, true);

        $token = $_REQUEST['token'] ?? null;
        $uuid = $_REQUEST['uuid'] ?? null;
        $tokenEntity = TokenDAL::validate($token);

        $message = new MessageService();

        $response = [];

        try {
            $statusCode = StatusCode::OK;
            switch ($this){
                case self::POST:
                    if (is_null($uuid))
                    {
                        $statusCode = StatusCode::CREATED;
                        $postBody['from'] = $tokenEntity->getUuid();
                        $response = $message->create($postBody);
                        break;
                    }
                    $response = $message->update($postBody, $uuid);
                    break;
                case self::GET:
                    if (is_null($uuid))
                    {
                        $response = $message->retrieve_all($tokenEntity->getUuid());
                        break;
                    }
                    $response = $message->retrieve($uuid);
                    break;
                case self::DELETE:
                    $statusCode = StatusCode::NO_CONTENT;
                    $message->remove($uuid);
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
    'POST' => MessageAction::POST, // send 201
    'GET' => MessageAction::GET, // send 200
    'DELETE' => MessageAction::DELETE, // send 204 status code
    default => throw new MethodNotAllowedException(), // send 405
};


// response, as described in https://jsonapi.org/format/#profile-rules
echo $userAction->getResponse();