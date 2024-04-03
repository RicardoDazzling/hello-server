<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\MessageDAL;
use DazzRick\HelloServer\Entity\Call;
use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Entity\Lost;
use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Entity\Writing;
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

    /**
     * @return Message|File|Call|Lost|Writing
     */
    protected static function emptyEntity(): mixed
    {
        return new Message();
    }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    protected static function populateEntity(array $data): mixed
    {
        return (new Message())->setData($data);
    }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    protected static function populateCreateEntity(array $data): mixed
    {
        return self::emptyEntity()
            ->setUuid(Uuid::uuid4()->toString())
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setContent($data['content'])
            ->setSend(time());
    }

    /**
     * @param Message|File|Call|Lost|Writing $entity
     * @return Message|File|Call|Lost|Writing
     * @throws SQL
     * @throws RedException
     */
    protected static function dalCreate(mixed $entity): mixed
    {
        return MessageDAL::create($entity);
    }

    protected static function dalGetAll(string $uuid): array
    {
        return MessageDAL::getAll($uuid);
    }

    /**
     * @param string $uuid
     * @return Message|File|Call|Lost|Writing
     */
    protected static function dalGet(string $uuid): mixed
    {
        return MessageDAL::get($uuid);
    }

    /**
     * @param Message|File|Call|Lost|Writing $entity
     * @return Message|File|Call|Lost|Writing
     * @throws SQL
     */
    protected static function dalUpdate(mixed $entity): mixed
    {
        return MessageDAL::update($entity);
    }

    /**
     * @param string $uuid
     * @return Message|File|Call|Lost|Writing
     * @throws SQL
     */
    protected static function dalRemove(string $uuid): mixed
    {
        return MessageDAL::remove($uuid);
    }

    /**
     * @param array $data
     * @return Message|File|Call|Lost|Writing
     * @throws RedException
     * @throws SQL
     */
    public function _create(array $data): mixed
    {
        self::validation($data);

        $message = self::populateCreateEntity($data);

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

    /**
     * @return Message|File|Call|Lost|Writing
     */
    public function _retrieve(string $uuid): mixed
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

    /**
     * @return Message|File|Call|Lost|Writing
     */
    public function _update(mixed $postBody, string $uuid): mixed
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

    /**
     * @return Message|File|Call|Lost|Writing
     */
    public function _remove(?string $uuid): mixed
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
                throw new ValidationException("Unknown entity.");
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