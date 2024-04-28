<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\DAL\GroupDAL;
use DazzRick\HelloServer\DAL\MessageDAL;
use DazzRick\HelloServer\DAL\ParticipantDAL;
use DazzRick\HelloServer\DAL\UserDAL;
use DazzRick\HelloServer\Entity\Participant;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait ParticipantValidation
{
    public static function isCreationSchemaValid(array $data, bool $mandatory = true, bool $update = false): array
    {
        if (array_key_exists('group', $data))
        {
            if (is_string($data['group']))
            {
                if (!(v::uuid()->validate($data['group'])))
                    throw new ValidationException('Group UUID is invalid.');
                $group = GroupDAL::get($data['group']);
                if ($group->isEmpty())
                    throw new ValidationException("Group doesn't exist.");
            } else throw new ValidationException('Group need to be a string.');
        }
        else if ($mandatory) throw new ValidationException('Group is empty.');

        global $jwt;
        $participant_from_session = ParticipantDAL::get($jwt->getUuid(), $data['group']);

        if (!$update) {
            if (array_key_exists('user', $data)) {
                if (is_string($data['user'])) {
                    if (v::email()->validate($data['user'])) {
                        $user = UserDAL::get_by_email($data['user']);
                        if ($user->isEmpty())
                            throw new ValidationException("User doesn't exist.");
                        else $data['user'] = $user->getUuid();
                    } else if (!(v::uuid()->validate($data['user']) && $data['user'] === $jwt->getUuid()))
                        throw new ValidationException('User is invalid.');
                } else throw new ValidationException('User need to be a string.');
            } else $data['user'] = $jwt->getUuid();
        }

        if (array_key_exists('is_active', $data)) {
            if ($participant_from_session->isEmpty())
                throw new UnAuthorizedException('Such inviter, you can not made yourself active.');
            if (!$participant_from_session->isAdmin())
                throw new UnAuthorizedException('You are not a administrator from this group.');
            if (!v::boolType()->validate($data['is_active'])) throw new ValidationException('Invalid is_active bool.');
        } else $data['is_active'] = false;

        if (array_key_exists('is_admin', $data)) {
            if ($participant_from_session->isEmpty())
                throw new UnAuthorizedException('Such inviter, you can not made yourself admin.');
            if (!$participant_from_session->isSuper()) throw new ValidationException('Only the super user can elevate to admin.');
            if (!v::boolType()->validate($data['is_admin'])) throw new ValidationException('Invalid is_admin bool.');
        } else $data['is_admin'] = false;

        if (array_key_exists('is_super', $data))
            throw new ValidationException('The super user can not be set by users.');
        else $data['is_super'] = false;

        if (array_key_exists('last_received', $data)) {
            if (is_string($data['last_received'])) {
                if (!(v::uuid()->validate($data['last_received'])))
                    throw new ValidationException('Last received UUID is invalid.');
                if (MessageDAL::get($data['last_received'])->isEmpty())
                    throw new ValidationException("Last received message doesn't exist.");
            } else throw new ValidationException('Last received is not a string.');
            throw new ValidationException("Last received message only is set by system.");
        }

        if (array_key_exists('last_read', $data)) {
            if ($participant_from_session->isEmpty())
                throw new UnAuthorizedException('Such inviter, you can not update yourself last_read.');
            if (!$participant_from_session->isActive())
                throw new UnAuthorizedException('You are not a active participant from this group.');
            if (is_string($data['last_read'])) {
                if (!(v::uuid()->validate($data['last_read'])))
                    throw new ValidationException('Last read UUID is invalid.');
                if (MessageDAL::get($data['last_read'])->isEmpty())
                    throw new ValidationException("Last read message doesn't exist.");
            } else throw new ValidationException('Last received is not a string.');
            if (!$update)throw new ValidationException('Last read message can only be updated before the creation');
        }

        return $data;
    }

    public static function isUpdateSchemaValid(array $data): Participant
    {
        $is_itself = ($data['user'] === $GLOBALS['jwt']->getUuid());
        $old_entity = ParticipantDAL::get($data['user'], $data['group']);
        if ($old_entity->isEmpty())
            throw new ValidationException("Unknown participant.");
        if ($is_itself && !$old_entity->isActive())
            throw new ValidationException("You can't update yourself while your inactive.");
        self::isCreationSchemaValid($data, update: true);

        if ($old_entity->isEmpty())
            throw new ValidationException("Participant doesn't exist.");

        $exist = function (string $key) use ($data): bool {return array_key_exists($key, $data);};

        if($old_entity->isSuper() && !$is_itself)
            throw new ValidationException('Only the super user can update its own data.');
        if(!$is_itself && $exist('last_read'))
            throw new ValidationException('Only the user it self can update the last read.');

        return $old_entity;
    }
}