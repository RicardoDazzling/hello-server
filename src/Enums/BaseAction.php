<?php

namespace DazzRick\HelloServer\Enums;

use DazzRick\HelloServer\DAL\TokenDAL;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Services\CallService;
use DazzRick\HelloServer\Services\FileService;
use DazzRick\HelloServer\Services\LostService;
use DazzRick\HelloServer\Services\MessageService;
use DazzRick\HelloServer\Services\WritingService;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

enum BaseAction: string
{
    case POST = 'POST';
    case GET = 'GET';
    case DELETE = 'DELETE';

    public function getResponse(): string
    {
        global $jwt;
        $token = getallheaders()['Web-Token'] ?? null;
        $jwt = TokenDAL::validate($token);
        $uuid = $_REQUEST['uuid'] ?? null;
        $service = match ($_REQUEST['resource']){
            'file' => new FileService(),
            'call' => new CallService(),
            'lost' => new LostService(),
            'writing' => new WritingService(),
            default => new MessageService()
        };

        $response = [];
        try {
            $statusCode = StatusCode::OK;
            switch ($this){
                case self::POST:
                    $postBody = file_get_contents('php://input');
                    if(empty($postBody)) throw new BadRequestException('Post Body empty.');
                    $postBody = json_decode($postBody, true);
                    if(is_null($postBody)) throw new BadRequestException('Error while decoding the Post Body.');
                    if (empty($uuid))
                    {
                        $statusCode = StatusCode::CREATED;
                        $postBody['from_uuid'] = $jwt->getUuid();
                        $postBody['from_email'] = $jwt->getEmail();
                        $response = $service->create($postBody);
                    }
                    else $response = $service->update($postBody, $uuid);
                    break;
                case self::GET:
                    if (empty($uuid)) $response = $service->retrieve_all($jwt->getUuid());
                    else $response = $service->retrieve($uuid);
                    break;
                case self::DELETE:
                    $statusCode = StatusCode::NO_CONTENT;
                    $service->remove($uuid);
            }
            if (http_response_code() === StatusCode::OK && $statusCode !== StatusCode::OK)
                Http::setHeadersByCode($statusCode);
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
        return json_encode(is_array($response)?$response:$response->getData(true));
    }
}
