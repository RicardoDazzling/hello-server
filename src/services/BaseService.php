<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\MessageDAL;
use DazzRick\HelloServer\Entity\Call;
use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Entity\Lost;
use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Entity\Writing;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Validation\BaseValidation;
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
    use BaseValidation;
    
    public const TYPE = '';

    public const SENDER = 'sender';

    public const RECEIVER = 'receiver';

    /**
     * Function destined to verify the user access to a message.
     * @param Message|File|Call|Lost|Writing $entity
     * @return self::SENDER | self::RECEIVER
     * @throws UnAuthorizedException
     */
    private static function userClassification(mixed $entity): string
    {
        global $jwt;
        $uuid = $jwt->getUuid();
        if($entity->getFromUuid() === $uuid) return self::SENDER;
        if($entity->getToUuid() === $uuid) return self::RECEIVER;
        throw new UnAuthorizedException('Unauthorized access to resource entity.');
    }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    protected static function emptyEntity(): mixed { return new Message(); }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    protected static function populateEntity(array $data): mixed { return static::emptyEntity()->setData($data); }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    protected static function populateCreateEntity(array $data): mixed
    {
        return static::emptyEntity()
            ->setUuid(Uuid::uuid4()->toString())
            ->setData($data);
    }

    /**
     * @param Message|File|Call|Lost|Writing $entity
     * @return Message|File|Call|Lost|Writing
     * @throws SQL
     * @throws RedException
     */
    protected static function dalCreate(mixed $entity): mixed { return MessageDAL::create($entity); }

    protected static function dalGetAll(string $uuid): array { return MessageDAL::getAll($uuid); }

    /**
     * @param string $uuid
     * @return Message|File|Call|Lost|Writing
     */
    protected static function dalGet(string $uuid): mixed { return MessageDAL::get($uuid); }

    /**
     * @param Message|File|Call|Lost|Writing $entity
     * @return Message|File|Call|Lost|Writing
     * @throws SQL
     */
    protected static function dalUpdate(mixed $entity): mixed { return MessageDAL::update($entity); }

    /**
     * @param string $uuid
     * @return Message|File|Call|Lost|Writing
     * @throws SQL
     */
    protected static function dalRemove(string $uuid): mixed { return MessageDAL::remove($uuid); }

    protected static function dalReceived(): void { MessageDAL::received(); }

    /**
     * @param array $data
     * @return Message|File|Call|Lost|Writing
     * @throws RedException
     * @throws SQL
     */
    public function _create(array $data): mixed
    {
        $data = static::isCreationSchemaValid($data);

        $entity = static::populateCreateEntity($data);

        $entity = static::dalCreate($entity);
        if ($entity->isEmpty()) Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        return $entity;
    }

    public function _retrieve_all(string $user_uuid = ''): array
    {
        if(empty($user_uuid)) throw new RuntimeException('Empty UUID!');
        $entities = static::dalGetAll($user_uuid);
        if (in_array(static::TYPE, ['message', 'file'])) {
            if(empty($_REQUEST['filter'])) $_REQUEST['filter'] = 'sent';
            static::dalReceived();
        }
        if(count($entities) <= 0) return [];
        return array_map(function (mixed $entity): array { return $entity->getData(true); }, $entities);
    }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    public function _retrieve(string $uuid): mixed
    {
        if (!v::uuid()->validate($uuid)) throw new ValidationException("Invalid user UUID");

        $entity = static::dalGet($uuid);

        if ($entity->isEmpty()) Http::setHeadersByCode(StatusCode::NOT_FOUND);
        else self::userClassification($entity);

        return $entity;
    }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    public function _update(mixed $postBody, string $uuid): mixed
    {
        if (!(v::uuid()->validate($uuid))) throw new ValidationException("Invalid update UUID");
        $oldEntity = static::dalGet($uuid);
        if ($oldEntity->isEmpty()) throw new ValidationException('UUID doens\'t exists');
        $classification = self::userClassification($oldEntity);

        $postBody = static::isUpdateSchemaValid($postBody, $classification, $oldEntity);

        $entity = static::populateEntity($postBody)->setUuid($uuid);
        $entity = static::dalUpdate($entity);

        if ($entity->isEmpty()) Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);

        return $entity->setEmpty($oldEntity->getData());
    }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    public function _remove(?string $uuid): mixed
    {
        if(is_null($uuid)) throw new ValidationException("UUID is required.");
        if (!v::uuid()->validate($uuid)) throw new ValidationException("Invalid message UUID");
        try {
            $entity = static::dalRemove($uuid);
            if ($entity->isEmpty()) throw new ValidationException("Unknown entity.");
            return $entity;
        }
        catch (SQL)
        {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
            return static::emptyEntity();
        }
    }
}