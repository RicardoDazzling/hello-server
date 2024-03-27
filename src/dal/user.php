<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\User;
use DazzRick\HelloServer\Exceptions\ValidationException;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

final class UserDAL
{
    public const string TABLE_NAME = 'users';

    /**
     * @throws SQL
     */
    public static function create(User $entity): User
    {
        if(!is_null(self::get_by_email($entity->getEmail())))
        {
            throw new ValidationException("EMail already exists.");
        }
        $bean = R::dispense(self::TABLE_NAME);
        $bean->uuid = $entity->getUuid();
        $bean->status = $entity->getStatus();
        $bean->name = $entity->getName();
        $bean->email = $entity->getEmail();
        $bean->default = $entity->getDefault();
        $bean->created_date = $entity->getCreationDate();

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string')
        {
            return $entity->setId($id);
        }
        return new User();
    }
    
    private static function _find(string $uuid): NULL|\RedBeanPHP\OODBBean
    {
        $bindings = ['uuid' => $uuid];
        return R::findOne(self::TABLE_NAME, 'uuid = :uuid ', $bindings);
    }

    public static function get(string $uuid): User
    {
        $bean = self::_find($uuid);

        if(is_null($bean))
        {
            return new User();
        }
        return (new User())->setData($bean->export());
    }

    public static function get_by_email(string $email): User
    {
        $bindings = ['email' => $email];
        $bean = R::findOne(self::TABLE_NAME, 'email = :email ', $bindings);

        if(is_null($bean))
        {
            return new User();
        }
        return (new User())->setData($bean->export());
    }


    /**
     * @return User[]|null[]
     */
    public static function getAll(): array
    {
        $users = R::findAll(self::TABLE_NAME);
        if (count($users) <= 0)
        {
            return [];
        }
        return array_map(function (object $bean): object {
            return (new User())->setData($bean->export());
        }, $users);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): User
    {
        $bean = self::_find($uuid);

        if (is_null($bean))
        {
            return new User();
        }

        $entity = (new User())->setData($bean->export());
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
    public static function update(User $entity): User
    {
        $bean = self::_find($entity->getUuid());

        // If the user exists, update it
        if (is_null($bean)) {
            return new User();
        }

        $name = $entity->getName();
        $status = $entity->getStatus();
        $default = $entity->getDefault();


        if ($name) {
            $bean->name = $name;
        }

        if ($status) {
            $bean->status = $status;
        }

        if ($default) {
            $bean->default = $default;
        }

        // save the user
        $id = R::store($bean);

        if(gettype($id) === 'integer' || gettype($id) === 'string')
        {
            return $entity->setId($id);
        }

        return new User();
    }
}