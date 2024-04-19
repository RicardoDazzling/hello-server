<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Message;
use RedBeanPHP\R;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class MessageDAL extends BaseDAL
{
    public const TABLE_NAME = 'messages';
    public const COLUMNS = ['uuid', 'from_uuid', 'to_uuid', 'content', 'sent', 'received', 'read'];
    public const ALLOW_UPDATE_COLUMNS = ['content', 'read'];
    public const TIME = 'month'; # month === 43200s ; week === 10080s

    public static function emptyEntity(): Message { return new Message(); }

    /**
     * @throws SQL|RedException
     */
    public static function create(Message $entity): Message { return parent::_create($entity); }

    public static function get(string $uuid): Message { return parent::_get($uuid); }

    /**
     * @return Message[]|null[]
     */
    public static function getAll(string $to): array { return parent::_getAll($to); }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Message { return parent::_remove($uuid); }

    public static function update(Message $entity): Message { return parent::_update($entity); }

    public static function received(): void
    {
        $to = $GLOBALS['jwt']->getUuid();
        $table_name = static::TABLE_NAME;
        $received = intdiv(time(), 60);

        R::exec("UPDATE $table_name SET received=$received WHERE to_uuid='$to'");
    }
}