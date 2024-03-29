<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Entity\Message;
use RedBeanPHP\R;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class BaseDAL
{
    public const string TABLE_NAME = '';
    public const array COLUMNS = [];
    public const array ALLOW_UPDATE_COLUMNS = [];
    public const string TIME = 'month'; # month === 2592000s ; week === 604800s

    public static function populatedEntity(array $data): Message|File
    {
        return (new Message())->setData($data);
    }

    public static function emptyEntity(): Message|File
    {
        return new Message();
    }

    /**
     * @throws SQL|RedException
     */
    protected static function _create(Message|File $entity): Message|File
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

    protected static function _get(string $uuid): Message|File
    {
        $bean = self::_find($uuid);

        if(is_null($bean))
        {
            return self::emptyEntity();
        }
        return self::populatedEntity($bean->export());
    }

    /**
     * @return Message[]|File[]|null[]
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
     * @throws SQL
     */
    protected static function _remove(string $uuid): Message|File
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
     * @throws SQL
     */
    protected static function _update(Message|File $entity): Message|File
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

    protected static function clean(): void
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