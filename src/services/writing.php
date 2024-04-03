<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\WritingDAL;
use DazzRick\HelloServer\Entity\Writing;
use DazzRick\HelloServer\Validation\BaseValidation;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class WritingService extends BaseService implements Serviceable
{

    protected static function validation(array $data): void
    {
        BaseValidation::isCreationSchemaValid($data);
    }

    protected static function emptyEntity(): Writing
    {
        return new Writing();
    }

    protected static function populateEntity(array $data): Writing
    {
        return (new Writing())->setData($data);
    }

    protected static function populateCreateEntity(array $data): Writing
    {
        return self::emptyEntity()
            ->setFrom($data['from'])
            ->setTo($data['to']);
    }

    /**
     * @throws SQL
     * @throws RedException
     */
    protected static function dalCreate(mixed $entity): Writing
    {
        return WritingDAL::create($entity);
    }

    protected static function dalGetAll(string $uuid): array
    {
        return WritingDAL::getAll($uuid);
    }

    protected static function dalGet(string $uuid): Writing
    {
        return WritingDAL::get($uuid);
    }

    /**
     * @throws SQL|RedException
     */
    protected static function dalUpdate(mixed $entity): Writing
    {
        return WritingDAL::update($entity);
    }

    /**
     * @throws SQL
     */
    protected static function dalRemove(string $uuid): Writing
    {
        return WritingDAL::remove($uuid);
    }

    public function create(array $data): Writing
    {
        return parent::_create($data);
    }

    #[\Override] public function retrieve_all(string $user_uuid = ''): array
    {
        return parent::_retrieve_all($user_uuid);
    }

    #[\Override] public function retrieve(string $uuid): Writing
    {
        return parent::_retrieve($uuid);
    }

    #[\Override] public function update(mixed $postBody, string $uuid): Writing
    {
        return parent::_update($postBody, $uuid);
    }

    #[\Override] public function remove(?string $uuid): Writing
    {
        return parent::_remove($uuid);
    }
}