<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Call;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class CallDAL extends BaseDAL
{
    public const string TABLE_NAME = 'calls';
    public const array COLUMNS = ['from', 'to', 'image', 'audio'];
    public const array ALLOW_UPDATE_COLUMNS = ['image', 'audio'];
    public const string TIME = '';

    public static function populatedEntity(array $data): Call
    {
        return (new Call())->setData($data);
    }

    public static function emptyEntity(): Call
    {
        return new Call();
    }

    /**
     * @throws SQL|RedException
     */
    public static function create(Call $entity): Call
    {
        return parent::_create($entity);
    }

    public static function get(string $uuid): Call
    {
        return parent::_get($uuid);
    }

    /**
     * @return Call[]|null[]
     */
    public static function getAll(string $to): array
    {
        return parent::_getAll($to);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Call
    {
        return parent::_remove($uuid);
    }

    /**
     * @throws SQL|RedException
     */
    public static function update(Call $entity): Call
    {
        return parent::_update($entity);
    }
}