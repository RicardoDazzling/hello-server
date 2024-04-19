<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\CallDAL;
use DazzRick\HelloServer\Entity\Call;
use DazzRick\HelloServer\Validation\CallValidation;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class CallService extends BaseService implements Serviceable
{
    use CallValidation;
    public const TYPE = 'call';

    protected static function emptyEntity(): Call { return new Call(); }

    /**
     * @throws SQL
     * @throws RedException
     */
    protected static function dalCreate(mixed $entity): Call { return CallDAL::create($entity); }

    protected static function dalGetAll(string $uuid): array { return CallDAL::getAll($uuid); }

    protected static function dalGet(string $uuid): Call { return CallDAL::get($uuid); }

    /**
     * @throws SQL|RedException
     */
    protected static function dalUpdate(mixed $entity): Call { return CallDAL::update($entity); }

    /**
     * @throws SQL
     */
    protected static function dalRemove(string $uuid): Call { return CallDAL::remove($uuid); }

    public function create(array $data): Call { return parent::_create($data); }

    public function retrieve_all(string $user_uuid = ''): array { return parent::_retrieve_all($user_uuid); }

    public function retrieve(string $uuid): Call { return parent::_retrieve($uuid); }

    public function update(mixed $postBody, string $uuid): Call { return parent::_update($postBody, $uuid); }

    public function remove(?string $uuid): Call { return parent::_remove($uuid); }
}