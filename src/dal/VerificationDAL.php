<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Verification;
use DazzRick\HelloServer\Exceptions\ValidationException;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class VerificationDAL
{
    public const TABLE_NAME = 'verifications';

    /**
     * @throws SQL
     */
    public static function create(Verification $entity): Verification
    {
        if(!self::get($entity->getUuid())->isEmpty()) throw new ValidationException("UUID already exists.");
        $bean = R::dispense(self::TABLE_NAME);
        $bean->uuid = $entity->getUuid();
        $bean->code = $entity->getCode();
        $bean->last_try = $entity->getLastTry();
        $bean->try_number = $entity->getTryNumber();

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        else return new Verification();
    }

    private static function _find(string $uuid): NULL|\RedBeanPHP\OODBBean
    {
        $bindings = ['uuid' => $uuid];
        return R::findOne(self::TABLE_NAME, 'uuid = :uuid ', $bindings);
    }

    public static function get(string $uuid): Verification
    {
        $bean = self::_find($uuid);

        if(is_null($bean))
        {
            return new Verification();
        }
        return (new Verification())->setData($bean->export());
    }


    /**
     * @return Verification[]|null[]
     */
    public static function getAll(): array
    {
        $verifications = R::findAll(self::TABLE_NAME);
        if (count($verifications) <= 0)
        {
            return [];
        }
        return array_map(function (object $bean): object {
            return (new Verification())->setData($bean->export());
        }, $verifications);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Verification
    {
        $bean = self::_find($uuid);

        if (is_null($bean))
        {
            return new Verification();
        }

        $entity = (new Verification())->setData($bean->export());
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
    public static function update(Verification $entity): Verification
    {
        $bean = self::_find($entity->getUuid());

        // If the Verification exists, update it
        if (is_null($bean)) {
            return new Verification();
        }

        $code = $entity->getCode();
        $last_try = $entity->getLastTry();
        $try_number = $entity->getTryNumber();


        if (!is_null($code)) {
            $bean->name = $code;
        }

        if (!is_null($last_try)) {
            $bean->last_try = $last_try;
        }

        if (!is_null($try_number)) {
            $bean->try_number = $try_number;
        }

        // save the Verification
        $id = R::store($bean);

        if(gettype($id) === 'integer' || gettype($id) === 'string')
        {
            return $entity->setId($id);
        }

        return new Verification();
    }
}