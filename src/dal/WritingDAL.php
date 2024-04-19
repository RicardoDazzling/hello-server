<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Writing;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class WritingDAL extends BaseDAL
{
    public const TABLE_NAME = 'writings';
    public const COLUMNS = ['from_uuid', 'to_uuid'];
    public const TIME = ''; # month === 43200s ; week === 10080s

    public static function emptyEntity(): Writing { return new Writing(); }

    /**
     * @throws SQL|RedException
     */
    public static function create(Writing $entity): Writing { return parent::_create($entity); }

    public static function get(string $uuid): Writing { return parent::_get($uuid); }

    /**
     * @return Writing[]|null[]
     */
    public static function getAll(string $to): array { return parent::_getAll($to); }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Writing { return parent::_remove($uuid); }

    /**
     * @throws SQL|RedException
     */
    public static function update(Writing $entity): Writing { return parent::_update($entity); }
}