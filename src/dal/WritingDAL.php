<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Writing;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class WritingDAL extends BaseDAL
{
    public const TABLE_NAME = 'writings';
    public const COLUMNS = ['from', 'to', 'image', 'audio'];
    public const ALLOW_UPDATE_COLUMNS = ['image', 'audio'];
    public const TIME = ''; # month === 2592000s ; week === 604800s

    public static function populatedEntity(array $data): Writing
    {
        return (new Writing())->setData($data);
    }

    public static function emptyEntity(): Writing
    {
        return new Writing();
    }

    /**
     * @throws SQL|RedException
     */
    public static function create(Writing $entity): Writing
    {
        return parent::_create($entity);
    }

    public static function get(string $uuid): Writing
    {
        return parent::_get($uuid);
    }

    /**
     * @return Writing[]|null[]
     */
    public static function getAll(string $to): array
    {
        return parent::_getAll($to);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Writing
    {
        return parent::_remove($uuid);
    }

    /**
     * @throws SQL|RedException
     */
    public static function update(Writing $entity): Writing
    {
        return parent::_update($entity);
    }
}