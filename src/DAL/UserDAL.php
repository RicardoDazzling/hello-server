<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\User;
use DazzRick\HelloServer\Exceptions\ValidationException;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

final class UserDAL
{
    public const TABLE_NAME = 'users';

    /**
     * @throws SQL
     */
    public static function create(User $entity): User
    {
        if(!self::get_by_email($entity->getEmail())->isEmpty()) throw new ValidationException("EMail already exists.");
        $bean = R::dispense(self::TABLE_NAME);
        $bean->uuid = $entity->getUuid();
        $bean->online = $entity->getOnline();
        $bean->name = $entity->getName();
        $bean->email = $entity->getEmail();
        $bean->creation_date = $entity->getCreationDate();

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
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

        if(is_null($bean)) return new User();
        return (new User())->setData($bean->export());
    }

    public static function get_by_email(string $email): User
    {
        $bindings = ['email' => $email];
        $bean = R::findOne(self::TABLE_NAME, 'email = :email ', $bindings);

        if(is_null($bean)) return new User();
        return (new User())->setData($bean->export());
    }

    /**
     * @return User[]|null[]
     */
    public static function getAll(): array
    {
        $users = R::findAll(self::TABLE_NAME);
        if (count($users) <= 0) return [];
        return array_map(function (object $bean): object {
            return (new User())->setData($bean->export());
        }, $users);
    }

    /**
     * Get the email list from a list of UUID's
     * @param array $uuid_list
     * @return string[]|null[]
     */
    public static function get_email_from_uuid(array $uuid_list): array
    {
        $query = preg_replace( '/\s+/', ' ',  "
            SELECT `uuid`, `email`
            FROM " . self::TABLE_NAME . "
            WHERE
                `uuid` IN ('" . join("', '", $uuid_list) . "')
        ");
        $email_list = R::getAll($query);
        $new_array = [];
        for ($i = 0; $i < count($email_list); $i++)
            $new_array[$email_list[$i]['uuid']] = $email_list[$i]['email'];
        return $new_array;
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): User
    {
        $bean = self::_find($uuid);

        if (is_null($bean)) return new User();

        $entity = (new User())->setData($bean->export());
        $works = (bool)R::trash($bean);

        if ($works) return $entity;
        else throw new SQL('Remove error!');
    }

    /**
     * @throws SQL
     */
    public static function update(User $entity): User
    {
        $bean = self::_find($entity->getUuid());

        // If the user exists, update it
        if (is_null($bean))  return new User();

        $name = $entity->getName();
        $online = $entity->getOnline();

        if (!is_null($name)) $bean->name = $name;

        if (!is_null($online)) $bean->online = $online;

        // save the user
        $id = R::store($bean);

        if(gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        else return new User();
    }

    public static function online(array $email_list): array
    {
        $table_name = self::TABLE_NAME;
        $email_list_string = str_replace(['{', '[', '}', ']', '"'], ['(', '(', ')', ')', '\''], json_encode($email_list));
        $list = R::getAll("SELECT email FROM $table_name WHERE email IN $email_list_string AND online=true");
        $list = array_map(function (array $data) { return $data['email']; }, $list);
        $new_email_list = [];
        foreach ($email_list as $email) $new_email_list[$email] = in_array($email, $list);
        return $new_email_list;
    }
}