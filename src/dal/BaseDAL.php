<?php

namespace DazzRick\HelloServer\DAL;

use DazzRick\HelloServer\Entity\Call;
use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Entity\Lost;
use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Entity\Writing;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use RedBeanPHP\R;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;

class BaseDAL
{
    public const TABLE_NAME = '';
    public const COLUMNS = [];
    public const ALLOW_UPDATE_COLUMNS = [];
    public const TIME = 'month'; # month === 43200m ; week === 10080m

    /**
     * @param array $data
     * @return Message|File|Call|Lost|Writing
     */
    public static function populatedEntity(array $data): mixed { return static::emptyEntity()->setData($data); }

    /**
     * @return Message|File|Call|Lost|Writing
     */
    public static function emptyEntity(): mixed { return new Message(); }

    /**
     * @param Message|File|Call|Lost|Writing $entity
     * @return Message|File|Call|Lost|Writing
     * @throws SQL|RedException
     */
    protected static function _create(mixed $entity): mixed
    {
        $bean = R::dispense(static::TABLE_NAME);
        foreach (static::COLUMNS as $column) $bean->__set($column, $entity->__get($column));

        $id = R::store($bean);

        R::close();

        if (gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        else return static::emptyEntity();
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

    /**
     * @param string $uuid
     * @return Message|File|Call|Lost|Writing
     */
    protected static function _get(string $uuid): mixed
    {
        $bean = static::_find($uuid);

        if(is_null($bean)) return static::emptyEntity();
        else return static::populatedEntity($bean);
    }

    /**
     * @return Message[]|File[]|Call[]|Lost[]|Writing[]|array<void>
     */
    protected static function _getAll(string $to): array
    {
        $table_name = static::TABLE_NAME;
        $users_table_name = UserDAL::TABLE_NAME;
        $classification = 'to_uuid';
        $filter = $_REQUEST['filter'] ?? null;
        $bigger = $_REQUEST['bigger'] ?? null;
        $filter_string = '';
        if(!empty($filter)) {
            $table_names = [MessageDAL::TABLE_NAME, FileDAL::TABLE_NAME];
            $filter_lists = [['sent', 'received', 'read'], ['sent', 'received', 'read', 'opened']];
            if (!in_array(static::TABLE_NAME, $table_names))
                throw new BadRequestException("This resource doesn't accept filters.");
            for ($i = 0; $i < count($table_names); $i++) {
                if (static::TABLE_NAME === $table_names[$i]) {
                    if (!in_array($filter, $filter_lists[$i]))
                        throw new BadRequestException('Unknown filter.');
                    $max_state = false;
                    foreach ($filter_lists[$i] as $_filter) {
                        $filter_string .=" AND `$_filter` ";
                        if ($_filter === $filter && !is_null($bigger))
                            $filter_string .= ">= $bigger";
                        else
                            $filter_string .= $max_state ? "IS NULL" : "IS NOT NULL";
                        $max_state = $max_state || $_filter === $filter;
                    }
                }
            }
            if ($filter !== 'sent')
                $classification = 'from_uuid';
        }
        else if (!empty($bigger))
            $filter_string .= "AND sent >= $bigger";
        $bindings = ['uuid' => $to];
        $query = preg_replace('/\s+/', ' ',
            "SELECT m.*,
                f.email AS from_email,
                t.email AS to_email
            FROM $table_name m
                INNER JOIN $users_table_name f ON m.from_uuid = f.uuid
                INNER JOIN $users_table_name t ON m.to_uuid = t.uuid
            WHERE m.$classification = :uuid
            $filter_string
            ORDER BY m.id");
        $messages = R::getAll($query, $bindings);
        $lambda = function (array $bean): object { return static::populatedEntity($bean); };
        if (count($messages) <= 0) return [];
        else return array_map($lambda, $messages);
    }

    /**
     * @return Message|File|Call|Lost|Writing
     * @throws SQL
     */
    protected static function _remove(string $uuid): mixed
    {
        global $jwt;
        $bean = static::_find($uuid);

        if (is_null($bean)) return static::emptyEntity();

        $entity = static::populatedEntity($bean);
        if ($jwt->getUuid() !== $entity->getFromUuid())
            throw new UnAuthorizedException("Entities can only be removed by sender.");
        $table_name = static::TABLE_NAME;
        $id = $bean['id'];
        $works = (bool)R::exec("DELETE FROM $table_name WHERE id=$id;");

        if ($works) return $entity;
        else throw new SQL('Remove error!');
    }

    /**
     * @param Message|File|Call|Lost|Writing $entity
     * @return Message|File|Call|Lost|Writing
     */
    protected static function _update(mixed $entity): mixed
    {
        $uuid = $entity->getUuid();
        $bean = static::_find($uuid);

        // If the user exists, update it
        if (is_null($bean)) return static::emptyEntity();

        $set_list = "";
        foreach (static::ALLOW_UPDATE_COLUMNS as $column) {
            $value = $entity->__get($column);
            if(!empty($value)) $set_list .= gettype($value)===gettype("string")?"$column='$value', ":"$column=$value, ";
        }
        $set_list = rtrim($set_list, ', ');

        // save the user
        $table_name = static::TABLE_NAME;
        $id = R::exec("UPDATE $table_name SET $set_list WHERE uuid='$uuid'");

        if(gettype($id) === 'integer' || gettype($id) === 'string') return $entity->setId($id);
        else return static::emptyEntity();
    }

    public static function clean(): void
    {
        $time = intdiv(time(), 60);
        $time -= static::TIME === 'month' ? 43200 : 10080;
        $bindings = ['time' => $time];
        $will_remove_array = R::findAll(static::TABLE_NAME, 'sent < :time ', $bindings);
        if(count($will_remove_array) > 0) foreach ($will_remove_array as $will_remove) R::trash($will_remove);
    }
}