<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\GFile;
use DazzRick\HelloServer\Entity\GMessage;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class GBaseDAL
{
    public const TABLE_NAME = '';

    public static function new_instance(): GFile|GMessage { return static::TABLE_NAME === 'gmessage' ? new GMessage(): new GFile();}

    /**
     * @throws SQL
     */
    public static function create(GFile|GMessage $entity): GFile|GMessage
    {
        $bean = R::dispense(self::TABLE_NAME);
        $bean->uuid = $entity->getUuid();
        $bean->from_uuid = $entity->getFromUuid();
        $bean->to_uuid = $entity->getToUuid();
        $bean->content = $entity->getContent();
        $bean->sent = $entity->getSent();

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        return $entity->new_instance();
    }

    private static function _find(string $uuid): NULL|array
    {
        $table_name = static::TABLE_NAME;
        $users_table_name = UserDAL::TABLE_NAME;
        $bindings = ['uuid' => $uuid];
        $get = R::getRow(
            "SELECT m.*,
                        f.email AS from_email,
                        t.email AS to_email
                 FROM $table_name m
                     INNER JOIN $users_table_name f ON m.from_uuid = f.uuid
                     INNER JOIN $users_table_name t ON m.to_uuid = t.uuid
                 WHERE m.uuid = :uuid", $bindings);
        if (!empty($get)) return $get;
        else return NULL;
    }

    public static function get(string $uuid): GFile|GMessage
    {
        $bean = self::_find($uuid);

        if(is_null($bean)) return static::new_instance();
        return static::new_instance()->setData($bean);
    }

    /**
     * @param string $to
     * @return GMessage[]|GFile[]|null[]
     */
    public static function getAll(string $to): array
    {
        $table_name = static::TABLE_NAME;
        $users_table_name = UserDAL::TABLE_NAME;
        $groups_table_name = GroupDAL::TABLE_NAME;
        $participants_table_name = ParticipantDAL::TABLE_NAME;
        $query = preg_replace('/\s+/', ' ', "
            SELECT m.*,
                f.email AS from_email,
            FROM $participants_table_name p
                INNER JOIN $groups_table_name g ON p.group = g.uuid
                INNER JOIN $table_name m ON p.group = m.to_uuid
                INNER JOIN $users_table_name f ON m.from_uuid = f.uuid
            WHERE p.user = '$to' AND p.is_active = true
            ORDER BY m.id");
        $messages = R::getAll($query);
        $lambda = function (array $bean): object { return static::new_instance()->setData($bean); };
        if (count($messages) <= 0) return [];
        else return array_map($lambda, $messages);
    }

    /**
     * @throws SQL
     */
    public static function remove(string $uuid): GFile|GMessage
    {
        $bean = static::_find($uuid);

        $new_instance = static::new_instance();
        if (is_null($bean)) return $new_instance;

        $entity = $new_instance->setData($bean);
        if ($GLOBALS['jwt']->getUuid() !== $entity->getFromUuid())
            throw new UnAuthorizedException("Entities can only be removed by sender.");
        $table_name = static::TABLE_NAME;
        $id = $bean['id'];
        $works = (bool)R::exec("DELETE FROM $table_name WHERE id=$id;");

        if ($works) return $entity;
        else throw new SQL('Remove error!');
    }

    /**
     * @throws SQL
     */
    public static function update(GMessage|GFile $entity): GMessage|GFile
    {
        $uuid = $entity->getUuid();
        $bean = static::_find($uuid);

        // If the user exists, update it
        if (is_null($bean)) return static::new_instance();

        $content = $entity->getContent();
        if(empty($content)) return static::new_instance();

        // save the user
        $table_name = static::TABLE_NAME;
        $id = R::exec("UPDATE $table_name SET `content`=$content WHERE uuid='$uuid'");

        if(gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        else return static::emptyEntity();
    }

    public static function received(): void
    {
        $to = $GLOBALS['jwt']->getUuid();
        $participants_table_name = ParticipantDAL::TABLE_NAME;
        $received = intdiv(time(), 60);

        R::exec("UPDATE $participants_table_name SET last_received=$received WHERE `user`='$to' AND is_active=true");
    }

    public static function clean(): void
    {
        $time = intdiv(time(), 60);
        $time -= static::TABLE_NAME === GFileDAL::TABLE_NAME ? 10080 : 43200;
        $bindings = ['time' => $time];
        $will_remove_array = R::findAll(static::TABLE_NAME, 'sent < :time ', $bindings);
        if(count($will_remove_array) > 0) foreach ($will_remove_array as $will_remove) R::trash($will_remove);
    }
}