<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Participant;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class ParticipantDAL
{
    public const TABLE_NAME = 'participants';

    /**
     * @throws SQL
     */
    public static function create(Participant $entity): Participant
    {
        if (!self::get($entity->getUser(), $entity->getGroup())->isEmpty())
            throw new BadRequestException('Participant already exist.');
        $bean = R::dispense(self::TABLE_NAME);
        $bean->user = $entity->getUser();
        $bean->group = $entity->getGroup();
        $bean->is_active = $entity->isActive();
        $bean->is_admin = $entity->isAdmin();
        $bean->is_super = $entity->isSuper();
        $bean->last_received = $entity->getLastReceived();
        $bean->last_read = $entity->getLastRead();

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        return new Participant();
    }

    private static function _find(string $user, string $group): NULL|\RedBeanPHP\OODBBean
    {
        $bindings = ['user' => $user, 'group' => $group];
        return R::findOne(self::TABLE_NAME, '`user` = :user AND `group` = :group ', $bindings);
    }

    public static function get(string $user, string $group): Participant
    {
        $bean = self::_find($user, $group);

        if(is_null($bean)) return new Participant();
        return (new Participant())->setData($bean->export());
    }


    /**
     * @return Participant[]|null[]
     */
    public static function getAll(?string $user = null, ?string $group = null): array
    {
        $filter = empty($_REQUEST['filter']) ? null : $_REQUEST['filter'];
        if (is_null($filter))
            $filter_string = '';
        else if (!in_array($filter, ['is_active', 'is_admin', 'is_super']))
            throw new BadRequestException("Unknown filter: '$filter'");
        else $filter_string = "AND `$filter` = true";
        if (!is_null($user) && !is_null($group))
            throw new BadRequestException('Only group or user can be searched.');
        else if (!is_null($group))
            $participants = R::findAll(self::TABLE_NAME, "`group` = '$group' $filter_string");
        else if(!is_null($user))
            $participants = R::findAll(self::TABLE_NAME, "`user` = '$user' $filter_string");
        else throw new BadRequestException('Group and User are null.');

        if (count($participants) <= 0) return [];
        return array_map(function (object $bean): object {
            return (new Participant())->setData($bean->export());
        }, $participants);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $user, string $group): Participant
    {
        $bean = self::_find($user, $group);

        if (is_null($bean)) return new Participant();

        $entity = (new Participant())->setData($bean->export());
        $works = (bool)R::trash($bean);

        if ($works) return $entity;
        else throw new SQL('Remove error!');
    }

    /**
     * @throws SQL
     */
    public static function update(Participant $entity): Participant
    {
        $bean = self::_find($entity->getUser(), $entity->getGroup());

        if (is_null($bean)) return new Participant();

        $is_active = $entity->isActive();
        $is_admin = $entity->isAdmin();
        $last_received = $entity->getLastReceived();
        $last_read = $entity->getLastRead();

        if (!is_null($is_active)) $bean->is_active = $is_active;
        if (!is_null($is_admin)) $bean->is_admin = $is_admin;
        if (!is_null($last_received)) $bean->last_received = $last_received;
        if (!is_null($last_read)) $bean->last_read = $last_read;

        $id = R::store($bean);

        if(gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        else return new Participant();
    }

    /**
     * @throws SQL
     */
    public static function update_super(string $group): void
    {
        $bindings = ['group' => $group];
        $new_super = R::findOne(self::TABLE_NAME, '`group` = :group AND `is_admin` = true', $bindings);
        if (is_null($new_super))
            $new_super = R::findOne(self::TABLE_NAME, '`group` = :group AND `is_active` = true', $bindings);
        if (is_null($new_super)) {
            GroupDAL::remove($group);
            self::remove_all_from_group($group);
            return;
        }
        $new_super->is_admin = true;
        $new_super->is_super = true;
        R::store($new_super);
    }

    public static function remove_all_from_group($group): void
    {

        $query = preg_replace('/\s+/', ' ', "
            DELETE FROM " . self::TABLE_NAME . "
            WHERE `group`='" . $group . "'
        ");
        try {
            R::exec($query);
        } catch (\Exception $e) {
            $message = "Failed to remove participants! SQL Query: '" . $query . "';\n SQL exception: '" . $e->getMessage() . "'";
            throw new InternalServerException($message);
        }
    }

    /**
     * @return String[]
     */
    public static function retrieve_all_groups_from_user(): array
    {
        global $jwt;
        $query = preg_replace('/\s+/', ' ', "
            SELECT `group`
            FROM " . self::TABLE_NAME . "
            WHERE `user`='" . $jwt->getUuid() . "'
        ");
        $groups = R::getAll($query);
        return array_map(function (array $data) { return $data['group']; }, $groups);
    }
}