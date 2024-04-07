<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\LostDAL;
use DazzRick\HelloServer\Entity\Lost;
use DazzRick\HelloServer\Validation\LostValidation;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class LostService extends BaseService implements Serviceable
{
    public string $_type = 'lost';

    protected static function validation(array $data): void
    {
        LostValidation::isCreationSchemaValid($data);
    }

    protected static function emptyEntity(): Lost
    {
        return new Lost();
    }

    protected static function populateEntity(array $data): Lost
    {
        return (new Lost())->setData($data);
    }

    protected static function populateCreateEntity(array $data): Lost
    {
        return self::emptyEntity()
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setType($data['type'])
            ->setSend(time());
    }

    /**
     * @throws SQL
     * @throws RedException
     */
    protected static function dalCreate(mixed $entity): Lost
    {
        return LostDAL::create($entity);
    }

    protected static function dalGetAll(string $uuid): array
    {
        return LostDAL::getAll($uuid);
    }

    protected static function dalGet(string $uuid): Lost
    {
        return LostDAL::get($uuid);
    }

    /**
     * @throws SQL|RedException
     */
    protected static function dalUpdate(mixed $entity): Lost
    {
        return LostDAL::update($entity);
    }

    /**
     * @throws SQL
     */
    protected static function dalRemove(string $uuid): Lost
    {
        return LostDAL::remove($uuid);
    }

    public function create(array $data): Lost
    {
        return parent::_create($data);
    }

    public function retrieve_all(string $user_uuid = ''): array
    {
        return parent::_retrieve_all($user_uuid);
    }

    public function retrieve(string $uuid): Lost
    {
        return parent::_retrieve($uuid);
    }

    public function update(mixed $postBody, string $uuid): Lost
    {
        return parent::_update($postBody, $uuid);
    }

    public function remove(?string $uuid): Lost
    {
        return parent::_remove($uuid);
    }
}