<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Exceptions\ValidationException;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class MessageDAL
{
    public const string TABLE_NAME = 'message';

    /**
     * @throws SQL
     */
    public static function create(Message $entity): Message
    {
        $bean = R::dispense(self::TABLE_NAME);
        $bean->uuid = $entity->getUuid();
        $bean->from = $entity->getFrom();
        $bean->to = $entity->getTo();
        $bean->content = $entity->getContent();
        $bean->send = $entity->getSend();
        $bean->received = $entity->getReceived();
        $bean->read = $entity->getRead();

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string')
        {
            return $entity->setId($id);
        }
        return new Message();
    }

    private static function _find(string $uuid): NULL|\RedBeanPHP\OODBBean
    {
        $bindings = ['uuid' => $uuid];
        return R::findOne(self::TABLE_NAME, 'uuid = :uuid ', $bindings);
    }

    public static function get(string $uuid): Message
    {
        $bean = self::_find($uuid);

        if(is_null($bean))
        {
            return new Message();
        }
        return (new Message())->setData($bean->export());
    }

    /**
     * @return Message[]|null[]
     */
    public static function getAll(string $to): array
    {
        $bindings = ['to' => $to];
        $messages = R::findAll(self::TABLE_NAME, 'to = :to ', $bindings);
        if (count($messages) <= 0)
        {
            return [];
        }
        return array_map(function (object $bean): object {
            return (new Message())->setData($bean->export());
        }, $messages);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Message
    {
        $bean = self::_find($uuid);

        if (is_null($bean))
        {
            return new Message();
        }

        $entity = (new Message())->setData($bean->export());
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
    public static function update(Message $entity): Message
    {
        $bean = self::_find($entity->getUuid());

        // If the user exists, update it
        if (is_null($bean)) {
            return new Message();
        }

        $content = $entity->getContent();
        $received = $entity->getReceived();
        $read = $entity->getRead();

        if ($content) {
            $bean->content = $content;
        }

        if ($received) {
            $bean->received = $received;
        }

        if ($read) {
            $bean->read = $read;
        }

        // save the user
        $id = R::store($bean);

        if(gettype($id) === 'integer' || gettype($id) === 'string')
        {
            return $entity->setId($id);
        }

        return new Message();
    }
}