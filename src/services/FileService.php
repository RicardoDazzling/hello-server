<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\FileDAL;
use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Validation\FileValidation;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class FileService extends BaseService implements Serviceable
{

    public const TYPE = 'file';

    protected static function validation(array $data): void
    {
        FileValidation::isCreationSchemaValid($data);
    }

    protected static function emptyEntity(): File
    {
        return new File();
    }

    protected static function populateEntity(array $data): File
    {
        return (new File())->setData($data);
    }

    /**
     * @throws SQL
     * @throws RedException
     */
    protected static function dalCreate(mixed $entity): File
    {
        return FileDAL::create($entity);
    }

    protected static function dalGetAll(string $uuid): array
    {
        return FileDAL::getAll($uuid);
    }

    protected static function dalGet(string $uuid): File
    {
        return FileDAL::get($uuid);
    }

    /**
     * @throws SQL
     */
    protected static function dalUpdate(mixed $entity): File
    {
        return FileDAL::update($entity);
    }

    /**
     * @throws SQL
     */
    protected static function dalRemove(string $uuid): File
    {
        return FileDAL::remove($uuid);
    }

    public function create(array $data): File
    {
        return parent::_create($data);
    }

    public function retrieve_all(string $user_uuid = ''): array
    {
        return parent::_retrieve_all($user_uuid);
    }

    public function retrieve(string $uuid): File
    {
        return parent::_retrieve($uuid);
    }

    public function update(mixed $postBody, string $uuid): File
    {
        return parent::_update($postBody, $uuid);
    }

    public function remove(?string $uuid): File
    {
        return parent::_remove($uuid);
    }

    public function clean(): void
    {
        FileDAL::clean();
    }
}