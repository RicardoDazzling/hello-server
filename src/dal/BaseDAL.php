<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Call;
use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Entity\Lost;
use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Entity\Writing;
use RedBeanPHP\R;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class BaseDAL
{
    public const TABLE_NAME = '';
    public const COLUMNS = [];
    public const ALLOW_UPDATE_COLUMNS = [];
    public const TIME = 'month'; # month === 2592000s ; week === 604800s

    /**
     * @param array $data
     * @return Message|File|Call|Lost|Writing
     */
    public static function populatedEntity(array $data): mixed
    {
        return (new Message())->setData($data);
    }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    public static function emptyEntity(): mixed
    {
        return new Message();
    }

    /**
     * @param Message|File|Call|Lost|Writing $entity
     * @return Message|File|Call|Lost|Writing
     * @throws SQL|RedException
     */
    protected static function _create(mixed $entity): mixed
    {
        $bean = R::dispense(self::TABLE_NAME);
        foreach (self::COLUMNS as $column)
        {
            $bean->__set($column, $entity->__get($column));
        }

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string')
        {
            return $entity->setId($id);
        }
        return self::emptyEntity();
    }

    private static function _find(string $uuid): NULL|\RedBeanPHP\OODBBean
    {
        $bindings = ['uuid' => $uuid];
        return R::findOne(self::TABLE_NAME, 'uuid = :uuid ', $bindings);
    }

    /**
     * @param string $uuid
     * @return Message|File|Call|Lost|Writing
     */
    protected static function _get(string $uuid): mixed
    {
        $bean = self::_find($uuid);

        if(is_null($bean))
        {
            return self::emptyEntity();
        }
        return self::populatedEntity($bean->export());
    }

    /**
     * @return Message[]|File[]|Call[]|Lost[]|Writing[]|array<void>
     */
    protected static function _getAll(string $to): array
    {
        $bindings = ['to' => $to];
        $messages = R::findAll(self::TABLE_NAME, 'to = :to ', $bindings);
        if (count($messages) <= 0)
        {
            return [];
        }
        return array_map(function (object $bean): object {
            return self::populatedEntity($bean->export());
        }, $messages);
    }

    /**
     * @return Message|File|Call|Lost|Writing
     * @throws SQL
     */
    protected static function _remove(string $uuid): mixed
    {
        $bean = self::_find($uuid);

        if (is_null($bean))
        {
            return self::emptyEntity();
        }

        $entity = self::populatedEntity($bean->export());
        $works = (bool)R::trash($bean);

        if ($works)
        {
            return $entity;
        }
        throw new SQL('Remove error!');
    }

    /**
     * @param Message|File|Call|Lost|Writing $entity
     * @return Message|File|Call|Lost|Writing
     * @throws SQL|RedException
     */
    protected static function _update(mixed $entity): mixed
    {
        $bean = self::_find($entity->getUuid());

        // If the user exists, update it
        if (is_null($bean)) {
            return self::emptyEntity();
        }

        foreach (self::ALLOW_UPDATE_COLUMNS as $column)
        {
            $value = $entity->__get($column);
            if(!empty($value)) $bean->__set($column, $value);
        }

        // save the user
        $id = R::store($bean);

        if(gettype($id) === 'integer' || gettype($id) === 'string')
        {
            return $entity->setId($id);
        }

        return self::emptyEntity();
    }

    public static function clean(): void
    {
        $time = time();
        $time -= self::TIME === 'month' ? 2592000 : 604800;
        $bindings = ['time' => $time];
        $will_remove_array = R::findAll(self::TABLE_NAME, 'send < :time ', $bindings);
        if(count($will_remove_array) <= 0) return;
        foreach ($will_remove_array as $will_remove)
        {
            R::trash($will_remove);
        }
    }
}