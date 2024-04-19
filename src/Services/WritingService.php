<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\WritingDAL;
use DazzRick\HelloServer\Entity\Writing;
use DazzRick\HelloServer\Validation\BaseValidation;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class WritingService extends BaseService implements Serviceable
{
    public const TYPE = 'writing';

    protected static function emptyEntity(): Writing { return new Writing(); }

    /**
     * @throws SQL
     * @throws RedException
     */
    protected static function dalCreate(mixed $entity): Writing { return WritingDAL::create($entity); }

    protected static function dalGetAll(string $uuid): array { return WritingDAL::getAll($uuid); }

    protected static function dalGet(string $uuid): Writing { return WritingDAL::get($uuid); }

    /**
     * @throws SQL|RedException
     */
    protected static function dalUpdate(mixed $entity): Writing { return WritingDAL::update($entity); }

    /**
     * @throws SQL
     */
    protected static function dalRemove(string $uuid): Writing { return WritingDAL::remove($uuid); }

    public function create(array $data): Writing { return parent::_create($data); }

    public function retrieve_all(string $user_uuid = ''): array { return parent::_retrieve_all($user_uuid); }

    public function retrieve(string $uuid): Writing { return parent::_retrieve($uuid); }

    public function update(mixed $postBody, string $uuid): Writing { return parent::_update($postBody, $uuid); }

    public function remove(?string $uuid): Writing { return parent::_remove($uuid); }
}