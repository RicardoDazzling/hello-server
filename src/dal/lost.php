<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Lost;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class LostDAL extends BaseDAL
{
    public const string TABLE_NAME = 'losts';
    public const array COLUMNS = ['from', 'to', 'image', 'audio'];
    public const array ALLOW_UPDATE_COLUMNS = ['image', 'audio'];
    public const string TIME = 'week';

    public static function populatedEntity(array $data): Lost
    {
        return (new Lost())->setData($data);
    }

    public static function emptyEntity(): Lost
    {
        return new Lost();
    }

    /**
     * @throws SQL|RedException
     */
    public static function create(Lost $entity): Lost
    {
        return parent::_create($entity);
    }

    public static function get(string $uuid): Lost
    {
        return parent::_get($uuid);
    }

    /**
     * @return Lost[]|null[]
     */
    public static function getAll(string $to): array
    {
        return parent::_getAll($to);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Lost
    {
        return parent::_remove($uuid);
    }

    /**
     * @throws SQL|RedException
     */
    public static function update(Lost $entity): Lost
    {
        return parent::_update($entity);
    }
}