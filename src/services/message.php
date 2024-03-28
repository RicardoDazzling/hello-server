<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\MessageDAL;
use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Validation\MessageValidation;
use http\Exception\RuntimeException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use Ramsey\Uuid\Uuid;
use RedBeanPHP\RedException\SQL;
use Respect\Validation\Validator as v;

class MessageService implements Serviceable
{
    public function create(array $data): Message
    {
        MessageValidation::isCreationSchemaValid($data);

        $message = new Message();
        $message
            ->setUuid(Uuid::uuid4()->toString())
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setContent($data['content'])
            ->setSend(date(self::DATE_TIME_FORMAT));

        $message = MessageDAL::create($message);
        if ($message->isEmpty()) {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        }
        return $message;
    }

    #[\Override] public function retrieve_all(string $user_uuid = ''): array
    {
        if(empty($user_uuid))
        {
            throw new RuntimeException('Empty UUID!');
        }
        $messages = MessageDAL::getAll($user_uuid);
        if(count($messages) <= 0)
        {
            return [];
        }
        return array_map(function (Message $message): array {
            return $message->getData();
        }, $messages);
    }

    #[\Override] public function retrieve(string $uuid): Message
    {

        if (!v::uuid()->validate($uuid)) {
            throw new ValidationException("Invalid user UUID");
        }

        $message = MessageDAL::get($uuid);

        if ($message->isEmpty()) {
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
        }

        return $message;
    }

    #[\Override] public function update(mixed $postBody, string $uuid): Message
    {
        if (!(v::uuid()->validate($uuid)))
        {
            throw new ValidationException("Invalid message UUID");
        }

        $message = (new Message())->setData($postBody);
        $message->setUuid($uuid);
        $message = MessageDAL::update($message);

        if ($message->isEmpty()) {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        }
        return $message;
    }

    #[\Override] public function remove(?string $uuid): Message
    {
        if(is_null($uuid)){
            throw new ValidationException("UUID is required.");
        }
        if (!v::uuid()->validate($uuid)) {
            throw new ValidationException("Invalid message UUID");
        }
        try {
            $entity = MessageDAL::remove($uuid);
            if ($entity->isEmpty()){
                throw new ValidationException("Unknown user.");
            }
            return $entity;
        }
        catch (SQL)
        {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
            return new Message();
        }
    }
}