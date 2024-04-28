<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\DAL\ParticipantDAL;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait GroupValidation
{
    private const MAX = 65_535; // MariaDB Max Text Size.

    public static function userVerification(string $group, ?bool $admin = false, ?bool $super = false): void
    {
        $participant = ParticipantDAL::get($GLOBALS['jwt']->getUuid(), $group);
        if($participant->isEmpty())
            throw new UnAuthorizedException('Participant not exist!');
        if(!$participant->isActive())
            throw new UnAuthorizedException('User not active!');
        if(!$participant->isAdmin() && $admin)
            throw new UnAuthorizedException('Admin level required!');
        if(!$participant->isSuper() && $super)
            throw new UnAuthorizedException('Super user level required!');
    }

    public static function isCreationSchemaValid(array $data, bool $mandatory=true): bool
    {
        if(array_key_exists('photo', $data))
            if(!v::base64()->validate(explode(',', trim($data['photo']))[1]))
                throw new ValidationException('The group photo are not a base64.');

        UserValidation::nameValidation($data, $mandatory);

        if (array_key_exists('description', $data))
            if (is_string($data['description'])) {
                if (!(strlen($data['name']) < self::MAX))
                    throw new ValidationException(
                        sprintf('Description have more than %d characters.', self::MAX));
            } else throw new ValidationException('Description need to be a string.');

        return true;
    }

    public static function isUpdateSchemaValid(array $data): bool
    {
        return self::isCreationSchemaValid($data, false);
    }
}