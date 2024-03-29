<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\MessageDAL;
use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Validation\MessageValidation;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use Ramsey\Uuid\Uuid;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;
use Respect\Validation\Validator as v;
use RuntimeException;

class BaseService
{
    public const string DATE_TIME_FORMAT = Serviceable::DATE_TIME_FORMAT;

    protected static function validation(array $data): void
    {
        MessageValidation::isCreationSchemaValid($data);
    }

    protected static function emptyEntity(): Message|File
    {
        return new Message();
    }

    protected static function populateEntity(array $data): Message|File
    {
        return (new Message())->setData($data);
    }

    /**
     * @throws SQL
     * @throws RedException
     */
    protected static function dalCreate(Message|File $entity): Message|File
    {
        return MessageDAL::create($entity);
    }

    protected static function dalGetAll(string $uuid): array
    {
        return MessageDAL::getAll($uuid);
    }

    protected static function dalGet(string $uuid): Message|File
    {
        return MessageDAL::get($uuid);
    }

    /**
     * @throws SQL
     */
    protected static function dalUpdate(Message|File $entity): Message|File
    {
        return MessageDAL::update($entity);
    }

    /**
     * @throws SQL
     */
    protected static function dalRemove(string $uuid): Message|File
    {
        return MessageDAL::remove($uuid);
    }

    public function _create(array $data): Message|File
    {
        self::validation($data);

        $message = self::emptyEntity();
        $message
            ->setUuid(Uuid::uuid4()->toString())
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setContent($data['content'])
            ->setSend(date(self::DATE_TIME_FORMAT));

        $message = self::dalCreate($message);
        if ($message->isEmpty()) {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        }
        return $message;
    }

    public function _retrieve_all(string $user_uuid = ''): array
    {
        if(empty($user_uuid))
        {
            throw new RuntimeException('Empty UUID!');
        }
        $messages = self::dalGetAll($user_uuid);
        if(count($messages) <= 0)
        {
            return [];
        }
        return array_map(function (Message|File $message): array {
            return $message->getData();
        }, $messages);
    }

    public function _retrieve(string $uuid): Message|File
    {
        if (!v::uuid()->validate($uuid)) {
            throw new ValidationException("Invalid user UUID");
        }

        $message = self::dalGet($uuid);

        if ($message->isEmpty()) {
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
        }

        return $message;
    }

    public function _update(mixed $postBody, string $uuid): Message|File
    {
        if (!(v::uuid()->validate($uuid)))
        {
            throw new ValidationException("Invalid message/file UUID");
        }

        $message = self::populateEntity($postBody);
        $message->setUuid($uuid);
        try
        {
            $message = self::dalUpdate($message);
        }catch (SQL){}

        if ($message->isEmpty()) {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        }
        return $message;
    }

    public function _remove(?string $uuid): Message|File
    {
        if(is_null($uuid)){
            throw new ValidationException("UUID is required.");
        }
        if (!v::uuid()->validate($uuid)) {
            throw new ValidationException("Invalid message UUID");
        }
        try {
            $entity = self::dalRemove($uuid);
            if ($entity->isEmpty()){
                throw new ValidationException("Unknown user.");
            }
            return $entity;
        }
        catch (SQL)
        {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
            return self::emptyEntity();
        }
    }
}