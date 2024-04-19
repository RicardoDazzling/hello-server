<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Lost;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class LostDAL extends BaseDAL
{
    public const TABLE_NAME = 'losts';
    public const COLUMNS = ['from_uuid', 'to_uuid', 'type', 'sent'];
    public const TIME = 'week';

    public static function populatedEntity(array $data): Lost { return (new Lost())->setData($data); }

    public static function emptyEntity(): Lost { return new Lost(); }

    /**
     * @throws SQL|RedException
     */
    public static function create(Lost $entity): Lost { return parent::_create($entity); }

    public static function get(string $uuid): Lost { return parent::_get($uuid); }

    /**
     * @return Lost[]|null[]
     */
    public static function getAll(string $to): array { return parent::_getAll($to); }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Lost { return parent::_remove($uuid); }

    /**
     * @throws BadRequestException
     */
    public static function update(Lost $entity): Lost { throw new BadRequestException('Lost update is not eligible.'); }
}