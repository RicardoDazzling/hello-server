<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\File;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class FileDAL extends BaseDAL
{
    public const TABLE_NAME = 'files';
    public const COLUMNS = ['uuid', 'from', 'to', 'content', 'send', 'received', 'read', 'open'];
    public const ALLOW_UPDATE_COLUMNS = ['content', 'received', 'read', 'open'];
    public const TIME = 'week';

    public static function populatedEntity(array $data): File
    {
        return (new File())->setData($data);
    }

    public static function emptyEntity(): File
    {
        return new File();
    }

    /**
     * @throws SQL|RedException
     */
    public static function create(File $entity): File
    {
        return parent::_create($entity);
    }

    public static function get(string $uuid): File
    {
        return parent::_get($uuid);
    }

    /**
     * @return File[]|null[]
     */
    public static function getAll(string $to): array
    {
        return parent::_getAll($to);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): File
    {
        return parent::_remove($uuid);
    }

    /**
     * @throws SQL
     */
    public static function update(File $entity): File
    {
        return parent::_update($entity);
    }
}