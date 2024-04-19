<?php

namespace DazzRick\HelloServer\Enums;

use DazzRick\HelloServer\DAL\TokenDAL;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Services\UserService;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

enum UserAction: string
{
    case POST = 'POST';
    case GET = 'GET';

    public function getResponse(): string
    {
        $token = getallheaders()['Web-Token'] ?? null;
        $tokenEntity = TokenDAL::validate($token);
        $uuid = $tokenEntity->getUuid();
        $search_uuid = $_REQUEST['search_uuid'] ?? null;
        $search_email = $_REQUEST['search_email'] ?? null;

        $user = new UserService();

        $response = [];

        try {
            $statusCode = StatusCode::OK;
            switch ($this){
                case self::POST:
                    $postBody = file_get_contents('php://input');
                    if(empty($postBody)) throw new BadRequestException('Post Body empty.');
                    $postBody = json_decode($postBody, true);
                    if(is_null($postBody)) throw new BadRequestException('Error while decoding the Post Body.');
                    $response = $user->update($postBody, $uuid)->getData();
                    break;
                case self::GET:
                    if (!empty($search_uuid)) $response = $user->search(uuid: $search_uuid, email: $search_email)->getData();
                    else $response = $user->retrieve($uuid)->getData();
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
