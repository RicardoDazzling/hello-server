<?php

namespace DazzRick\HelloServer\Enums;

use DazzRick\HelloServer\DAL\TokenDAL;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Services\FileService;
use DazzRick\HelloServer\Services\GFileService;
use DazzRick\HelloServer\Services\GMessageService;
use DazzRick\HelloServer\Services\GroupService;
use DazzRick\HelloServer\Services\MessageService;
use DazzRick\HelloServer\Services\ParticipantService;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;

enum GroupAction: string
{
    case POST = 'POST';
    case GET = 'GET';
    case DELETE = 'DELETE';

    public function getResponse(): string
    {
        global $jwt;
        $token = getallheaders()['Web-Token'] ?? null;
        $jwt = TokenDAL::validate($token);
        $subresource = $_REQUEST['subresource'] ?? null;
        if(!is_null($subresource) && !in_array($subresource, ['participant', 'file', 'message']))
            throw new BadRequestException("Subresource '$subresource' does not exist.");
        $email = $_REQUEST['email'] ?? null;
        $uuid = $_REQUEST['uuid'] ?? null;
        $service = match ($subresource){
            'participant' => new ParticipantService($email, $uuid),
            'file' => new GFileService(),
            'message' => new GMessageService(),
            default => new GroupService()
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
                        if (in_array($subresource, ['file', 'message'])) {
                            $postBody['from_uuid'] = $jwt->getUuid();
                            $postBody['from_email'] = $jwt->getEmail();
                        }
                        $response = $service->create($postBody);
                    }
                    else $response = $service->update($postBody, $uuid);
                    break;
                case self::GET:
                    if (empty($uuid)) $response = $service->retrieve_all();
                    else if ($subresource === 'participant' && empty($email)) $service->retrieve_all();
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