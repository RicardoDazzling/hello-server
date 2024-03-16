<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\Exceptions\ValidationException;

use DazzRick\HelloServer\Services\UserService;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

require_once dirname(__DIR__) . '/services/user.php';

// PHP 8.1 enums
enum UserAction: string
{
    case CREATE = 'create';
    case RETRIEVE_ALL = 'retrieve_all';
    case RETRIEVE = 'retrieve';
    case REMOVE = 'remove';
    case UPDATE = 'update';

    public function getResponse(): string
    {
        $postBody = file_get_contents('php://input');
        $postBody = json_decode($postBody);

        // Null coalescing operator
        $uuid = $_REQUEST['uuid'] ?? null;

        // TODO Remove the hard-coded values from here
        $user = new UserService();

        try {
            $response = match ($this) {
                self::CREATE => $user->create($postBody),
                self::RETRIEVE_ALL => $user->retrieve_all(),
                self::RETRIEVE => $user->retrieve($uuid),
                self::REMOVE => $user->remove($postBody),
                self::UPDATE => $user->update($postBody),
            };
            if (http_response_code() === StatusCode::OK)
            {
                switch ($this)
                {
                    case self::CREATE:
                        Http::setHeadersByCode(StatusCode::CREATED);
                        break;
                    //case self::RETRIEVE_ALL || self::RETRIEVE:
                    //    Http::setHeadersByCode(StatusCode::OK);
                    //    break;
                    case self::REMOVE:
                        Http::setHeadersByCode(StatusCode::NO_CONTENT);
                        break;
                    case self::UPDATE:
                        Http::setHeadersByCode(StatusCode::ACCEPTED);
                }
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


$action = $_REQUEST['action'] ?? null;

// PHP 8.0 match - https://stitcher.io/blog/php-8-match-or-switch
// Various HTTP codes explained here: https://www.apiscience.com/blog/7-ways-to-validate-that-your-apis-are-working-correctly/
$userAction = match ($action) {
    'create' => UserAction::CREATE, // send 201
    'retrieve' => UserAction::RETRIEVE, // send 200
    'remove' => UserAction::REMOVE, // send 204 status code
    'update' => UserAction::UPDATE, //
    default => UserAction::RETRIEVE_ALL, // send 200
};


// response, as described in https://jsonapi.org/format/#profile-rules
echo $userAction->getResponse();