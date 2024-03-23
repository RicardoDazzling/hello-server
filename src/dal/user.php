<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\User;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

final class UserDAL
{
    public const string TABLE_NAME = 'users';

    /**
     * @throws SQL
     */
    public static function create(User $userEntity): int|string|false
    {
        $bean = R::dispense(self::TABLE_NAME);
        $bean->uuid = $userEntity->getUuid();
        $bean->status = $userEntity->getStatus();
        $bean->target = $userEntity->getTarget();
        $bean->name = $userEntity->getName();
        $bean->email = $userEntity->getEmail();
        $bean->phone = $userEntity->getPhone();
        $bean->default = $userEntity->getDefault();
        $bean->created_date = $userEntity->getCreationDate();

        $id = R::store($bean);

        R::close();

        return $id;
    }
    
    private static function _find(string $uuid): NULL|\RedBeanPHP\OODBBean
    {
        $bindings = ['uuid' => $uuid];
        return R::findOne(self::TABLE_NAME, 'uuid = :uuid ', $bindings);
    }

    public static function get(string $uuid): ?array
    {
        $bean = self::_find($uuid);

        return $bean?->export();
    }

    public static function getAll(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function remove(string $uuid): ?bool
    {
        $bean = self::_find($uuid);

        if ($bean) {
            return (bool)R::trash($bean);
        }

        return null;
    }

    public static function update(string $uuid, User $user): int|string|false
    {
        $userBean = self::_find($uuid);

        // If the user exists, update it
        if ($userBean) {
            $name = $user->getName();
            $status = $user->getStatus();
            $target = $user->getTarget();
            $default = $user->getDefault();


            if ($name) {
                $userBean->name = $name;
            }

            if ($status) {
                $userBean->status = $status;
            }

            if ($target) {
                $userBean->target = $target;
            }

            if ($default) {
                $userBean->default = $default;
            }

            // save the user
            return R::store($userBean);
        }

        return 0;
    }
}