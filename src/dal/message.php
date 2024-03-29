<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Message;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class MessageDAL extends BaseDAL
{
    public const string TABLE_NAME = 'message';
    public const array COLUMNS = ['uuid', 'from', 'to', 'content', 'send', 'received', 'read'];
    public const array ALLOW_UPDATE_COLUMNS = ['content', 'received', 'read'];
    public const string TIME = 'month'; # month === 2592000s ; week === 604800s

    public static function populatedEntity(array $data): Message
    {
        return (new Message())->setData($data);
    }

    public static function emptyEntity(): Message
    {
        return new Message();
    }

    /**
     * @throws SQL|RedException
     */
    public static function create(Message $entity): Message
    {
        return parent::_create($entity);
    }

    public static function get(string $uuid): Message
    {
        return parent::_get($uuid);
    }

    /**
     * @return Message[]|null[]
     */
    public static function getAll(string $to): array
    {
        return parent::_getAll($to);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Message
    {
        return parent::_remove($uuid);
    }

    /**
     * @throws SQL
     */
    public static function update(Message $entity): Message
    {
        return parent::_update($entity);
    }
}