<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\GFileDAL;
use DazzRick\HelloServer\DAL\GMessageDAL;
use DazzRick\HelloServer\DAL\ParticipantDAL;
use DazzRick\HelloServer\Entity\Entitable;
use DazzRick\HelloServer\Entity\GFile;
use DazzRick\HelloServer\Entity\GMessage;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\NotFoundException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Validation\GBaseValidation;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

class GBaseService implements Serviceable
{
    use GBaseValidation;

    public const TYPE = '';

    public const SENDER = BaseService::SENDER;

    public const RECEIVER = BaseService::RECEIVER;

    private static function userClassification(GMessage|GFile $entity): string
    {
        $user = $GLOBALS['jwt']->getUuid();
        if ($user === $entity->getFromUuid())
            return static::SENDER;
        $participant = ParticipantDAL::get($user, $entity->getToUuid());
        if ($participant->isEmpty())
            throw new UnAuthorizedException('You don\'t is a participant.');
        if (!$participant->isActive())
            throw new UnAuthorizedException('You are not a Active Member.');
        return static::RECEIVER;
    }

    public static function new_instance(): GFile|GMessage
    {
        return match (static::TYPE){
            GMessageDAL::TABLE_NAME => new GMessage(),
            GFileDAL::TABLE_NAME => new GFile(),
            default => throw new BadRequestException('Unknown table.')
        };
    }

    public static function new_dal_instance(): GFileDAL|GMessageDAL
    {
        return match (static::TYPE) {
            GMessageDAL::TABLE_NAME => new GMessageDAL(),
            GFileDAL::TABLE_NAME => new GFileDAL(),
            default => throw new BadRequestException('Unknown table.')
        };
    }

    public function create(array $data): Entitable
    {
        $data = static::isCreationSchemaValid($data);

        $entity = static::new_instance()
            ->setData($data)
            ->setUuid(Uuid::uuid4()->toString())
            ->setSent(intdiv(time(), 60));

        $participant = ParticipantDAL::get($GLOBALS['jwt']->getUuid(), $entity->getToUuid());
        if ($participant->isEmpty())
            throw new UnAuthorizedException('You don\'t is a participant.');
        if (!$participant->isActive())
            throw new UnAuthorizedException('You are not a Active Member.');

        $entity = static::new_dal_instance()->create($entity);
        if ($entity->isEmpty()) Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        return $entity;
    }

    public function retrieve_all(): array
    {
        $user_uuid = $GLOBALS['jwt']->getUuid();
        $dal = static::new_dal_instance();
        $entities = $dal->getAll($user_uuid);
        if(count($entities) <= 0) return [];
        $dal->received(end($entities)->getUuid());
        return array_map(function (mixed $entity): array { return $entity->getData(true); }, $entities);
    }

    public function retrieve(string $uuid): Entitable
    {
        if (!v::uuid()->validate($uuid)) throw new ValidationException("Invalid message UUID");

        $entity = static::new_dal_instance()->get($uuid);

        if ($entity->isEmpty()) Http::setHeadersByCode(StatusCode::NOT_FOUND);
        else self::userClassification($entity);

        return $entity;
    }

    public function update(array $postBody, string $uuid): Entitable
    {
        if (!(v::uuid()->validate($uuid))) throw new ValidationException("Invalid update UUID");
        $dal = static::new_dal_instance();
        $oldEntity = $dal->get($uuid);
        if ($oldEntity->isEmpty()) throw new ValidationException('UUID doens\'t exists');
        $classification = self::userClassification($oldEntity);

        static::isUpdateSchemaValid($postBody, $classification, $oldEntity);

        $entity = static::new_instance()->setData($postBody)->setUuid($uuid);
        $entity = $dal->update($entity);

        if ($entity->isEmpty()) Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);

        return $entity->setEmpty($oldEntity->getData());
    }

    public function remove(?string $uuid): Entitable
    {
        if(is_null($uuid)) throw new ValidationException("UUID is required.");
        if (!v::uuid()->validate($uuid)) throw new ValidationException("Invalid message UUID");
        $dal = static::new_dal_instance();
        $old_entity = $dal::get($uuid);
        if($old_entity->isEmpty())
            throw new NotFoundException('Message not found');
        $classification = self::userClassification($old_entity);
        if ($classification !== self::SENDER)
            throw new UnAuthorizedException('Only the sender user can remove the message.');
        $entity = static::new_dal_instance()->remove($uuid);
        if ($entity->isEmpty()) throw new ValidationException("Unknown entity.");
        return $entity;
    }
}