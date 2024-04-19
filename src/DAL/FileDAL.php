<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\File;
use RedBeanPHP\R;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class FileDAL extends BaseDAL
{
    public const TABLE_NAME = 'files';
    public const COLUMNS = ['uuid', 'from_uuid', 'to_uuid', 'content', 'sent', 'received', 'read', 'opened'];
    public const ALLOW_UPDATE_COLUMNS = ['content', 'read', 'opened'];
    public const TIME = 'week';

    public static function emptyEntity(): File { return new File(); } 

    /**
     * @throws SQL|RedException
     */
    public static function create(File $entity): File { return parent::_create($entity); } 

    public static function get(string $uuid): File { return parent::_get($uuid); } 

    /**
     * @return File[]|null[]
     */
    public static function getAll(string $to): array { return parent::_getAll($to); } 

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): File { return parent::_remove($uuid); } 

    public static function update(File $entity): File { return parent::_update($entity); }

    public static function received(): void
    {
        $to = $GLOBALS['jwt']->getUuid();
        $table_name = static::TABLE_NAME;
        $received = intdiv(time(), 60);

        R::exec("UPDATE $table_name SET received=$received WHERE to_uuid='$to'");
    }
}