<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait GroupValidation
{
    private const MAX = 65_535; // MariaDB Max Text Size.

    public static function isCreationSchemaValid(array $data, bool $mandatory=true): bool
    {
        if(array_key_exists('photo', $data))
            if(!v::base64()->validate($data['photo']))
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