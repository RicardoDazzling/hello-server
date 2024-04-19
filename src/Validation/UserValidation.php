<?php

namespace DazzRick\HelloServer\Validation;

use Respect\Validation\Validator as v;
use DazzRick\HelloServer\Exceptions\ValidationException;

final class UserValidation implements Validate
{
    private const MIN = 2;
    private const MAX = 80;

    public static function isCreationSchemaValid(array $data, bool $mandatory = true): bool
    {
        // validation schema
        if (array_key_exists('name', $data))
        {
            if (is_string($data['name']))
                if(!(self::MIN < strlen($data['name']) && strlen($data['name']) < self::MAX)) throw new ValidationException(
                    sprintf('Name is out of range (%d, %d).', self::MIN, self::MAX));
        }
        else if ($mandatory) throw new ValidationException('Name is empty.');

        // validation schema
        if (array_key_exists('email', $data))
        {
            if (!v::email()->validate($data['email'])) throw new ValidationException('Invalid Email.');
        }
        else if ($mandatory) throw new ValidationException('Email is empty.');

        return true;
    }

    public static function isUpdateSchemaValid(array $data): bool
    {
        return self::isCreationSchemaValid($data, false);
    }
}