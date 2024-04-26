<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\DAL\GroupDAL;
use DazzRick\HelloServer\DAL\MessageDAL;
use DazzRick\HelloServer\DAL\ParticipantDAL;
use DazzRick\HelloServer\DAL\UserDAL;
use DazzRick\HelloServer\Entity\Participant;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait ParticipantValidation
{
    public static function isCreationSchemaValid(array $data, bool $mandatory = true): array
    {
        if (array_key_exists('user', $data))
        {
            if (is_string($data['user']))
            {
                if (!(v::uuid()->validate($data['user'])))
                    throw new ValidationException('User UUID is invalid.');
                if (UserDAL::get($data['user'])->isEmpty())
                    throw new ValidationException("User doesn't exist.");
            }
        }
        else if ($mandatory) throw new ValidationException('User is empty.');

        if (array_key_exists('group', $data))
        {
            if (is_string($data['group']))
            {
                if (!(v::uuid()->validate($data['group'])))
                    throw new ValidationException('Group UUID is invalid.');
                if (GroupDAL::get($data['group'])->isEmpty())
                    throw new ValidationException("Group doesn't exist.");
            }
        }
        else if ($mandatory) throw new ValidationException('Group is empty.');

        if (array_key_exists('is_active', $data)) {
            if (!v::boolType()->validate($data['is_active'])) throw new ValidationException('Invalid is_active bool.');
        } else $data['is_active'] = false;

        if (array_key_exists('is_admin', $data)) {
            if (!v::boolType()->validate($data['is_admin'])) throw new ValidationException('Invalid is_admin bool.');
        } else $data['is_admin'] = false;

        if (array_key_exists('is_super', $data))
            throw new ValidationException('The super user can not be set by users.');

        if (array_key_exists('last_received', $data))
            if (is_string($data['last_received']))
            {
                if (!(v::uuid()->validate($data['last_received'])))
                    throw new ValidationException('Last received UUID is invalid.');
                if (MessageDAL::get($data['last_received'])->isEmpty())
                    throw new ValidationException("Last received message doesn't exist.");
            }

        if (array_key_exists('last_read', $data))
            if (is_string($data['last_read']))
            {
                if (!(v::uuid()->validate($data['last_read'])))
                    throw new ValidationException('Last read UUID is invalid.');
                if (MessageDAL::get($data['last_read'])->isEmpty())
                    throw new ValidationException("LAst read message doesn't exist.");
            }

        return $data;
    }

    public static function isUpdateSchemaValid(array $data): Participant
    {
        global $jwt;
        self::isCreationSchemaValid($data);

        $old_entity = ParticipantDAL::get($data['user'], $data['group']);
        if ($old_entity->isEmpty())
            throw new ValidationException("Participant doesn't exist.");

        $exist = function (string $key) use ($data): bool {return array_key_exists($key, $data);};
        $is_itself = $data['user'] === $jwt->getUuid();

        if($old_entity->isSuper() && !$is_itself)
            throw new ValidationException('Only the super user can update its own data.');
        if(!$is_itself && ($exist('last_received') || $exist('last_read')))
            throw new ValidationException('Only the user it self can update the last received and read.');
        if($exist('is_super'))
            throw new ValidationException('The super user statement can not be updated.');
        if(!$old_entity->isAdmin() && ($exist('is_active') || $exist('is_admin')))
            throw new ValidationException('Only administrators can update participant status.');

        return $old_entity;
    }
}