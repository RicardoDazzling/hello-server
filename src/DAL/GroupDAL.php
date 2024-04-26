<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Group;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class GroupDAL
{
    public const TABLE_NAME = 'groups';

    /**
     * @throws SQL
     */
    public static function create(Group $entity): Group
    {
        $bean = R::dispense(self::TABLE_NAME);
        $bean->uuid = $entity->getUuid();
        $bean->photo = $entity->getPhoto();
        $bean->name = $entity->getName();
        $bean->description = $entity->getDescription();
        $bean->creation = $entity->getCreation();

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        return new Group();
    }

    private static function _find(string $uuid): NULL|\RedBeanPHP\OODBBean
    {
        $bindings = ['uuid' => $uuid];
        return R::findOne(self::TABLE_NAME, 'uuid = :uuid ', $bindings);
    }

    public static function get(string $uuid): Group
    {
        $bean = self::_find($uuid);

        if(is_null($bean)) return new Group();
        return (new Group())->setData($bean->export());
    }

    /**
     * @return Group[]|null[]
     */
    public static function getSelection(array $uuid_list): array
    {
        $groups = R::findAll(self::TABLE_NAME, "uuid IN ('" . join("', '", $uuid_list) . "')");
        if (count($groups) <= 0) return [];
        return array_map(function (object $bean): object {
            return (new Group())->setData($bean->export());
        }, $groups);
    }

    /**
     * @return Group[]|null[]
     */
    public static function getAll(): array
    {
        $groups = R::findAll(self::TABLE_NAME);
        if (count($groups) <= 0) return [];
        return array_map(function (object $bean): object {
            return (new Group())->setData($bean->export());
        }, $groups);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): Group
    {
        $bean = self::_find($uuid);

        if (is_null($bean)) return new Group();

        $entity = (new Group())->setData($bean->export());
        $works = (bool)R::trash($bean);

        if ($works) return $entity;
        else throw new SQL('Remove error!');
    }

    /**
     * @throws SQL
     */
    public static function update(Group $entity): Group
    {
        $bean = self::_find($entity->getUuid());

        if (is_null($bean)) return new Group();

        $name = $entity->getName();
        $photo = $entity->getPhoto();
        $description = $entity->getDescription();

        if (!is_null($name)) $bean->name = $name;
        if (!is_null($photo)) $bean->photo = $photo;
        if (!is_null($description)) $bean->description = $description;

        $id = R::store($bean);

        if(gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        else return new Group();
    }
}